<?php

namespace App\Services\AiProviders;

use Illuminate\Http\Client\Response;
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
     * Uses Anthropic prompt caching on the system prompt and tool definitions
     * (the "static prefix" that's identical across every tool-use turn). After
     * the first turn, those tokens become cache reads — they don't count
     * against the per-minute input-token rate limit. For Kinhold's 7-tool
     * setup that's ~6-8k tokens cached per turn.
     *
     * On HTTP 429 (rate limit), waits the server-suggested retry-after window
     * (capped) and retries once. Beyond that the original error surfaces.
     *
     * @param  array<int, array{role: string, content: mixed}>  $messages
     * @param  array<int, array{name: string, description: string, input_schema: array<string, mixed>}>  $tools
     * @return array{content: array<int, mixed>, stop_reason: string, input_tokens: int, output_tokens: int}
     */
    public function askWithTools(string $systemPrompt, array $messages, array $tools): array
    {
        // Mark the system prompt as cacheable (must be array form when using cache_control).
        $systemBlocks = [
            [
                'type' => 'text',
                'text' => $systemPrompt,
                'cache_control' => ['type' => 'ephemeral'],
            ],
        ];

        // Mark the tool array as cacheable. The cache_control on the LAST tool
        // tells Anthropic to cache everything up through that point — covers
        // all tool definitions in one cache block.
        $cachedTools = $tools;
        if (! empty($cachedTools)) {
            $lastIdx = count($cachedTools) - 1;
            $cachedTools[$lastIdx]['cache_control'] = ['type' => 'ephemeral'];
        }

        $payload = [
            'model' => $this->model,
            'max_tokens' => 4096,
            'system' => $systemBlocks,
            'messages' => $messages,
            'tools' => $cachedTools,
        ];

        $response = $this->postWithRateLimitRetry($payload);

        if ($response->failed()) {
            Log::error('Anthropic API error (tool_use)', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Failed to get response from Anthropic.');
        }

        $data = $response->json();

        // Sum every input-token bucket Anthropic reports. With prompt caching
        // the response splits totals across input_tokens / cache_creation_input_tokens
        // / cache_read_input_tokens; for billing-style accounting we want them
        // all combined. Output is reported in a single bucket.
        $usage = $data['usage'] ?? [];
        $inputTokens = (int) ($usage['input_tokens'] ?? 0)
            + (int) ($usage['cache_creation_input_tokens'] ?? 0)
            + (int) ($usage['cache_read_input_tokens'] ?? 0);
        $outputTokens = (int) ($usage['output_tokens'] ?? 0);

        return [
            'content' => $data['content'] ?? [],
            'stop_reason' => $data['stop_reason'] ?? 'end_turn',
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
        ];
    }

    /**
     * POST to /v1/messages with one retry on 429.
     *
     * Honors the server's `retry-after` header but caps at 5 seconds — beyond
     * that we surface the 429 to the caller instead of blocking a PHP-FPM
     * worker. With max_children=3 in production, blocking three workers for
     * 30s each would freeze the entire chat endpoint for everyone.
     *
     * The trade-off: a fast server-suggested retry (1–5s) is silent and
     * recovers transparently. A long suggested retry (≥6s) becomes a clear
     * error to the user, who can wait and retry from the chat UI. Better
     * UX than silent multi-second hangs.
     */
    private function postWithRateLimitRetry(array $payload): Response
    {
        $url = 'https://api.anthropic.com/v1/messages';
        $headers = [
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ];

        $response = Http::withHeaders($headers)->timeout(120)->post($url, $payload);

        if ($response->status() !== 429) {
            return $response;
        }

        $retryAfterHeader = $response->header('retry-after');
        $retryAfter = $retryAfterHeader !== '' ? (int) $retryAfterHeader : 5;

        // Worker-blocking budget: 5 seconds.
        if ($retryAfter > 5) {
            Log::warning('Anthropic rate limit — retry-after exceeds blocking budget, surfacing 429', [
                'server_suggested' => $retryAfter,
                'budget_seconds' => 5,
            ]);

            return $response;
        }

        $waitSeconds = max(1, $retryAfter);

        Log::warning('Anthropic rate limit hit — retrying after short backoff', [
            'wait_seconds' => $waitSeconds,
        ]);

        sleep($waitSeconds);

        return Http::withHeaders($headers)->timeout(120)->post($url, $payload);
    }

    public static function displayName(): string
    {
        return 'Anthropic Claude';
    }

    public static function defaultModel(): string
    {
        return config('services.ai_providers.anthropic.default_model', 'claude-haiku-4-5-20251001');
    }
}
