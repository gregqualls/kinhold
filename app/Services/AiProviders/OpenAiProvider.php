<?php

namespace App\Services\AiProviders;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiProvider implements AiProviderInterface
{
    public function __construct(
        private string $apiKey,
        private string $model = '',
    ) {
        $this->model = $model ?: self::defaultModel();
    }

    public function ask(string $systemPrompt, string $userMessage): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
            'model' => $this->model,
            'max_tokens' => 1024,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ],
                [
                    'role' => 'user',
                    'content' => $userMessage,
                ],
            ],
        ]);

        if ($response->failed()) {
            Log::error('OpenAI API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Failed to get response from OpenAI.');
        }

        $data = $response->json();

        return $data['choices'][0]['message']['content'] ?? 'Sorry, I could not generate a response.';
    }

    public static function displayName(): string
    {
        return 'OpenAI';
    }

    public static function defaultModel(): string
    {
        return config('services.ai_providers.openai.default_model', 'gpt-4o');
    }
}
