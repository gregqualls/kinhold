<?php

namespace App\Services;

use App\Models\User;
use App\Models\Family;
use App\Models\Task;
use App\Models\VaultEntry;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    /**
     * Send a message and get Claude's response.
     *
     * @param string $message
     * @param User $user
     * @return string
     */
    public function chat(string $message, User $user): string
    {
        if (!config('q32hub.chatbot.enabled')) {
            throw new \RuntimeException('Chatbot is not configured. Add ANTHROPIC_API_KEY to your .env file.');
        }

        $family = $user->currentFamily()->firstOrFail();
        $context = $this->buildFamilyContext($user, $family);
        $systemPrompt = $this->buildSystemPrompt($user, $family);

        try {
            $response = Http::withHeaders([
                'x-api-key' => config('q32hub.chatbot.api_key'),
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => config('q32hub.chatbot.model', 'claude-sonnet-4-5-20250514'),
                'max_tokens' => 1024,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "{$message}\n\nFamily Context:\n{$context}",
                    ],
                ],
            ]);

            if ($response->failed()) {
                Log::error('Chatbot API error: ' . $response->body());
                throw new \RuntimeException('Failed to get response from AI service.');
            }

            $data = $response->json();
            return $data['content'][0]['text'] ?? 'Sorry, I could not generate a response.';
        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            throw $e;
        }
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
