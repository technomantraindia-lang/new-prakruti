<?php

namespace Database\Seeders;

use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Inquiry;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Page;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\Role;
use App\Models\Setting;
use App\Models\ShippingMethod;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin'], ['guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Admin'], ['guard_name' => 'web']);
        $customerRole = Role::firstOrCreate(['name' => 'Customer'], ['guard_name' => 'web']);

        $this->call(PermissionSeeder::class);

        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@prakruti'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
                'phone' => '9876543210',
                'role_id' => $superAdminRole->id,
                'status' => 'active',
            ]
        );

        $customer = User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'phone' => '9876543211',
                'role_id' => $customerRole->id,
                'status' => 'active',
            ]
        );

        $prakrutiBrand = Brand::firstOrCreate(
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
            $catObj = Category::firstOrCreate(
                ['slug' => $catData['slug']],
                ['name' => $catData['name'], 'description' => $catData['description'], 'status' => 'active']
            );
            $categoryMap[$catData['name']] = $catObj;
        }

        $this->call(PrakrutiCatalogSeeder::class);

        $weightAttr = ProductAttribute::firstOrCreate(['name' => 'Weight'], ['status' => 'active']);
        $packAttr = ProductAttribute::firstOrCreate(['name' => 'Pack Size'], ['status' => 'active']);
        AttributeValue::firstOrCreate(['slug' => '1kg'], ['attribute_id' => $weightAttr->id, 'value' => '1kg', 'status' => 'active']);
        AttributeValue::firstOrCreate(['slug' => '500g'], ['attribute_id' => $weightAttr->id, 'value' => '500g', 'status' => 'active']);
        AttributeValue::firstOrCreate(['slug' => 'small'], ['attribute_id' => $packAttr->id, 'value' => 'Small', 'status' => 'active']);
        AttributeValue::firstOrCreate(['slug' => 'large'], ['attribute_id' => $packAttr->id, 'value' => 'Large', 'status' => 'active']);

        Tax::firstOrCreate(['name' => '5% GST'], ['percentage' => 5, 'status' => 'active']);
        Tax::firstOrCreate(['name' => '12% GST'], ['percentage' => 12, 'status' => 'active']);
        Tax::firstOrCreate(['name' => '18% GST'], ['percentage' => 18, 'status' => 'active']);
        Tax::firstOrCreate(['name' => '28% GST'], ['percentage' => 28, 'status' => 'active']);

        $standardShipping = ShippingMethod::firstOrCreate(
            ['name' => 'Standard Delivery'],
            [
                'charge' => 50.00,
                'min_free_order' => 500.00,
                'status' => 'active',
            ]
        );

        ShippingMethod::firstOrCreate(
            ['name' => 'Express Delivery'],
            [
                'charge' => 100.00,
                'min_free_order' => 1000.00,
                'status' => 'active',
            ]
        );

        Coupon::firstOrCreate(
            ['code' => 'PRAKRUTI100'],
            [
                'type' => 'fixed',
                'value' => 100,
                'min_order' => 200,
                'usage_limit' => 1000,
                'per_user_limit' => 5,
                'used_count' => 0,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addYear()->toDateString(),
                'status' => 'active',
            ]
        );

        $coupon = Coupon::firstOrCreate(
            ['code' => 'SAVE10'],
            [
                'type' => 'percentage',
                'value' => 10,
                'min_order' => 200,
                'usage_limit' => 100,
                'per_user_limit' => 2,
                'used_count' => 0,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonths(3)->toDateString(),
                'status' => 'active',
            ]
        );

        Setting::updateOrCreate(['key' => 'company_name'], ['value' => 'Prakruti Organic']);
        Setting::updateOrCreate(['key' => 'company_address'], ['value' => 'Delhi, India']);
        Setting::updateOrCreate(['key' => 'company_email'], ['value' => 'info@prakrutiorganic.com']);
        Setting::updateOrCreate(['key' => 'company_phone'], ['value' => '9876543210']);
        Setting::updateOrCreate(['key' => 'gst_number'], ['value' => '07AABCU9603R1Z0']);
        Setting::updateOrCreate(['key' => 'currency'], ['value' => 'INR']);
        Setting::updateOrCreate(['key' => 'order_prefix'], ['value' => 'PRK']);

        $order = Order::firstOrCreate(
            ['order_num' => 'PRK-10001'],
            [
                'user_id' => $customer->id,
                'subtotal' => 310.00,
                'discount' => 31.00,
                'gst_amt' => 13.95,
                'ship_charge' => 50.00,
                'total' => 342.95,
                'coupon_id' => $coupon->id,
                'ship_id' => $standardShipping->id,
                'status' => 'processing',
                'pay_status' => 'paid',
            ]
        );

        $seedProduct = Product::where('sku', 'PRK-001')->first();
        $secondProduct = Product::where('sku', 'PRK-002')->first();

        if ($seedProduct && ! OrderItem::where('order_id', $order->id)->where('product_id', $seedProduct->id)->exists()) {
            OrderItem::create(['order_id' => $order->id, 'product_id' => $seedProduct->id, 'product_name' => $seedProduct->name, 'sku' => $seedProduct->sku, 'qty' => 1, 'price' => 105.00, 'gst_pct' => 5, 'line_total' => 105.00]);
        }
        if ($secondProduct && ! OrderItem::where('order_id', $order->id)->where('product_id', $secondProduct->id)->exists()) {
            OrderItem::create(['order_id' => $order->id, 'product_id' => $secondProduct->id, 'product_name' => $secondProduct->name, 'sku' => $secondProduct->sku, 'qty' => 1, 'price' => 125.00, 'gst_pct' => 5, 'line_total' => 125.00]);
        }

        if (! \App\Models\OrderStatusHistory::where('order_id', $order->id)->exists()) {
            \App\Models\OrderStatusHistory::create(['order_id' => $order->id, 'status' => 'pending', 'note' => 'Order placed']);
            \App\Models\OrderStatusHistory::create(['order_id' => $order->id, 'status' => 'processing', 'note' => 'Order confirmed and processing']);
        }

        Payment::firstOrCreate(
            ['order_id' => $order->id],
            [
                'amount' => 342.95,
                'method' => 'cod',
                'status' => 'paid',
                'txn_id' => 'TXN-' . time(),
            ]
        );

        Invoice::firstOrCreate(
            ['order_id' => $order->id],
            [
                'inv_num' => 'INV-10001',
                'inv_data' => null,
            ]
        );

        Inquiry::firstOrCreate(
            ['email' => 'rahul@example.com'],
            [
                'name' => 'Rahul Sharma',
                'phone' => '9988776655',
                'product_id' => $seedProduct?->id,
                'msg' => 'Is this available in bulk quantity?',
                'status' => 'pending',
            ]
        );

        Page::firstOrCreate(
            ['slug' => 'about-us'],
            [
                'title' => 'About Us',
                'content' => 'Prakruti Organic is your trusted source for pure, carefully sourced organic staples.',
                'status' => 'active',
            ]
        );

        Page::firstOrCreate(
            ['slug' => 'privacy-policy'],
            [
                'title' => 'Privacy Policy',
                'content' => 'Your privacy is important to us. We protect your personal information.',
                'status' => 'active',
            ]
        );
    }
}
