<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('luxury_quotes', function (Blueprint $table): void {
            $table->decimal('discount_percent', 5, 2)->default(0)->after('investment_amount');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_percent');
            $table->string('promo_code', 80)->nullable()->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('luxury_quotes', function (Blueprint $table): void {
            $table->dropColumn(['discount_percent', 'discount_amount', 'promo_code']);
        });
    }
};
