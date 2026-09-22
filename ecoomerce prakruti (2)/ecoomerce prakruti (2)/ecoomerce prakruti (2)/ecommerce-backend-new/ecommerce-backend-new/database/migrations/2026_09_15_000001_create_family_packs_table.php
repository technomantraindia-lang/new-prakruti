<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('family_packs')) {
            Schema::create('family_packs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->json('profile');
                $table->json('nutrient_summary')->nullable();
                $table->json('recommendations')->nullable();
                $table->decimal('monthly_total', 12, 2)->default(0);
                $table->timestamp('next_purchase_at')->nullable();
                $table->timestamp('next_reminder_at')->nullable();
                $table->timestamp('reminder_sent_at')->nullable();
                $table->string('status')->default('active');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique('user_id');
                $table->index(['status', 'next_reminder_at', 'reminder_sent_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('family_packs');
    }
};
