<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'products' => ['woocommerce_id', 'woocommerce_sync_status', 'woocommerce_synced_at'],
            'product_variations' => ['woocommerce_id', 'woocommerce_sync_status', 'woocommerce_synced_at'],
            'orders' => ['woocommerce_id', 'woocommerce_synced_at'],
            'users' => ['woocommerce_customer_id'],
            'coupons' => ['woocommerce_id'],
        ] as $table => $columns) {
            if (Schema::hasTable($table)) {
                foreach ($columns as $column) {
                    if (Schema::hasColumn($table, $column)) {
                        Schema::table($table, function (Blueprint $schema) use ($column) {
                            $schema->dropColumn($column);
                        });
                    }
                }
            }
        }

        Schema::dropIfExists('woocommerce_sync_conflicts');
        Schema::dropIfExists('woocommerce_sync_logs');
        Schema::dropIfExists('webhook_logs');
    }

    public function down(): void
    {
        // WooCommerce integration was intentionally removed and is not restored.
    }
};
