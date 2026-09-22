<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(private ImageUploadService $uploader) {}

    public function index()
    {
        $settings = Setting::orderBy('key')->get()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
            'gst_number' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:10',
            'order_prefix' => 'nullable|string|max:20',
            'min_order_amount' => 'nullable|numeric|min:0',
            'standard_shipping_charge' => 'nullable|numeric|min:0',
            'free_shipping_min_amount' => 'nullable|numeric|min:0',
            'bill_discount_min_amount' => 'nullable|array',
            'bill_discount_min_amount.*' => 'nullable|numeric|min:0',
            'bill_discount_percent' => 'nullable|array',
            'bill_discount_percent.*' => 'nullable|numeric|min:0|max:100',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $old = Setting::get('logo');
            $this->uploader->delete($old);
            Setting::set('logo', $this->uploader->upload($request->file('logo'), 'settings'));
        }

        Setting::set('bill_discount_enabled', $request->boolean('bill_discount_enabled') ? '1' : '0');
        Setting::set('bill_discount_rules', json_encode($this->discountRules($request)));

        unset($data['logo'], $data['bill_discount_min_amount'], $data['bill_discount_percent']);
        foreach ($data as $key => $value) {
            Setting::set($key, $value ?? '');
        }

        return redirect()->route('admin.settings.index')->with('success', 'Settings saved successfully.');
    }

    private function discountRules(Request $request): array
    {
        $amounts = $request->input('bill_discount_min_amount', []);
        $percents = $request->input('bill_discount_percent', []);
        $rules = [];

        foreach ($amounts as $index => $amount) {
            $minAmount = (float) ($amount ?: 0);
            $percent = (float) ($percents[$index] ?? 0);

            if ($minAmount <= 0 || $percent <= 0) {
                continue;
            }

            $rules[] = [
                'min_amount' => round($minAmount, 2),
                'percent' => round(min($percent, 100), 2),
            ];
        }

        usort($rules, fn ($a, $b) => $a['min_amount'] <=> $b['min_amount']);

        return array_values($rules);
    }
}
