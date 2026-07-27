<?php

namespace App\Support;

use App\Models\PromotionSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AnniversaryPromotion
{
    public function current(): array
    {
        $promo = config('seo.anniversary_promo', []);

        try {
            if (Schema::hasTable('promotion_settings')) {
                $stored = PromotionSetting::query()->where('key', 'anniversary')->first();

                if ($stored) {
                    $promo = [...$promo,
                        'enabled' => $stored->enabled,
                        'years' => $stored->years,
                        'discount_percent' => (float) $stored->discount_percent,
                        'code' => $stored->promo_code,
                        'ends_at' => $stored->ends_at?->toIso8601String(),
                    ];
                }
            }
        } catch (Throwable) {
            // Use the config fallback while the promotion migration is pending.
        }

        $endsAt = isset($promo['ends_at']) ? Carbon::parse($promo['ends_at']) : null;

        return [...$promo,
            'is_active' => ! empty($promo['enabled']) && $endsAt && now()->lt($endsAt),
            'ends_at_formatted' => $endsAt?->format('M d, Y g:i A'),
            'ends_at_input' => $endsAt?->format('Y-m-d\TH:i'),
        ];
    }
}
