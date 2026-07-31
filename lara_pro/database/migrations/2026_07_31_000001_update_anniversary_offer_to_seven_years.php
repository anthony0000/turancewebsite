<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('promotion_settings')) {
            return;
        }

        DB::table('promotion_settings')
            ->where('key', 'anniversary')
            ->update([
                'years' => 7,
                'updated_at' => now(),
            ]);

        DB::table('promotion_settings')
            ->where('key', 'anniversary')
            ->where('promo_code', 'TURANCE10')
            ->update([
                'promo_code' => 'TURANCE7',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('promotion_settings')) {
            return;
        }

        DB::table('promotion_settings')
            ->where('key', 'anniversary')
            ->where('years', 7)
            ->update([
                'years' => 10,
                'updated_at' => now(),
            ]);

        DB::table('promotion_settings')
            ->where('key', 'anniversary')
            ->where('promo_code', 'TURANCE7')
            ->update([
                'promo_code' => 'TURANCE10',
                'updated_at' => now(),
            ]);
    }
};
