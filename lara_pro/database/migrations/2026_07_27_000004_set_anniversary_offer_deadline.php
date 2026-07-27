<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('promotion_settings')) {
            DB::table('promotion_settings')
                ->where('key', 'anniversary')
                ->update(['ends_at' => now()->addDays(30)->endOfDay(), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // The previous deadline was campaign-specific and is intentionally not restored.
    }
};
