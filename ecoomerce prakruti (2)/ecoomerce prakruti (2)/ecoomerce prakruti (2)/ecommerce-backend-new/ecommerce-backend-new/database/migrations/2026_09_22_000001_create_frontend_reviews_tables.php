<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_reviews')) {
            Schema::create('product_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('name');
                $table->string('location')->nullable();
                $table->unsignedTinyInteger('rating')->default(5);
                $table->text('comment');
                $table->string('status', 24)->default('active')->index();
                $table->string('source', 40)->default('frontend');
                $table->timestamps();

                $table->index(['product_id', 'status', 'created_at']);
            });
        }

        if (Schema::hasTable('testimonials')) {
            Schema::table('testimonials', function (Blueprint $table) {
                if (! Schema::hasColumn('testimonials', 'location')) {
                    $table->string('location')->nullable()->after('name');
                }
                if (! Schema::hasColumn('testimonials', 'source')) {
                    $table->string('source', 40)->default('frontend')->after('status');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');

        if (Schema::hasTable('testimonials')) {
            Schema::table('testimonials', function (Blueprint $table) {
                if (Schema::hasColumn('testimonials', 'source')) {
                    $table->dropColumn('source');
                }
                if (Schema::hasColumn('testimonials', 'location')) {
                    $table->dropColumn('location');
                }
            });
        }
    }
};
