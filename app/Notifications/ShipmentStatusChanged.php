<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShipmentStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Order $order,
        public readonly Shipment $shipment,
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
            ->subject(__('orders.shipment_subject', ['number' => $this->order->number]))
            ->greeting(__('orders.greeting', ['name' => $notifiable->name]))
            ->line(__('orders.shipment_line', [
                'number' => $this->order->number,
                'status' => __('orders.status_'.$this->status),
            ]))
            ->when(
                filled($this->shipment->tracking_number),
                fn (MailMessage $message) => $message->line(
                    __('orders.tracking_line', ['number' => $this->shipment->tracking_number]),
                ),
            )
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
            'type' => 'shipment.status_changed',
            'order_uuid' => $this->order->uuid,
            'order_number' => $this->order->number,
            'shipment_uuid' => $this->shipment->uuid,
            'status' => $this->status,
            'tracking_number' => $this->shipment->tracking_number,
        ];
    }
}
