<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('invoices');
    }

    public function down(): void
    {
        // Invoices were intentionally removed and are not restored.
    }
};
