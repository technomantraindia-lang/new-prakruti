<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('consultation_requests')) {
            Schema::create('consultation_requests', function (Blueprint $table) {
                $table->id();
                $table->string('reference_no')->unique();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('preferred_contact_method', 30)->default('email');
                $table->text('concern');
                $table->string('age_range', 30)->nullable();
                $table->string('dietary_preference', 50)->nullable();
                $table->string('preferred_time')->nullable();
                $table->boolean('consent')->default(false);
                $table->string('status', 40)->default('new')->index();
                $table->text('internal_note')->nullable();
                $table->string('assigned_to')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_requests');
    }
};
