<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    public function generateResponse($history, $systemInstruction = null)
    {
        $url = $this->baseUrl . '?key=' . $this->apiKey;
        $payload = [];

        // Add System Instruction if provided (Gemini 1.5/Titan way, but for 1.0/Flash we might inject as first message)
        // Note: For Gemini 1.5 Flash via API, 'system_instruction' is a top-level field.
        if ($systemInstruction) {
            $payload['system_instruction'] = [
                'parts' => [['text' => $systemInstruction]]
            ];
        }

        // If history is just a string (legacy support), wrap it
        if (is_string($history)) {
            $contents = [
                ['parts' => [['text' => $history]]]
            ];
        } else {
            // Assume $history is an array of ['role' => 'user'|'model', 'parts' => [...]]
            $contents = $history;
        }
        $payload['contents'] = $contents;

        $response = Http::withoutVerifying()->withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        if ($response->successful()) {
            return $response->json()['candidates'][0]['content']['parts'][0]['text'];
        }

        \Log::error('Gemini API Error', $response->json());
        return 'Error: ' . ($response->json()['error']['message'] ?? 'Unknown error occurred.');
    }

    public function updateMemory($currentMemory, $userMessage, $botMessage)
    {
        $prompt = "You are a memory manager. Current memory about user: '$currentMemory'. User said: '$userMessage'. Bot said: '$botMessage'. Extract any new personal facts (name, preferences, location, etc.) and merge them with current memory. Return ONLY the updated concise memory summary. If nothing new, return the current memory.";
        
        // Use a separate simple call for this to avoid infinite recursion or context pollution
        return $this->generateResponse($prompt);
    }

    public function estimateTokens($text)
    {
        // Simple estimation: 1 token ~= 4 characters
        return ceil(strlen($text) / 4);
    }
}
