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

    public function ask(string $systemPrompt, string $userMessage, array $conversationHistory = []): string
    {
        $messages = [];

        // Add conversation history (last N turns)
        foreach ($conversationHistory as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content'],
            ];
        }

        // Add current user message
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ])->timeout(60)->post('https://api.anthropic.com/v1/messages', [
            'model' => $this->model,
            'max_tokens' => 1024,
            'system' => $systemPrompt,
            'messages' => $messages,
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

    /**
     * Send a message with tool definitions and return the full response.
     *
     * @param  array<int, array{role: string, content: mixed}>  $messages
     * @param  array<int, array{name: string, description: string, input_schema: array<string, mixed>}>  $tools
     * @return array{content: array<int, mixed>, stop_reason: string}
     */
    public function askWithTools(string $systemPrompt, array $messages, array $tools): array
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ])->timeout(120)->post('https://api.anthropic.com/v1/messages', [
            'model' => $this->model,
            'max_tokens' => 4096,
            'system' => $systemPrompt,
            'messages' => $messages,
            'tools' => $tools,
        ]);

        if ($response->failed()) {
            Log::error('Anthropic API error (tool_use)', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Failed to get response from Anthropic.');
        }

        $data = $response->json();

        return [
            'content' => $data['content'] ?? [],
            'stop_reason' => $data['stop_reason'] ?? 'end_turn',
        ];
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
