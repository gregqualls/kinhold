<?php

namespace App\Services\AiProviders;

interface AiProviderInterface
{
    /**
     * Send a message to the AI provider and get a response.
     *
     * @param string $systemPrompt The system prompt with context
     * @param string $userMessage The user's message with family context appended
     * @return string The AI's response text
     * @throws \RuntimeException If the API call fails
     */
    public function ask(string $systemPrompt, string $userMessage): string;

    /**
     * Get the provider's display name.
     */
    public static function displayName(): string;

    /**
     * Get the default model for this provider.
     */
    public static function defaultModel(): string;
}
