<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\ChatMessage;
use App\Models\Family;
use App\Models\Task;
use App\Models\User;
use App\Models\VaultEntry;
use App\Services\AiProviders\AiProviderInterface;
use App\Services\AiProviders\AnthropicProvider;
use App\Services\AiProviders\GoogleGeminiProvider;
use App\Services\AiProviders\OpenAiProvider;
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
     */
    public function chat(string $message, User $user): string
    {
        $family = $user->currentFamily()->firstOrFail();
        $provider = $this->resolveProvider($family);

        $context = $this->buildFamilyContext($user, $family);
        $systemPrompt = $this->buildSystemPrompt($user, $family);
        $userMessage = "{$message}\n\nFamily Context:\n{$context}";

        // Fetch recent conversation history (last 10 messages = 5 turns)
        $history = $this->getConversationHistory($user, 10);

        try {
            return $provider->ask($systemPrompt, $userMessage, $history);
        } catch (\Exception $e) {
            Log::error('Chatbot error: '.$e->getMessage());
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

        // Get the API key based on ai_mode:
        // 'kinhold' = use our platform key, 'byok' = use family's own key
        $aiMode = $settings['ai_mode'] ?? 'kinhold';
        $apiKey = null;

        if ($aiMode === 'byok' && ! empty($settings['ai_api_key'])) {
            try {
                $apiKey = decrypt($settings['ai_api_key']);
            } catch (\Exception $e) {
                Log::warning('Failed to decrypt family AI API key, falling back to platform key');
            }
        }

        // Platform key: always available for anthropic (kinhold mode or byok fallback)
        if (empty($apiKey) && $providerSlug === 'anthropic') {
            $apiKey = config('kinhold.chatbot.api_key');
        }

        if (empty($apiKey)) {
            throw new \RuntimeException(
                'No API key configured for '.($providerSlug ?? 'anthropic').'. '
                .'Add one in Settings > API Configuration.'
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
You are a helpful family assistant AI for the Kinhold family management application.
You help family members with tasks, scheduling, security information, and general questions.

Current user: {$user->name}
Family: {$family->name}
User role: {$user->family_role->value}

Guidelines:
- Be friendly and supportive
- Provide practical advice for family management
- Respect privacy - only share information the user has access to
- For children, avoid sharing detailed security/vault information they don't have access to
- NEVER reveal details about hidden badges (name, description, criteria, or how to earn them). If asked, say "There are some surprise badges to discover!" but don't give specifics
- Help with task planning, scheduling, and reminders
- Be concise but helpful
- Ask clarifying questions when needed
PROMPT;
    }

    /**
     * Get recent conversation history for multi-turn context.
     */
    private function getConversationHistory(User $user, int $limit = 10): array
    {
        try {
            return ChatMessage::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->reverse()
                ->map(fn ($msg) => [
                    'role' => $msg->role,
                    'content' => $msg->message,
                ])
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
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
        if (! empty($pendingTasks)) {
            $context[] = "Pending tasks:\n".implode("\n", $pendingTasks);
        }

        // Accessible vault summary
        $vaultSummary = $this->getAccessibleVaultSummary($user, $family);
        if (! empty($vaultSummary)) {
            $context[] = 'Vault info: '.implode(', ', $vaultSummary);
        }

        // Badges (exclude hidden unearned badges)
        $badgeSummary = $this->getBadgeSummary($user, $family);
        if (! empty($badgeSummary)) {
            $context[] = "Badges:\n".implode("\n", $badgeSummary);
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

            return $tasks->map(fn ($task) => "- {$task->title}".
                ($task->assignee ? " (assigned to {$task->assignee->name})" : '').
                ($task->due_date ? " [due: {$task->due_date->format('M d')}]" : '')
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

    /**
     * Get badge summary, excluding hidden badges the user hasn't earned.
     */
    private function getBadgeSummary(User $user, Family $family): array
    {
        try {
            $earnedBadgeIds = $user->badges()->pluck('badges.id')->toArray();

            $badges = Badge::where('family_id', $family->id)
                ->where('is_active', true)
                ->get();

            $lines = [];
            foreach ($badges as $badge) {
                $earned = in_array($badge->id, $earnedBadgeIds);

                // Never reveal hidden badge details unless earned
                if ($badge->is_hidden && ! $earned) {
                    continue;
                }

                $status = $earned ? 'EARNED' : 'not yet earned';
                $lines[] = "- {$badge->name}: {$badge->description} [{$status}]";
            }

            $hiddenCount = $badges->where('is_hidden', true)
                ->whereNotIn('id', $earnedBadgeIds)
                ->count();

            if ($hiddenCount > 0) {
                $lines[] = "- Plus {$hiddenCount} hidden surprise badge(s) to discover!";
            }

            return $lines;
        } catch (\Exception $e) {
            return [];
        }
    }
}
