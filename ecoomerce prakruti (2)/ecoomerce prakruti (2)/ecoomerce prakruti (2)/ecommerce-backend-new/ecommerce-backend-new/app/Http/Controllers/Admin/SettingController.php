<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('key')->get()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $admin = $request->user();

        $data = $request->validate([
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
            'admin_current_email' => 'nullable|email|max:255',
            'admin_new_email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($admin?->id),
            ],
            'admin_current_password' => 'nullable|string',
            'admin_new_password' => 'nullable|string|min:8|confirmed',
        ]);

        $adminAccountChanged = false;
        $wantsEmailChange = $request->filled('admin_current_email') || $request->filled('admin_new_email');
        $wantsPasswordChange = $request->filled('admin_current_password') || $request->filled('admin_new_password');

        if ($wantsEmailChange) {
            if (! $admin || ! $request->filled('admin_current_email') || ! $request->filled('admin_new_email')) {
                return back()
                    ->withInput()
                    ->withErrors(['admin_new_email' => 'Please enter both old email and new email to change the admin email.']);
            }

            if (strcasecmp((string) $admin->email, (string) $data['admin_current_email']) !== 0) {
                return back()
                    ->withInput()
                    ->withErrors(['admin_current_email' => 'Old admin email does not match the logged-in admin account.']);
            }

            $admin->forceFill(['email' => strtolower(trim((string) $data['admin_new_email']))])->save();
            $adminAccountChanged = true;
        }

        if ($wantsPasswordChange) {
            if (! $admin || ! $request->filled('admin_current_password') || ! $request->filled('admin_new_password')) {
                return back()
                    ->withInput()
                    ->withErrors(['admin_new_password' => 'Please enter old password and new password to change the admin password.']);
            }

            if (! Hash::check((string) $data['admin_current_password'], (string) $admin->password)) {
                return back()
                    ->withInput()
                    ->withErrors(['admin_current_password' => 'Old password is incorrect.']);
            }

            $admin->forceFill(['password' => Hash::make($data['admin_new_password'])])->save();
            $adminAccountChanged = true;
        }

        Setting::set('bill_discount_enabled', $request->boolean('bill_discount_enabled') ? '1' : '0');
        Setting::set('bill_discount_rules', json_encode($this->discountRules($request)));

        unset(
            $data['bill_discount_min_amount'],
            $data['bill_discount_percent'],
            $data['admin_current_email'],
            $data['admin_new_email'],
            $data['admin_current_password'],
            $data['admin_new_password'],
            $data['admin_new_password_confirmation'],
        );

        foreach ($data as $key => $value) {
            Setting::set($key, $value ?? '');
        }

        $message = $adminAccountChanged
            ? 'Settings saved and admin account updated successfully.'
            : 'Settings saved successfully.';

        return redirect()->route('admin.settings.index')->with('success', $message);
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
