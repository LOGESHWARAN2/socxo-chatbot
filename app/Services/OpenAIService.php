<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAIService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
    }

    public function generateResponse($prompt)
    {
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 150,
        ]);

        if ($response->successful()) {
            return $response->json()['choices'][0]['message']['content'];
        }

        \Log::error('OpenAI API Error', $response->json());
        return 'Error: ' . ($response->json()['error']['message'] ?? 'Unknown error occurred.');
    }

    public function estimateTokens($text)
    {
        // Simple estimation: 1 token ~= 4 characters
        return ceil(strlen($text) / 4);
    }
}
