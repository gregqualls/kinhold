<?php

namespace Tests\Unit\Config;

use Tests\TestCase;

class LoggingDefaultTest extends TestCase
{
    private ?string $originalLogChannel = null;

    protected function setUp(): void
    {
        parent::setUp();
        $value = getenv('LOG_CHANNEL');
        $this->originalLogChannel = $value === false ? null : $value;
    }

    protected function tearDown(): void
    {
        $this->setLogChannel($this->originalLogChannel);
        parent::tearDown();
    }

    public function test_defaults_to_stack_when_env_unset(): void
    {
        $this->setLogChannel(null);
        $this->assertSame('stack', $this->resolveDefault());
    }

    public function test_defaults_to_stack_when_env_empty_string(): void
    {
        $this->setLogChannel('');
        $this->assertSame('stack', $this->resolveDefault());
    }

    public function test_defaults_to_stack_when_env_is_literal_null_string(): void
    {
        $this->setLogChannel('null');
        $this->assertSame('stack', $this->resolveDefault());
    }

    public function test_uses_single_when_env_is_single(): void
    {
        $this->setLogChannel('single');
        $this->assertSame('single', $this->resolveDefault());
    }

    public function test_uses_stack_when_env_is_stack(): void
    {
        $this->setLogChannel('stack');
        $this->assertSame('stack', $this->resolveDefault());
    }

    private function setLogChannel(?string $value): void
    {
        if ($value === null) {
            putenv('LOG_CHANNEL');
            unset($_ENV['LOG_CHANNEL'], $_SERVER['LOG_CHANNEL']);

            return;
        }
        putenv('LOG_CHANNEL='.$value);
        $_ENV['LOG_CHANNEL'] = $value;
        $_SERVER['LOG_CHANNEL'] = $value;
    }

    private function resolveDefault(): string
    {
        $config = require base_path('config/logging.php');

        return $config['default'];
    }
}
