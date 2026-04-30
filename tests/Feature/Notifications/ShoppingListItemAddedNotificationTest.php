<?php

namespace Tests\Feature\Notifications;

use App\Enums\ShoppingItemSource;
use App\Models\Family;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\ShoppingList;
use App\Models\User;
use App\Notifications\ShoppingListItemAddedNotification;
use App\Services\ShoppingListService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use Tests\TestCase;

class ShoppingListItemAddedNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_item_notifies_other_family_members(): void
    {
        Notification::fake();

        $family = Family::factory()->create();
        $adder = User::factory()->create(['family_id' => $family->id]);
        $other = User::factory()->create([
            'family_id' => $family->id,
            'notification_preferences' => [
                'email' => [],
                'push' => ['shopping_item_added' => true],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
            ],
        ]);
        $other->updatePushSubscription(endpoint: 'https://example.test/p', key: 'pk', token: 'auth');

        $list = ShoppingList::create([
            'family_id' => $family->id,
            'created_by' => $adder->id,
            'name' => 'Weekly',
        ]);

        app(ShoppingListService::class)->addItem($list, ['name' => 'Milk'], $adder);

        Notification::assertSentTo($other, ShoppingListItemAddedNotification::class);
    }

    public function test_add_item_does_not_notify_the_adder(): void
    {
        Notification::fake();

        $family = Family::factory()->create();
        $adder = User::factory()->create(['family_id' => $family->id]);

        $list = ShoppingList::create([
            'family_id' => $family->id,
            'created_by' => $adder->id,
            'name' => 'Weekly',
        ]);

        app(ShoppingListService::class)->addItem($list, ['name' => 'Milk'], $adder);

        Notification::assertNotSentTo($adder, ShoppingListItemAddedNotification::class);
    }

    public function test_add_recipe_ingredients_does_not_dispatch_notifications(): void
    {
        Notification::fake();

        $family = Family::factory()->create();
        $adder = User::factory()->create(['family_id' => $family->id]);
        User::factory()->create(['family_id' => $family->id]);

        $list = ShoppingList::create([
            'family_id' => $family->id,
            'created_by' => $adder->id,
            'name' => 'Weekly',
        ]);

        $recipe = Recipe::create([
            'family_id' => $family->id,
            'created_by' => $adder->id,
            'title' => 'Pasta',
        ]);
        RecipeIngredient::create([
            'recipe_id' => $recipe->id,
            'name' => 'Tomato',
            'quantity' => '2',
            'sort_order' => 0,
        ]);

        app(ShoppingListService::class)->addRecipeIngredients($list, $recipe, $adder);

        Notification::assertNothingSent();
    }

    public function test_via_returns_only_push_channel(): void
    {
        $family = Family::factory()->create();
        $adder = User::factory()->create(['family_id' => $family->id]);
        $recipient = User::factory()->create([
            'family_id' => $family->id,
            'notification_preferences' => [
                'email' => [],
                'push' => ['shopping_item_added' => true],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
            ],
        ]);
        $recipient->updatePushSubscription(endpoint: 'https://example.test/p', key: 'pk', token: 'auth');
        $recipient->refresh();

        $list = ShoppingList::create([
            'family_id' => $family->id,
            'created_by' => $adder->id,
            'name' => 'Weekly',
        ]);
        $item = $list->items()->create([
            'family_id' => $family->id,
            'added_by' => $adder->id,
            'name' => 'Milk',
            'source' => ShoppingItemSource::Manual,
        ]);

        $channels = (new ShoppingListItemAddedNotification($item, $adder))->via($recipient);

        $this->assertSame([WebPushChannel::class], $channels);
    }
}
