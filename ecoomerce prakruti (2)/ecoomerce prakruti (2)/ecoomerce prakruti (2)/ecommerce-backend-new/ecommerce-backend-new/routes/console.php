<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use App\Models\FamilyPack;
use App\Notifications\FamilyPackReminderNotification;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('family-packs:send-reminders', function () {
    $sent = 0;

    FamilyPack::with('user')
        ->where('status', 'active')
        ->whereNull('reminder_sent_at')
        ->whereNotNull('next_reminder_at')
        ->where('next_reminder_at', '<=', now())
        ->chunkById(100, function ($packs) use (&$sent) {
            foreach ($packs as $pack) {
                if (! $pack->user?->email) {
                    continue;
                }

                try {
                    $pack->user->notify(new FamilyPackReminderNotification($pack));
                    $pack->update(['reminder_sent_at' => now()]);
                    $sent++;
                } catch (\Throwable $exception) {
                    Log::error('Family pack reminder could not be sent.', [
                        'family_pack_id' => $pack->id,
                        'user_id' => $pack->user_id,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }
        });

    $this->info("Sent {$sent} family pack reminder(s).");
})->purpose('Send family pack reorder reminders due before the next monthly purchase.');

Schedule::command('family-packs:send-reminders')->dailyAt('09:00');
