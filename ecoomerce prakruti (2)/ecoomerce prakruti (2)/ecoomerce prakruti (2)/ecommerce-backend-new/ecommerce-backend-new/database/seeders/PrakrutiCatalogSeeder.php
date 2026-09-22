<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PrakrutiCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::firstOrCreate(
            ['slug' => 'prakruti-organic'],
            ['name' => 'Prakruti Organic', 'status' => 'active']
        );

        $categoriesList = [
            ['name' => 'Cereal & Pulses', 'slug' => 'cereal-pulses', 'description' => 'Daily essentials that nourish your body with pure plant-based protein and fiber.'],
            ['name' => 'Healthy Seeds', 'slug' => 'healthy-seeds', 'description' => 'Tiny powerhouses packed with nutrients for a healthier you.'],
            ['name' => 'Indian Spices', 'slug' => 'indian-spices', 'description' => 'Aromatic spices that add flavor, warmth and wellness to every meal.'],
            ['name' => 'Millets', 'slug' => 'millets', 'description' => 'Ancient grains for modern lifestyles - wholesome, hearty and naturally gluten-free.'],
            ['name' => 'Oils & Ghee', 'slug' => 'oils-ghee', 'description' => 'Pure, cold-pressed oils and traditional ghee for a healthy you.'],
            ['name' => 'Rice & Flours', 'slug' => 'rice-flours', 'description' => 'Wholesome grains and flours for everyday cooking and baking.'],
            ['name' => 'Sweeteners', 'slug' => 'sweeteners', 'description' => 'Natural sweeteners for mindful indulgence.'],
        ];

        $categoryMap = [];
        foreach ($categoriesList as $catData) {
            $categoryMap[$catData['name']] = Category::firstOrCreate(
                ['slug' => $catData['slug']],
                ['name' => $catData['name'], 'description' => $catData['description'], 'status' => 'active']
            );
        }

        $featured = [
            'Masoor Dal / Pink Lentil Split',
            'Panchratna Dal/Mix Dal ( 5 Pulses )',
            'Chia Seed',
            'Turmeric Powder',
            'A2 Gir Cow Ghee Bilona',
        ];

        $products = [
            ['Masoor Dal / Pink Lentil Split', 'Cereal & Pulses', 120],
            ['Panchratna Dal/Mix Dal ( 5 Pulses )', 'Cereal & Pulses', 140],
            ['Rajma Jammu', 'Cereal & Pulses', 150],
            ['Split Bengal Gram (Chana Dal)', 'Cereal & Pulses', 110],
            ['Tuar Dal/Arhar/Pigeon Pea', 'Cereal & Pulses', 120],
            ['Urad Dal Chilka / Black Gram Split', 'Cereal & Pulses', 130],
            ['White Chickpeas (Kabuli) Dollar Big Size', 'Cereal & Pulses', 160],
            ['Green Gram Whole (Moong)', 'Cereal & Pulses', 120],
            ['Brown Chick Peas Small Size [Desi Chana]', 'Cereal & Pulses', 110],
            ['Chia Seed', 'Healthy Seeds', 180],
            ['Flax Seed', 'Healthy Seeds', 110],
            ['Pumpkin Seeds', 'Healthy Seeds', 170],
            ['Sunflower Seed', 'Healthy Seeds', 120],
            ['Ajwain / Carom Seed', 'Indian Spices', 110],
            ['Black Pepper Whole / Kali Mirch', 'Indian Spices', 130],
            ['Black Sesame (Til)', 'Indian Spices', 110],
            ['Cinnamon Stick / Dal Chini', 'Indian Spices', 110],
            ['Clove Whole / Loung', 'Indian Spices', 120],
            ['Coriander Powder', 'Indian Spices', 110],
            ['Cumin (Jeera) / Cumin Whole', 'Indian Spices', 110],
            ['Cumin Powder / Jeera Powder', 'Indian Spices', 110],
            ['Pink Rock Salt / Sendha Namak (Light Pink)', 'Indian Spices', 55],
            ['Red Chilli Powder', 'Indian Spices', 110],
            ['Turmeric Powder', 'Indian Spices', 110],
            ['White Sesame (Til) / Natural', 'Indian Spices', 110],
            ['Bajra Whole / Pearl Millet', 'Millets', 75],
            ['Finger (Ragi) Millet', 'Millets', 75],
            ['Jawar/Sorghum Whole', 'Millets', 85],
            ['Kodo (Kodro) Millet', 'Millets', 80],
            ['Little Millet', 'Millets', 80],
            ['White Quinoa Seeds (Processed)', 'Millets', 120],
            ['A2 Gir Cow Ghee Bilona', 'Oils & Ghee', 750],
            ['Black Sesame Oil (Cold Pressed)', 'Oils & Ghee', 290],
            ['Groundnut Oil ( Cold Pressed)', 'Oils & Ghee', 220],
            ['Sunflower Oil [Cold Pressed]', 'Oils & Ghee', 180],
            ['Olive Oil Extra Virgin', 'Oils & Ghee', 650],
            ['Bajra Atta / Pearl Millet Flour', 'Rice & Flours', 75],
            ['Basmati Rice Brown', 'Rice & Flours', 150],
            ['Basmati Rice White', 'Rice & Flours', 130],
            ['Sona Masoori - White Rice', 'Rice & Flours', 120],
            ['Chana Besan / Bengal Gram Flour', 'Rice & Flours', 90],
            ['Finger (Ragi) Millet Flour', 'Rice & Flours', 80],
            ['Jowar Atta/Sorghum Flour', 'Rice & Flours', 75],
            ['Wheat Flour', 'Rice & Flours', 60],
            ['Multi Grain Flour', 'Rice & Flours', 100],
            ['Jaggery Powder', 'Sweeteners', 110],
            ['Off White Sugar Light', 'Sweeteners', 90],
            ['Raw Sugar / Khandasari Sugar (Brown)', 'Sweeteners', 110],
        ];

        foreach ($products as $index => $item) {
            [$name, $categoryName, $price] = $item;
            $sku = 'PRK-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
            $category = $categoryMap[$categoryName] ?? reset($categoryMap);
            $isFeatured = in_array($name, $featured, true);

            Product::updateOrCreate(
                ['sku' => $sku],
                [
                    'name' => $name,
                    'slug' => Str::slug($name) . '-' . ($index + 1),
                    'category_id' => $category->id,
                    'brand_id' => $brand->id,
                    'price' => $price,
                    'sale_price' => $isFeatured ? round($price * 0.9, 2) : null,
                    'gst_percentage' => 5,
                    'stock_qty' => 100,
                    'low_stock_qty' => 10,
                    'unit' => 'pack',
                    'min_order_qty' => 1,
                    'weight' => 1,
                    'short_desc' => "Premium organic {$name} sourced from trusted farms.",
                    'description' => "Premium quality {$name} sourced from organic farms. Clean, natural, and essential for healthy everyday cooking.",
                    'status' => 'active',
                    'featured' => $isFeatured,
                ]
            );
        }
    }
}
