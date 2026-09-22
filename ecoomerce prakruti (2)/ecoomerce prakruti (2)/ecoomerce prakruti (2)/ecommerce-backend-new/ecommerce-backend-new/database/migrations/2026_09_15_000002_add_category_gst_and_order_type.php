<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('categories') && ! Schema::hasColumn('categories', 'gst_percentage')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->decimal('gst_percentage', 5, 2)->default(5)->after('description');
            });
        }

        if (Schema::hasTable('orders') && ! Schema::hasColumn('orders', 'order_type')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('order_type')->default('standard')->after('order_num')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'order_type')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('order_type');
            });
        }

        if (Schema::hasTable('categories') && Schema::hasColumn('categories', 'gst_percentage')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('gst_percentage');
            });
        }
    }
};
