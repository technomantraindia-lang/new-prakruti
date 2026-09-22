<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FamilyPack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class FamilyPackApiController extends Controller
{
    public function latest(Request $request)
    {
        if (! Schema::hasTable('family_packs')) {
            return response()->json([
                'success' => true,
                'message' => 'No family pack saved yet.',
                'data' => null,
            ]);
        }

        $pack = FamilyPack::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        return response()->json([
            'success' => true,
            'message' => $pack ? 'Family pack fetched successfully.' : 'No family pack saved yet.',
            'data' => $pack ? $this->serialize($pack) : null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'profile' => 'required|array',
            'profile.members' => 'required|array|min:1',
            'profile.members.*.name' => 'required|string|max:80',
            'profile.members.*.age' => 'required|integer|min:1|max:110',
            'profile.adults' => 'nullable|integer|min:0',
            'profile.children' => 'nullable|integer|min:0',
            'nutrient_summary' => 'nullable|array',
            'recommendations' => 'required|array|min:1',
            'recommendations.*.product_id' => 'required|exists:products,id',
            'recommendations.*.name' => 'required|string|max:255',
            'recommendations.*.qty' => 'required|integer|min:1',
            'recommendations.*.price' => 'nullable|numeric|min:0',
            'recommendations.*.label' => 'nullable|string|max:120',
            'recommendations.*.benefit' => 'nullable|string|max:255',
            'monthly_total' => 'nullable|numeric|min:0',
        ]);

        $nextPurchaseAt = now()->addMonth();
        $nextReminderAt = $nextPurchaseAt->copy()->subDays(3);

        if (! Schema::hasTable('family_packs')) {
            return response()->json([
                'success' => true,
                'message' => 'Family pack saved in this browser. Please run backend migrations to save it permanently.',
                'data' => [
                    'id' => null,
                    'profile' => $validated['profile'],
                    'nutrient_summary' => $validated['nutrient_summary'] ?? [],
                    'recommendations' => $validated['recommendations'],
                    'monthly_total' => (float) ($validated['monthly_total'] ?? 0),
                    'next_purchase_at' => $nextPurchaseAt->toISOString(),
                    'next_reminder_at' => $nextReminderAt->toISOString(),
                    'reminder_sent_at' => null,
                    'status' => 'active',
                ],
            ]);
        }

        $pack = FamilyPack::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'profile' => $validated['profile'],
                'nutrient_summary' => $validated['nutrient_summary'] ?? [],
                'recommendations' => $validated['recommendations'],
                'monthly_total' => $validated['monthly_total'] ?? 0,
                'next_purchase_at' => $nextPurchaseAt,
                'next_reminder_at' => $nextReminderAt,
                'reminder_sent_at' => null,
                'status' => 'active',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Family pack saved successfully.',
            'data' => $this->serialize($pack->fresh()),
        ]);
    }

    private function serialize(FamilyPack $pack): array
    {
        return [
            'id' => $pack->id,
            'profile' => $pack->profile,
            'nutrient_summary' => $pack->nutrient_summary,
            'recommendations' => $pack->recommendations,
            'monthly_total' => (float) $pack->monthly_total,
            'next_purchase_at' => $pack->next_purchase_at?->toISOString(),
            'next_reminder_at' => $pack->next_reminder_at?->toISOString(),
            'reminder_sent_at' => $pack->reminder_sent_at?->toISOString(),
            'status' => $pack->status,
        ];
    }
}
