<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Services\AgentService;
use App\Services\AiUsageService;
use App\Services\BillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(
        private AgentService $agentService,
        private AiUsageService $usageService,
        private BillingService $billingService,
    ) {}

    /**
     * Send a message and get AI agent response.
     */
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $user = $request->user();
        $family = $user->currentFamily()->first();

        // Demo-family layered caps (#266). Run before the family-wide check so
        // session/monthly limits surface a more relevant message than the
        // generic "daily limit reached" copy.
        //
        // Per-visitor key: the Sanctum access token ID. Demo logins create one
        // token per browser (demoLogin returns a fresh plainTextToken), so this
        // gives us the "one slot per browser" semantics the issue asks for
        // without requiring a stateful session — the API is stateless. Falls
        // back to user_id|ip when no token row is available (still rate-limits
        // per IP for the unusual case).
        $isDemo = $this->billingService->isDemoFamily($family);
        $demoSessionKey = null;
        if ($isDemo) {
            $token = $user->currentAccessToken();
            $tokenId = is_object($token) && isset($token->id) ? (string) $token->id : null;
            $demoSessionKey = $tokenId
                ? 'token:'.$tokenId
                : 'fallback:'.$user->id.'|'.$request->ip();
        }

        if ($isDemo && $this->usageService->isDemoMonthExhausted($family)) {
            return response()->json([
                'error' => 'demo_monthly_limit_reached',
                'message' => 'The demo is taking a breather this month. Sign up for a free trial to chat with the assistant.',
                'usage' => $this->usageService->payloadFor($family),
            ], 429);
        }

        if ($isDemo && $this->usageService->isDemoSessionExhausted($demoSessionKey)) {
            return response()->json([
                'error' => 'demo_session_limit_reached',
                'message' => 'You\'ve used your '.$this->usageService->demoSessionLimit().' demo messages. Sign up for a free trial to keep chatting.',
                'usage' => $this->usageService->payloadFor($family),
            ], 429);
        }

        // Pre-flight limit check. Recording happens after a successful response
        // so failed calls (Anthropic timeouts, validation errors) don't burn a
        // user's quota.
        if ($this->usageService->shouldEnforce($family) && $this->usageService->isExhausted($family)) {
            return response()->json([
                'error' => 'usage_limit_reached',
                'message' => 'Daily AI assistant limit reached. Resets at midnight UTC.',
                'usage' => $this->usageService->payloadFor($family),
            ], 429);
        }

        try {
            // Agent loop may call multiple tools — extend timeout
            set_time_limit(120);

            // Store user message
            $userMessage = ChatMessage::create([
                'user_id' => $user->id,
                'family_id' => $family->id,
                'message' => $validated['message'],
                'role' => 'user',
            ]);

            // Get agent response (may execute multiple tool calls)
            $result = $this->agentService->chat($validated['message'], $user);

            // Record usage before storing the assistant message so the chip in
            // the JSON response reflects this turn. Only counts when enforced —
            // BYOK and self-hosted families don't accrue against the cap.
            if ($this->usageService->shouldEnforce($family)) {
                $this->usageService->recordMessage(
                    $family,
                    $result['input_tokens'],
                    $result['output_tokens'],
                );
            }

            // Demo session counter ticks regardless of shouldEnforce — the
            // session cap exists specifically to prevent one visitor from
            // burning the demo's daily allotment.
            if ($demoSessionKey !== null) {
                $this->usageService->incrementDemoSession($demoSessionKey);
            }

            $metadata = [];
            if (! empty($result['tools_used'])) {
                $metadata['tools_used'] = $result['tools_used'];
            }
            if ($result['input_tokens'] > 0 || $result['output_tokens'] > 0) {
                $metadata['input_tokens'] = $result['input_tokens'];
                $metadata['output_tokens'] = $result['output_tokens'];
            }

            $aiMessage = ChatMessage::create([
                'user_id' => $user->id,
                'family_id' => $family->id,
                'message' => $result['text'],
                'role' => 'assistant',
                'metadata' => $metadata !== [] ? $metadata : null,
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
                'usage' => $this->usageService->payloadFor($family),
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

        $family = $user->currentFamily()->first();

        return response()->json([
            'messages' => $messages->map(fn ($msg) => [
                'id' => $msg->id,
                'message' => $msg->message,
                'role' => $msg->role,
                'created_at' => $msg->created_at,
            ]),
            'usage' => $family ? $this->usageService->payloadFor($family) : null,
        ], 200);
    }
}
