<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('luxury_quotes', function (Blueprint $table): void {
            $table->json('line_items')->nullable()->after('scope_items');
        });
    }

    public function down(): void
    {
        Schema::table('luxury_quotes', function (Blueprint $table): void {
            $table->dropColumn('line_items');
        });
    }
};
