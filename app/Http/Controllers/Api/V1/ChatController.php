<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Services\AgentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    private AgentService $agentService;

    public function __construct(AgentService $agentService)
    {
        $this->agentService = $agentService;
    }

    /**
     * Send a message and get AI agent response.
     */
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $user = $request->user();

        try {
            $familyId = $user->currentFamily()->first()->id;

            // Store user message
            $userMessage = ChatMessage::create([
                'user_id' => $user->id,
                'family_id' => $familyId,
                'message' => $validated['message'],
                'role' => 'user',
            ]);

            // Get agent response (may execute multiple tool calls)
            $result = $this->agentService->chat($validated['message'], $user);

            // Store AI response with tool metadata
            $aiMessage = ChatMessage::create([
                'user_id' => $user->id,
                'family_id' => $familyId,
                'message' => $result['text'],
                'role' => 'assistant',
                'metadata' => ! empty($result['tools_used']) ? [
                    'tools_used' => $result['tools_used'],
                ] : null,
            ]);

            return response()->json([
                'user_message' => [
                    'id' => $userMessage->id,
                    'message' => $userMessage->message,
                    'role' => $userMessage->role,
                    'created_at' => $userMessage->created_at,
                ],
                'assistant_message' => [
                    'id' => $aiMessage->id,
                    'message' => $aiMessage->message,
                    'role' => $aiMessage->role,
                    'created_at' => $aiMessage->created_at,
                ],
            ], 200);
        } catch (\RuntimeException $e) {
            // Surface specific configuration errors (e.g., no API key)
            return response()->json([
                'message' => $e->getMessage(),
                'error_type' => 'configuration',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to process chat message. Please try again.',
            ], 500);
        }
    }

    /**
     * Get recent chat history.
     */
    public function history(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = $request->query('limit', 50);

        $messages = ChatMessage::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'messages' => $messages->map(fn ($msg) => [
                'id' => $msg->id,
                'message' => $msg->message,
                'role' => $msg->role,
                'created_at' => $msg->created_at,
            ]),
        ], 200);
    }
}
