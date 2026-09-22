<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\FamilyPack;
use App\Notifications\FamilyPackReminderNotification;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('family-packs:send-reminders', function () {
    $packs = FamilyPack::with('user')
        ->where('status', 'active')
        ->whereNull('reminder_sent_at')
        ->whereNotNull('next_reminder_at')
        ->where('next_reminder_at', '<=', now())
        ->get();

    foreach ($packs as $pack) {
        if (! $pack->user) {
            continue;
        }

        $pack->user->notify(new FamilyPackReminderNotification($pack));
        $pack->update(['reminder_sent_at' => now()]);
    }

    $this->info("Sent {$packs->count()} family pack reminder(s).");
})->purpose('Send family pack reorder reminders due before the next monthly purchase.');
