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

    public function generateResponse($prompt)
    {
        $url = $this->baseUrl . '?key=' . $this->apiKey;

        $response = Http::withoutVerifying()->withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]);

        if ($response->successful()) {
            return $response->json()['candidates'][0]['content']['parts'][0]['text'];
        }

        \Log::error('Gemini API Error', $response->json());
        return 'Error: ' . ($response->json()['error']['message'] ?? 'Unknown error occurred.');
    }

    public function estimateTokens($text)
    {
        // Simple estimation: 1 token ~= 4 characters
        return ceil(strlen($text) / 4);
    }
}
