<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressApiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $addresses = Address::where('user_id', $user->id)->latest()->get()->map(function ($address) {
            return $this->present($address);
        });

        return response()->json([
            'success' => true,
            'data' => $addresses,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'nullable|string|in:shipping,billing',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'fname' => 'nullable|string|max:255',
            'lname' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'nullable|string|max:500',
            'address' => 'nullable|string|max:500',
            'address_line_2' => 'nullable|string|max:500',
            'apt' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pincode' => 'nullable|string|max:10',
            'zip' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $isDefault = (bool) ($validated['is_default'] ?? false);

        if ($isDefault) {
            Address::where('user_id', $user->id)->update(['default' => false]);
        }

        $address = Address::create([
            'user_id' => $user->id,
            'type' => $validated['type'] ?? 'shipping',
            'fname' => $validated['first_name'] ?? $validated['fname'] ?? $user->name,
            'lname' => $validated['last_name'] ?? $validated['lname'] ?? '',
            'address' => $validated['address_line_1'] ?? $validated['address'] ?? '',
            'apt' => $validated['address_line_2'] ?? $validated['apt'] ?? null,
            'city' => $validated['city'],
            'state' => $validated['state'],
            'zip' => $validated['pincode'] ?? $validated['zip'] ?? '',
            'country' => $validated['country'] ?? 'India',
            'phone' => $validated['phone'],
            'default' => $isDefault,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address saved successfully.',
            'data' => $this->present($address),
        ], 201);
    }

    private function present(Address $address): array
    {
        return [
            'id' => $address->id,
            'type' => $address->type,
            'first_name' => $address->fname,
            'last_name' => $address->lname,
            'phone' => $address->phone,
            'address_line_1' => $address->address,
            'address_line_2' => $address->apt,
            'city' => $address->city,
            'state' => $address->state,
            'pincode' => $address->zip,
            'country' => $address->country,
            'is_default' => (bool) $address->default,
        ];
    }
}
