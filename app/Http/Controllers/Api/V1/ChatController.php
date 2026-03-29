<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    private ChatbotService $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Send a message and get AI response.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $user = $request->user();

        try {
            // Store user message
            $userMessage = ChatMessage::create([
                'user_id' => $user->id,
                'family_id' => $user->currentFamily()->first()->id,
                'message' => $validated['message'],
                'role' => 'user',
            ]);

            // Get AI response
            $response = $this->chatbotService->chat($validated['message'], $user);

            // Store AI response
            $aiMessage = ChatMessage::create([
                'user_id' => $user->id,
                'family_id' => $user->currentFamily()->first()->id,
                'message' => $response,
                'role' => 'assistant',
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
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to process chat message. Please try again.',
            ], 500);
        }
    }

    /**
     * Get recent chat history.
     *
     * @param Request $request
     * @return JsonResponse
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
