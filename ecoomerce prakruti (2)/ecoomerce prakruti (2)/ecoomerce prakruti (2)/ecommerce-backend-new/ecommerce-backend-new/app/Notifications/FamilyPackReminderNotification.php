<?php

namespace App\Notifications;

use App\Models\FamilyPack;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FamilyPackReminderNotification extends Notification
{
    public function __construct(private readonly FamilyPack $familyPack)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $total = number_format((float) $this->familyPack->monthly_total, 2);

        return (new MailMessage)
            ->subject('Your Prakruti Family Pack is ready to reorder')
            ->greeting('Hello ' . ($notifiable->name ?: 'there') . ',')
            ->line('Your saved monthly family pack is due soon.')
            ->line("Estimated pack value: Rs. {$total}.")
            ->action('Reorder Family Pack', url('/#family-pack'))
            ->line('You can edit family member ages, quantities, and extra products before purchase.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Family Pack reorder reminder',
            'message' => 'Your saved monthly family pack is due soon.',
            'level' => 'info',
            'action_url' => url('/#family-pack'),
            'metadata' => [
                'family_pack_id' => $this->familyPack->id,
                'monthly_total' => (float) $this->familyPack->monthly_total,
                'next_purchase_at' => $this->familyPack->next_purchase_at?->toISOString(),
            ],
        ];
    }
}
