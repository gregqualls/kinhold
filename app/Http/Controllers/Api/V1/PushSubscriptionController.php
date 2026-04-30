<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Notifications\TestPushNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use NotificationChannels\WebPush\WebPushMessage;

class PushSubscriptionController extends Controller
{
    /**
     * Register (or refresh) a browser push subscription for the current user.
     *
     * The browser's PushManager.subscribe() returns a subscription whose endpoint
     * uniquely identifies the device. Re-subscribing with the same endpoint is
     * the canonical way to refresh expired keys, so we upsert by endpoint via
     * HasPushSubscriptions::updatePushSubscription().
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string|max:500',
            'keys' => 'required|array',
            'keys.p256dh' => 'required|string|max:255',
            'keys.auth' => 'required|string|max:255',
            'content_encoding' => 'nullable|string|max:32',
        ]);

        $user = $request->user();

        $subscription = $user->updatePushSubscription(
            endpoint: $validated['endpoint'],
            key: $validated['keys']['p256dh'],
            token: $validated['keys']['auth'],
            contentEncoding: $validated['content_encoding'] ?? 'aesgcm',
        );

        return response()->json([
            'subscription' => [
                'id' => $subscription->id,
                'endpoint' => $subscription->endpoint,
            ],
        ], 201);
    }

    /**
     * Remove a subscription by endpoint (typically called when the browser
     * unsubscribes via PushManager.unsubscribe() or on logout).
     */
    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string|max:500',
        ]);

        $request->user()->deletePushSubscription($validated['endpoint']);

        return response()->json([], 204);
    }

    /**
     * Send a sample push to all of the current user's subscriptions —
     * a one-click verification that the SW + VAPID + delivery path are wired up.
     */
    public function test(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->pushSubscriptions()->count() === 0) {
            return response()->json([
                'message' => 'No push subscriptions registered for this user.',
            ], 422);
        }

        $message = (new WebPushMessage)
            ->title('Kinhold push is working')
            ->body('You will see notifications here.')
            ->icon('/icons/icon-192.png')
            ->badge('/icons/badge-96.png')
            ->tag('kinhold-push-test')
            ->data([
                'type' => 'test',
                'url' => '/settings',
            ]);

        $user->notifyNow(new TestPushNotification($message));

        return response()->json([
            'message' => 'Test push dispatched.',
        ]);
    }
}
