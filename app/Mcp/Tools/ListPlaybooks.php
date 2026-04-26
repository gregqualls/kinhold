<?php

namespace App\Mcp\Tools;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('list-playbooks')]
#[Description('List available vault playbooks — guided workflows that help families set up their vault by asking questions and creating entries automatically. Each playbook covers a topic like house manual, medical info, vehicles, school, or emergency contacts.')]
class ListPlaybooks extends Tool
{
    public function schema($schema): array
    {
        return [
            'tag' => $schema->string()->description('Optional tag to filter playbooks (e.g., "house", "medical", "school")'),
        ];
    }

    public function handle(Request $request): Response
    {
        $playbookDir = resource_path('playbooks/vault');

        if (! is_dir($playbookDir)) {
            return Response::json(['playbooks' => [], 'message' => 'No playbooks directory found.']);
        }

        $files = glob($playbookDir.'/*.md');
        $tagFilter = $request->get('tag');

        $playbooks = [];
        foreach ($files as $file) {
            $parsed = $this->parsePlaybook($file);
            if (! $parsed) {
                continue;
            }

            if ($tagFilter && ! in_array(strtolower($tagFilter), array_map('strtolower', $parsed['tags'] ?? []), true)) {
                continue;
            }

            $playbooks[] = [
                'slug' => pathinfo($file, PATHINFO_FILENAME),
                'name' => $parsed['name'],
                'description' => $parsed['description'],
                'category' => $parsed['category'] ?? null,
                'icon' => $parsed['icon'] ?? null,
                'tags' => $parsed['tags'] ?? [],
            ];
        }

        return Response::json([
            'playbooks' => $playbooks,
            'count' => count($playbooks),
            'usage' => 'Use get-playbook with a slug to retrieve the full playbook instructions.',
        ]);
    }

    private function parsePlaybook(string $file): ?array
    {
        $content = file_get_contents($file);
        if (! $content) {
            return null;
        }

        // Parse YAML frontmatter with simple regex (avoids symfony/yaml dependency)
        if (! preg_match('/^---\s*\n(.*?)\n---/s', $content, $matches)) {
            return null;
        }

        $frontmatter = [];
        foreach (explode("\n", $matches[1]) as $line) {
            if (preg_match('/^(\w+):\s*(.+)$/', trim($line), $kv)) {
                $value = trim($kv[2]);
                // Handle array values like [tag1, tag2]
                if (str_starts_with($value, '[') && str_ends_with($value, ']')) {
                    $value = array_map('trim', explode(',', trim($value, '[]')));
                }
                $frontmatter[$kv[1]] = $value;
            }
        }

        return $frontmatter ?: null;
    }
}
