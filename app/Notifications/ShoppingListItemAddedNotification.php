<?php

namespace App\Notifications;

use App\Models\ShoppingItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class ShoppingListItemAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ShoppingItem $item,
        public User $addedBy,
    ) {}

    public function via(object $notifiable): array
    {
        if ($notifiable->wants('push', 'shopping_item_added') && ! $notifiable->isPushSuppressed()) {
            return [WebPushChannel::class];
        }

        return [];
    }

    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        $listName = $this->item->shoppingList?->name ?: 'shopping list';
        $itemBody = $this->item->quantity
            ? "{$this->item->quantity} · {$this->item->name}"
            : $this->item->name;

        return (new WebPushMessage)
            ->title("{$this->addedBy->name} added to {$listName}")
            ->body($itemBody)
            ->icon('/icons/icon-192.png')
            ->badge('/icons/badge-96.png')
            ->tag('shopping-list-'.$this->item->shopping_list_id)
            ->data([
                'type' => 'shopping_item_added',
                'url' => '/shopping?list='.$this->item->shopping_list_id,
                'list_id' => $this->item->shopping_list_id,
                'item_id' => $this->item->id,
            ])
            ->options(['TTL' => 60 * 60 * 24]);
    }
}
