<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('rto_shipments');
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('order_returns');
    }

    public function down(): void
    {
        // Returns and RTO are intentionally removed and are not restored.
    }
};
