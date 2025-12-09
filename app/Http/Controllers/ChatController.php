<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->middleware('auth');
        $this->geminiService = $geminiService;
    }

    public function index()
    {
        $user = Auth::user();
        $apiToken = $user->createToken('chat-app')->plainTextToken;

        // Start with a new chat by default
        $conversations = \App\Models\Conversation::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->get();
        $currentConversation = null;
        $messages = collect();

        // Pass API token to view
        return view('chat', compact('conversations', 'currentConversation', 'messages', 'apiToken'));
    }

    public function newChat()
    {
        return redirect()->route('chat.index');
    }

    public function loadConversation($uuid)
    {
        $user = Auth::user();
        $apiToken = $user->createToken('chat-app')->plainTextToken;

        $conversation = \App\Models\Conversation::where('user_id', Auth::id())->where('uuid', $uuid)->firstOrFail();
        
        $conversations = \App\Models\Conversation::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->get();
        $messages = $conversation->messages;
        $currentConversation = $conversation;

        return view('chat', compact('conversations', 'currentConversation', 'messages', 'apiToken'));
    }

    public function sendMessage(Request $request)
    {
        \Log::info('SendMessage called', $request->all());
        $request->validate([
            'message' => 'required|string',
            'conversation_id' => 'nullable|exists:conversations,id',
        ]);

        try {
            $user = Auth::user();
            if (! $user) {
                \Log::error('User not authenticated in sendMessage');

                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $messageContent = $request->input('message');
            $conversationId = $request->input('conversation_id');

            // Create new conversation if ID is not provided
            if (! $conversationId) {
                $conversation = \App\Models\Conversation::create([
                    'user_id' => $user->id,
                    'title' => substr($messageContent, 0, 30).'...',
                ]);
                $conversationId = $conversation->id;
            } else {
                $conversation = \App\Models\Conversation::findOrFail($conversationId);
                $conversation->touch(); // Update updated_at timestamp
            }

            $estimatedTokens = $this->geminiService->estimateTokens($messageContent);

            // Check token limit
            if ($user->token_usage + $estimatedTokens > 20000) {
                return response()->json(['error' => 'Token limit exceeded.'], 403);
            }

            // Save user message
            $userMessage = Message::create([
                'user_id' => $user->id,
                'conversation_id' => $conversationId,
                'content' => $messageContent,
                'sender' => 'user',
                'tokens' => $estimatedTokens,
            ]);

            // Update user token usage
            $user->increment('token_usage', $estimatedTokens);

            // Get AI response
            $aiResponseContent = $this->geminiService->generateResponse($messageContent);
            $aiTokens = $this->geminiService->estimateTokens($aiResponseContent);

            // Save AI message
            $botMessage = Message::create([
                'user_id' => $user->id,
                'conversation_id' => $conversationId,
                'content' => $aiResponseContent,
                'sender' => 'bot',
                'tokens' => $aiTokens,
            ]);

            // Update user token usage for AI response
            $user->increment('token_usage', $aiTokens);

            return response()->json([
                'user_message' => $userMessage,
                'bot_message' => $botMessage,
                'conversation_id' => $conversationId,
                'conversation_uuid' => $conversation->uuid,
                'conversation_title' => $conversation->title,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in sendMessage: '.$e->getMessage());

            return response()->json(['error' => 'Internal Server Error: '.$e->getMessage()], 500);
        }
    }
}
