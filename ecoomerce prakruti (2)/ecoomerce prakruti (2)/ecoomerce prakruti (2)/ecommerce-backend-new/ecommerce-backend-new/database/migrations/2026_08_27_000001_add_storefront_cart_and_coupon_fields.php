<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('carts')) {
            if (Schema::hasColumn('carts', 'user_id')) {
                try {
                    DB::statement('ALTER TABLE carts MODIFY user_id BIGINT UNSIGNED NULL');
                } catch (\Throwable $e) {
                    Schema::table('carts', function (Blueprint $table) {
                        $table->unsignedBigInteger('user_id')->nullable()->change();
                    });
                }
            }

            if (! Schema::hasColumn('carts', 'session_id')) {
                Schema::table('carts', function (Blueprint $table) {
                    $table->string('session_id', 64)->nullable()->after('user_id');
                    $table->index('session_id');
                });
            }
        }

        if (Schema::hasTable('coupons') && ! Schema::hasColumn('coupons', 'used_count')) {
            Schema::table('coupons', function (Blueprint $table) {
                $table->unsignedInteger('used_count')->default(0)->after('per_user_limit');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('carts') && Schema::hasColumn('carts', 'session_id')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->dropIndex(['session_id']);
                $table->dropColumn('session_id');
            });
        }

        if (Schema::hasTable('coupons') && Schema::hasColumn('coupons', 'used_count')) {
            Schema::table('coupons', function (Blueprint $table) {
                $table->dropColumn('used_count');
            });
        }
    }
};
