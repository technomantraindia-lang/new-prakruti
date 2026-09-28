<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $items = [
            [
                'category' => 'fields',
                'title' => 'Certified Organic Spelt & Wheat Farmlands',
                'description' => 'Our organic grains are grown in native, mineral-rich soils completely free of synthetic pesticides and fertilizers, yielding higher natural fiber, rich micronutrients, and authentic flavor.',
                'image' => 'farm-gallery/defaults/farm-gallery-spelt-wheat.jpg',
                'tag' => 'Sowing & Fields',
                'location' => 'Punjab & Madhya Pradesh',
                'details' => 'Native heirloom seed preservation with compost-enriched organic soil management.',
                'sort_order' => 1,
            ],
            [
                'category' => 'processing',
                'title' => 'Traditional Vedic Bilona A2 Gir Cow Ghee',
                'description' => 'Handcrafted from fresh curd of free-grazing indigenous Gir cows, slowly hand-churned in wooden bilona clay pots and woodfire simmered into golden, aromatic, granular elixir.',
                'image' => 'farm-gallery/defaults/bestseller-gir-ghee.jpg',
                'tag' => 'Vedic Bilona Ghee',
                'location' => 'Gir Somnath & Saurashtra Pastures',
                'details' => 'Cruelty-free grass-fed A2 Gir cows, bi-directional wooden bilona curd churning, low-heat brass vessel boiling.',
                'sort_order' => 2,
                'videos' => [
                    ['title' => 'Woodfire Simmering & Clarification', 'video' => 'farm-gallery/videos/default-ghee-video-1.mp4', 'sort_order' => 1],
                    ['title' => 'Traditional Curd Churning (Bilona)', 'video' => 'farm-gallery/videos/default-ghee-video-2.mp4', 'sort_order' => 2],
                    ['title' => 'Pouring Golden Granular Ghee', 'video' => 'farm-gallery/videos/default-ghee-video-3.mp4', 'sort_order' => 3],
                ],
            ],
            [
                'category' => 'harvesting',
                'title' => 'Ethical & Natural Manual Harvesting',
                'description' => 'Each crop is hand-harvested at peak solar ripeness by generational farming families to ensure that living enzymes, vital nutrients, and natural plant aromas are fully preserved.',
                'image' => 'farm-gallery/defaults/ethical-natural-manual-harvesting.png',
                'tag' => 'Manual Harvest',
                'location' => 'Rural Farmer Cooperatives',
                'details' => 'Hand-picked selection and natural shade drying to preserve delicate nutrients.',
                'sort_order' => 3,
            ],
            [
                'category' => 'processing',
                'title' => 'Traditional Wooden Ghani Cold-Pressing',
                'description' => 'Our cold-pressed cooking oils are gently extracted using slow wooden Ghani rotations at temperatures strictly kept below 40C, keeping essential antioxidants and aroma intact.',
                'image' => 'farm-gallery/defaults/oil-and-ghee.png',
                'tag' => 'Cold Pressing',
                'location' => 'Heritage Extraction Mills',
                'details' => 'Zero artificial heat, zero chemical solvents, 100% virgin unrefined oils.',
                'sort_order' => 4,
            ],
            [
                'category' => 'processing',
                'title' => 'Sustainable Eco-Friendly Packaging',
                'description' => 'All Prakruti food staples are packed in food-grade recyclable containers and vacuum-sealed multi-layer barrier pouches to lock in purity, aroma, and prevent oxidation.',
                'image' => 'farm-gallery/defaults/sustainable-eco-friendly-packaging.png',
                'tag' => 'Eco Packaging',
                'location' => 'Clean Certified Facility',
                'details' => 'Food-grade tamper-evident packaging protecting raw natural freshness.',
                'sort_order' => 5,
            ],
            [
                'category' => 'fields',
                'title' => 'Traditional Spice Cultivation in Native Soils',
                'description' => 'High-curcumin turmeric and robust regional spices are cultivated in fertile Western Ghats soils adopting bio-fertilizers, green manure, and biodiversity farming.',
                'image' => 'farm-gallery/defaults/traditional-spice-cultivation.png',
                'tag' => 'Spice Farms',
                'location' => 'Salem & Western Ghats',
                'details' => 'Sun-cured naturally with high essential oil concentration and zero chemical dyes.',
                'sort_order' => 6,
            ],
            [
                'category' => 'harvesting',
                'title' => 'Handpicked Nutrient-Dense Seed Selection',
                'description' => 'Carefully sorted and triple-cleaned seeds ensuring uniform high-grade grains, zero grit, and optimal omega-3 fatty acid and dietary fiber retention.',
                'image' => 'farm-gallery/defaults/handpicked-seed-selection.png',
                'tag' => 'Seed Selection',
                'location' => 'Local Farmer Collectives',
                'details' => 'Triple gravity-cleaned sorting retaining whole-grain nutrient density.',
                'sort_order' => 7,
            ],
        ];

        foreach ($items as $item) {
            $videos = $item['videos'] ?? [];
            unset($item['videos']);

            $itemId = DB::table('farm_gallery_items')->where('title', $item['title'])->value('id');

            if (! $itemId) {
                $itemId = DB::table('farm_gallery_items')->insertGetId(array_merge($item, [
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }

            foreach ($videos as $video) {
                DB::table('farm_gallery_videos')->updateOrInsert(
                    [
                        'farm_gallery_item_id' => $itemId,
                        'video' => $video['video'],
                    ],
                    [
                        'title' => $video['title'],
                        'sort_order' => $video['sort_order'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        $titles = [
            'Certified Organic Spelt & Wheat Farmlands',
            'Traditional Vedic Bilona A2 Gir Cow Ghee',
            'Ethical & Natural Manual Harvesting',
            'Traditional Wooden Ghani Cold-Pressing',
            'Sustainable Eco-Friendly Packaging',
            'Traditional Spice Cultivation in Native Soils',
            'Handpicked Nutrient-Dense Seed Selection',
        ];

        $ids = DB::table('farm_gallery_items')->whereIn('title', $titles)->pluck('id');
        DB::table('farm_gallery_videos')->whereIn('farm_gallery_item_id', $ids)->delete();
        DB::table('farm_gallery_items')->whereIn('id', $ids)->delete();
    }
};
