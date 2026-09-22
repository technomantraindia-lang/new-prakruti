<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('categories') && Schema::hasColumn('categories', 'parent_id')) {
            DB::table('categories')->whereNotNull('parent_id')->update(['parent_id' => null]);
        }

        if (Schema::hasTable('products')) {
            if (Schema::hasColumn('products', 'sub_category_id')) {
                DB::table('products')->whereNotNull('sub_category_id')->update(['sub_category_id' => null]);
            }

            if (Schema::hasColumn('products', 'sub_sub_category_id')) {
                DB::table('products')->whereNotNull('sub_sub_category_id')->update(['sub_sub_category_id' => null]);
            }
        }
    }

    public function down(): void
    {
        // Intentionally left blank: the previous hierarchy cannot be safely reconstructed.
    }
};
