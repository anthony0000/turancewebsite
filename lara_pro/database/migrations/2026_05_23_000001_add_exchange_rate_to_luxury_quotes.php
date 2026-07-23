<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('luxury_quotes', function (Blueprint $table): void {
            $table->decimal('exchange_rate', 12, 4)->default(1370)->after('investment_amount');
        });
    }

    public function down(): void
    {
        Schema::table('luxury_quotes', function (Blueprint $table): void {
            $table->dropColumn('exchange_rate');
        });
    }
};
