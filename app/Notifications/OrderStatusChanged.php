<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Order $order,
        public readonly string $status,
    ) {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('orders.status_subject', ['number' => $this->order->number]))
            ->greeting(__('orders.greeting', ['name' => $notifiable->name]))
            ->line(__('orders.status_line', [
                'number' => $this->order->number,
                'status' => __('orders.status_'.$this->status),
            ]))
            ->action(
                __('orders.view_order'),
                route('orders.show', [
                    'locale' => $notifiable->preferredLocale(),
                    'order' => $this->order,
                ]),
            );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order.status_changed',
            'order_uuid' => $this->order->uuid,
            'number' => $this->order->number,
            'status' => $this->status,
        ];
    }
}
