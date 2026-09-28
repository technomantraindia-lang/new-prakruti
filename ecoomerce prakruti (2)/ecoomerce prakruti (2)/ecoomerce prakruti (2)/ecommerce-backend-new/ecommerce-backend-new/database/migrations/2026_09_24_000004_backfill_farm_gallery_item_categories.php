<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $existingSlugs = DB::table('farm_gallery_categories')->pluck('slug')->all();
        $itemCategories = DB::table('farm_gallery_items')->distinct()->pluck('category');
        $sortOrder = (int) DB::table('farm_gallery_categories')->max('sort_order');

        foreach ($itemCategories as $slug) {
            if (! $slug || in_array($slug, $existingSlugs, true)) {
                continue;
            }

            DB::table('farm_gallery_categories')->insert([
                'name' => Str::headline($slug),
                'slug' => $slug,
                'sort_order' => ++$sortOrder,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $existingSlugs[] = $slug;
        }
    }

    public function down(): void
    {
        $usedSlugs = DB::table('farm_gallery_items')->distinct()->pluck('category')->all();
        $defaultSlugs = ['fields', 'harvesting', 'processing'];

        DB::table('farm_gallery_categories')
            ->whereIn('slug', array_diff($usedSlugs, $defaultSlugs))
            ->delete();
    }
};
