<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->string('promo_code', 80)->nullable()->after('topic');
            $table->decimal('promo_discount_percent', 5, 2)->nullable()->after('promo_code');
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->dropColumn(['promo_code', 'promo_discount_percent']);
        });
    }
};
