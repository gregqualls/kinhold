<?php

namespace App\Services\AiProviders;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnthropicProvider implements AiProviderInterface
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
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ])->timeout(60)->post('https://api.anthropic.com/v1/messages', [
            'model' => $this->model,
            'max_tokens' => 1024,
            'system' => $systemPrompt,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $userMessage,
                ],
            ],
        ]);

        if ($response->failed()) {
            Log::error('Anthropic API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Failed to get response from Anthropic.');
        }

        $data = $response->json();

        return $data['content'][0]['text'] ?? 'Sorry, I could not generate a response.';
    }

    public static function displayName(): string
    {
        return 'Anthropic Claude';
    }

    public static function defaultModel(): string
    {
        return config('services.ai_providers.anthropic.default_model', 'claude-sonnet-4-5-20250514');
    }
}
