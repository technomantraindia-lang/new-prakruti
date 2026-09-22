<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('coupon_usages');

        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'coupon_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['coupon_id']);
                $table->dropColumn('coupon_id');
            });
        }

        Schema::dropIfExists('coupons');
    }

    public function down(): void
    {
        // Coupons were intentionally removed and are not restored.
    }
};
