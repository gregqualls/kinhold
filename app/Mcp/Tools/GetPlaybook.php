<?php

namespace App\Mcp\Tools;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('get-playbook')]
#[Description('Get the full instructions for a vault playbook. Returns the complete playbook content that guides you through asking the user questions and creating vault entries. Use the slug from list-playbooks.')]
class GetPlaybook extends Tool
{
    public function schema($schema): array
    {
        return [
            'slug' => $schema->string()->required()->description('Playbook slug (e.g., "house-manual", "medical-info", "vehicle-info", "school-info", "emergency-contacts")'),
        ];
    }

    public function handle(Request $request): Response
    {
        $slug = $request->get('slug');
        if (! $slug) {
            return Response::error('slug is required.');
        }

        // Sanitize slug to prevent path traversal
        $slug = preg_replace('/[^a-zA-Z0-9\-_]/', '', $slug);

        $file = base_path("playbooks/vault/{$slug}.md");

        if (! file_exists($file)) {
            return Response::error("Playbook \"{$slug}\" not found. Use list-playbooks to see available playbooks.");
        }

        $content = file_get_contents($file);

        // Strip YAML frontmatter — the agent just needs the instructions
        $content = preg_replace('/^---\s*\n.*?\n---\s*\n/s', '', $content);

        return Response::text(trim($content));
    }
}
