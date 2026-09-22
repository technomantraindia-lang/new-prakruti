<?php

namespace App\Services;

use App\Models\Setting;

class BillDiscountService
{
    public function rules(): array
    {
        $enabled = filter_var(Setting::get('bill_discount_enabled', false), FILTER_VALIDATE_BOOLEAN);
        $savedRules = json_decode((string) Setting::get('bill_discount_rules', '[]'), true);

        if (! is_array($savedRules) || empty($savedRules)) {
            $savedRules = [[
                'min_amount' => Setting::get('bill_discount_min_amount', 0),
                'percent' => Setting::get('bill_discount_percent', 0),
            ]];
        }

        $rules = collect($savedRules)
            ->map(function ($rule) {
                return [
                    'min_amount' => max(0, (float) ($rule['min_amount'] ?? 0)),
                    'percent' => max(0, min(100, (float) ($rule['percent'] ?? 0))),
                ];
            })
            ->filter(fn ($rule) => $rule['min_amount'] > 0 && $rule['percent'] > 0)
            ->sortByDesc('min_amount')
            ->values()
            ->all();

        return ['enabled' => $enabled, 'rules' => $rules];
    }

    public function calculate(float $subtotal): array
    {
        $ruleSet = $this->rules();
        $matchedRule = null;
        $nextRule = null;

        if ($ruleSet['enabled']) {
            foreach ($ruleSet['rules'] as $rule) {
                if ($subtotal >= $rule['min_amount']) {
                    $matchedRule = $rule;
                    break;
                }
            }

            foreach (array_reverse($ruleSet['rules']) as $rule) {
                if ($subtotal < $rule['min_amount']) {
                    $nextRule = $rule;
                    break;
                }
            }
        }

        $applied = (bool) $matchedRule;
        $percent = $matchedRule['percent'] ?? 0;
        $minAmount = $matchedRule['min_amount'] ?? 0;

        $discount = $applied ? round($subtotal * ($percent / 100), 2) : 0.0;

        return [
            'enabled' => $ruleSet['enabled'],
            'rules' => $ruleSet['rules'],
            'min_amount' => $minAmount,
            'percent' => $percent,
            'applied' => $applied,
            'discount' => min($discount, $subtotal),
            'next_rule' => $nextRule,
            'amount_needed' => $nextRule ? round(max(0, $nextRule['min_amount'] - $subtotal), 2) : 0,
            'label' => $applied
                ? "{$percent}% bill discount above Rs. {$minAmount}"
                : null,
            'offer_text' => $applied
                ? "You saved Rs. {$discount} with {$percent}% bill discount"
                : ($nextRule ? "Add Rs. " . round(max(0, $nextRule['min_amount'] - $subtotal), 2) . " more to unlock {$nextRule['percent']}% bill discount" : null),
        ];
    }
}
