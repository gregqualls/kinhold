<?php

namespace Tests\Unit;

use App\Services\AiProviders\AnthropicProvider;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AnthropicValidateKeyTest extends TestCase
{
    public function test_returns_valid_true_on_success(): void
    {
        Http::fake(['api.anthropic.com/*' => Http::response(['content' => []], 200)]);

        $this->assertSame(['valid' => true], AnthropicProvider::validateKey('sk-ant-good'));
    }

    public function test_returns_invalid_on_authentication_error(): void
    {
        Http::fake(['api.anthropic.com/*' => Http::response([
            'error' => ['type' => 'authentication_error', 'message' => 'invalid x-api-key'],
        ], 401)]);

        $result = AnthropicProvider::validateKey('sk-ant-bad');
        $this->assertFalse($result['valid']);
        $this->assertSame('Invalid API key.', $result['error']);
    }

    public function test_returns_invalid_on_rate_limit(): void
    {
        Http::fake(['api.anthropic.com/*' => Http::response([
            'error' => ['type' => 'rate_limit_error', 'message' => 'slow down'],
        ], 429)]);

        $result = AnthropicProvider::validateKey('sk-ant-throttled');
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('Rate limited', $result['error']);
    }

    public function test_returns_invalid_on_connection_failure(): void
    {
        Http::fake(function () {
            throw new ConnectionException('dns blew up');
        });

        $result = AnthropicProvider::validateKey('sk-ant-anything');
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('Could not reach Anthropic', $result['error']);
    }

    public function test_falls_back_to_truncated_message_on_other_error(): void
    {
        Http::fake(['api.anthropic.com/*' => Http::response([
            'error' => ['type' => 'invalid_request_error', 'message' => 'model not found'],
        ], 400)]);

        $result = AnthropicProvider::validateKey('sk-ant-misconfigured');
        $this->assertFalse($result['valid']);
        $this->assertSame('model not found', $result['error']);
    }
}
