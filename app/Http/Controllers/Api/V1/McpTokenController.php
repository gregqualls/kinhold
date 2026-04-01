<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class McpTokenController extends Controller
{
    /**
     * Check if the user has an active MCP token.
     */
    public function show(Request $request): JsonResponse
    {
        $token = $request->user()->tokens()->where('name', 'mcp')->first();

        return response()->json([
            'has_token' => (bool) $token,
            'created_at' => $token?->created_at,
            'last_used_at' => $token?->last_used_at,
            'mcp_url' => rtrim(config('app.url'), '/').'/mcp',
        ]);
    }

    /**
     * Generate a new MCP token, revoking any existing ones.
     * Returns the plain token and ready-to-use config snippets.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        // Revoke existing MCP tokens (one active at a time)
        $user->tokens()->where('name', 'mcp')->delete();

        $token = $user->createToken('mcp');
        $plainToken = $token->plainTextToken;
        $mcpUrl = rtrim(config('app.url'), '/').'/mcp';

        $serverConfig = [
            'kinhold' => [
                'type' => 'streamable-http',
                'url' => $mcpUrl,
                'headers' => [
                    'Authorization' => 'Bearer '.$plainToken,
                ],
            ],
        ];

        return response()->json([
            'has_token' => true,
            'created_at' => $token->accessToken->created_at,
            'plain_token' => $plainToken,
            'mcp_url' => $mcpUrl,
            'clients' => [
                [
                    'id' => 'claude_desktop',
                    'name' => 'Claude Desktop',
                    'instructions' => 'Add to your claude_desktop_config.json:',
                    'config' => ['mcpServers' => $serverConfig],
                ],
                [
                    'id' => 'chatgpt',
                    'name' => 'ChatGPT',
                    'instructions' => 'In ChatGPT, go to Settings > Connectors > Add, then enter:',
                    'steps' => [
                        'Open ChatGPT Settings > Connectors > Advanced > Developer Mode',
                        'Click "Add connector"',
                        'Set the URL to: '.$mcpUrl,
                        'Add authorization header: Bearer '.$plainToken,
                        'Save and start a new chat with the connector enabled',
                    ],
                ],
                [
                    'id' => 'claude_code',
                    'name' => 'Claude Code',
                    'instructions' => 'Run in your terminal:',
                    'command' => 'claude mcp add kinhold --transport streamable-http --url '.escapeshellarg($mcpUrl).' --header '.escapeshellarg('Authorization: Bearer '.$plainToken),
                ],
                [
                    'id' => 'other',
                    'name' => 'Other',
                    'instructions' => 'Use these details to connect any MCP-compatible app:',
                    'details' => [
                        'url' => $mcpUrl,
                        'transport' => 'streamable-http',
                        'auth_header' => 'Authorization: Bearer '.$plainToken,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Revoke all MCP tokens for the user.
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->user()->tokens()->where('name', 'mcp')->delete();

        return response()->json([
            'has_token' => false,
            'message' => 'MCP token revoked.',
        ]);
    }
}
