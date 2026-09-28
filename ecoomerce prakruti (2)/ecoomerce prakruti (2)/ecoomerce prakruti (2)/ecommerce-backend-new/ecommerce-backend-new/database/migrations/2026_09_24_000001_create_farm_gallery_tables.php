<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('title');
            $table->text('description');
            $table->string('image');
            $table->string('tag');
            $table->string('location');
            $table->text('details');
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('farm_gallery_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_gallery_item_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('video');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_gallery_videos');
        Schema::dropIfExists('farm_gallery_items');
    }
};
