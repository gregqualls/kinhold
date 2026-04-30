<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FamilyEvent;
use App\Models\MealPlanEntry;
use App\Models\ShoppingItem;
use App\Models\ShoppingList;
use App\Models\Task;
use App\Notifications\CalendarEventReminderNotification;
use App\Notifications\DinnerReminderNotification;
use App\Notifications\ShoppingListItemAddedNotification;
use App\Notifications\TaskDueSoonNotification;
use App\Notifications\TestPushNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

    /**
     * Fire a sample of one of the registry-driven notification types so users
     * can see exactly how it will render before waiting for the real trigger
     * (e.g., dinner-time, an 8am task reminder, an event 30 minutes out).
     *
     * The sample data is fully synthetic — Eloquent instances are populated
     * in-memory and never saved. Dispatch is `notifyNow` so the WebPush HTTP
     * call happens inside the request and any failure surfaces in the response.
     */
    public function testType(Request $request, string $key): JsonResponse
    {
        $user = $request->user();

        if ($user->pushSubscriptions()->count() === 0) {
            return response()->json([
                'message' => 'No push subscriptions registered for this user.',
            ], 422);
        }

        $sample = match ($key) {
            'task_due_soon' => $this->buildTaskDueSoonSample(),
            'shopping_item_added' => $this->buildShoppingItemAddedSample($user),
            'calendar_event_reminder' => $this->buildCalendarEventReminderSample(),
            'dinner_reminder' => $this->buildDinnerReminderSample(),
            default => null,
        };

        if (! $sample) {
            return response()->json([
                'message' => "Test notifications are not available for '{$key}'.",
            ], 422);
        }

        // Build the real notification's payload, then re-dispatch through
        // TestPushNotification so the test fires regardless of the user's
        // current preference for this key — the click is the explicit consent.
        // Stamp a unique suffix onto the tag so repeated tests render as new
        // toasts instead of silently replacing the previous test (the real
        // dispatch sites use stable tags on purpose — for actual dedup).
        $message = $sample->toWebPush($user, $sample);
        $message->tag($key.'-test-'.microtime(true));
        $user->notifyNow(new TestPushNotification($message));

        return response()->json([
            'message' => "Test {$key} notification dispatched.",
        ]);
    }

    private function buildTaskDueSoonSample(): TaskDueSoonNotification
    {
        $task = new Task;
        $task->id = 'demo-task';
        $task->title = '🧪 Test: Take out the trash';
        $task->description = 'This is a sample notification — no real task was created.';
        $task->due_date = Carbon::today();
        $task->points = 5;

        return new TaskDueSoonNotification($task);
    }

    private function buildShoppingItemAddedSample($user): ShoppingListItemAddedNotification
    {
        $list = new ShoppingList;
        $list->id = 'demo-list';
        $list->name = 'Weekly Groceries';

        $item = new ShoppingItem;
        $item->id = 'demo-item';
        $item->shopping_list_id = $list->id;
        $item->name = 'Milk (test item)';
        $item->quantity = '1 gal';
        $item->setRelation('shoppingList', $list);

        return new ShoppingListItemAddedNotification($item, $user);
    }

    private function buildCalendarEventReminderSample(): CalendarEventReminderNotification
    {
        $event = new FamilyEvent;
        $event->id = 'demo-event';
        $event->title = '🧪 Test: Soccer practice';
        $event->location = 'Test Field';
        $event->reminder_minutes_before = 30;

        return new CalendarEventReminderNotification($event, Carbon::now()->addMinutes(30));
    }

    private function buildDinnerReminderSample(): DinnerReminderNotification
    {
        $entry = new MealPlanEntry;
        $entry->id = 'demo-entry';
        $entry->date = Carbon::today();
        $entry->custom_title = '🧪 Test: Spaghetti night';
        $entry->assigned_cooks = [];

        return new DinnerReminderNotification($entry);
    }
}
