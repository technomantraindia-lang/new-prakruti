<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('family_packs')) {
            return;
        }

        DB::table('family_packs')
            ->whereNull('reminder_sent_at')
            ->orderBy('id')
            ->chunkById(100, function ($packs) {
                foreach ($packs as $pack) {
                    $createdAt = $pack->created_at ? Carbon::parse($pack->created_at) : now();

                    DB::table('family_packs')
                        ->where('id', $pack->id)
                        ->update([
                            'next_purchase_at' => $createdAt->copy()->addDays(30),
                            'next_reminder_at' => $createdAt->copy()->addDays(25),
                        ]);
                }
            });
    }

    public function down(): void
    {
        // No rollback needed: this migration only normalizes reminder dates for unsent reminders.
    }
};
