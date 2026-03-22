<?php

namespace App\Services;

use App\Models\User;
use App\Models\Family;
use App\Models\Task;
use App\Models\VaultEntry;
use App\Services\AiProviders\AiProviderInterface;
use App\Services\AiProviders\AnthropicProvider;
use App\Services\AiProviders\OpenAiProvider;
use App\Services\AiProviders\GoogleGeminiProvider;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    /**
     * Map of provider slugs to their class names.
     */
    private const PROVIDERS = [
        'anthropic' => AnthropicProvider::class,
        'openai' => OpenAiProvider::class,
        'google' => GoogleGeminiProvider::class,
    ];

    /**
     * Send a message and get an AI response.
     *
     * @param string $message
     * @param User $user
     * @return string
     */
    public function chat(string $message, User $user): string
    {
        $family = $user->currentFamily()->firstOrFail();
        $provider = $this->resolveProvider($family);

        $context = $this->buildFamilyContext($user, $family);
        $systemPrompt = $this->buildSystemPrompt($user, $family);
        $userMessage = "{$message}\n\nFamily Context:\n{$context}";

        try {
            return $provider->ask($systemPrompt, $userMessage);
        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Resolve the AI provider based on family settings with .env fallback.
     */
    private function resolveProvider(Family $family): AiProviderInterface
    {
        $settings = $family->settings ?? [];

        // Determine which provider to use
        $providerSlug = $settings['ai_provider'] ?? 'anthropic';

        // Get the API key: family setting (encrypted) -> .env fallback for anthropic
        $apiKey = null;
        if (!empty($settings['ai_api_key'])) {
            try {
                $apiKey = decrypt($settings['ai_api_key']);
            } catch (\Exception $e) {
                Log::warning('Failed to decrypt family AI API key, falling back to .env');
            }
        }

        // Fallback to .env for Anthropic
        if (empty($apiKey) && $providerSlug === 'anthropic') {
            $apiKey = config('q32hub.chatbot.api_key');
        }

        if (empty($apiKey)) {
            throw new \RuntimeException(
                'No API key configured for ' . ($providerSlug ?? 'anthropic') . '. '
                . 'Add one in Settings > API Configuration.'
            );
        }

        // Get optional model override
        $model = $settings['ai_model'] ?? '';

        // Build the provider
        $providerClass = self::PROVIDERS[$providerSlug] ?? AnthropicProvider::class;

        return new $providerClass($apiKey, $model);
    }

    /**
     * Get available providers with their metadata.
     *
     * @return array
     */
    public static function availableProviders(): array
    {
        return [
            [
                'slug' => 'anthropic',
                'name' => AnthropicProvider::displayName(),
                'default_model' => AnthropicProvider::defaultModel(),
                'key_placeholder' => 'sk-ant-...',
                'key_prefix' => 'sk-ant-',
                'help_url' => 'https://console.anthropic.com/settings/keys',
            ],
            [
                'slug' => 'openai',
                'name' => OpenAiProvider::displayName(),
                'default_model' => OpenAiProvider::defaultModel(),
                'key_placeholder' => 'sk-...',
                'key_prefix' => 'sk-',
                'help_url' => 'https://platform.openai.com/api-keys',
            ],
            [
                'slug' => 'google',
                'name' => GoogleGeminiProvider::displayName(),
                'default_model' => GoogleGeminiProvider::defaultModel(),
                'key_placeholder' => 'AIza...',
                'key_prefix' => 'AIza',
                'help_url' => 'https://aistudio.google.com/apikey',
            ],
        ];
    }

    /**
     * Build system prompt for the chatbot.
     */
    private function buildSystemPrompt(User $user, Family $family): string
    {
        return <<<PROMPT
You are a helpful family assistant AI for the Q32 Hub family management application.
You help family members with tasks, scheduling, security information, and general questions.

Current user: {$user->name}
Family: {$family->name}
User role: {$user->family_role->value}

Guidelines:
- Be friendly and supportive
- Provide practical advice for family management
- Respect privacy - only share information the user has access to
- For children, avoid sharing detailed security/vault information they don't have access to
- Help with task planning, scheduling, and reminders
- Be concise but helpful
- Ask clarifying questions when needed
PROMPT;
    }

    /**
     * Build context from family data the user has access to.
     */
    private function buildFamilyContext(User $user, Family $family): string
    {
        $context = [];

        // Family members
        $members = $family->members->map(fn ($m) => "{$m->name} ({$m->family_role->value})")->join(', ');
        $context[] = "Family members: {$members}";

        // Pending tasks
        $pendingTasks = $this->getPendingTasks($family, $user);
        if (!empty($pendingTasks)) {
            $context[] = "Pending tasks:\n" . implode("\n", $pendingTasks);
        }

        // Accessible vault summary
        $vaultSummary = $this->getAccessibleVaultSummary($user, $family);
        if (!empty($vaultSummary)) {
            $context[] = "Vault info: " . implode(', ', $vaultSummary);
        }

        return implode("\n\n", $context);
    }

    /**
     * Get pending tasks for the family.
     */
    private function getPendingTasks(Family $family, User $user): array
    {
        try {
            $tasks = Task::whereHas('taskList', function ($query) use ($family) {
                $query->where('family_id', $family->id);
            })
                ->whereNull('completed_at')
                ->orderBy('due_date')
                ->limit(10)
                ->get();

            return $tasks->map(fn ($task) =>
                "- {$task->title}" .
                ($task->assignee ? " (assigned to {$task->assignee->name})" : "") .
                ($task->due_date ? " [due: {$task->due_date->format('M d')}]" : "")
            )->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get accessible vault entry summary for the user.
     */
    private function getAccessibleVaultSummary(User $user, Family $family): array
    {
        try {
            if ($user->isParent()) {
                $count = VaultEntry::where('family_id', $family->id)->count();
                return ["You have access to all {$count} vault entries as a parent"];
            }

            $count = VaultEntry::whereHas('permissions', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count();

            return ["You have access to {$count} vault entries"];
        } catch (\Exception $e) {
            return [];
        }
    }
}
