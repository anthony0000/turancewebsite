<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_contracts', function (Blueprint $table): void {
            $table->foreignId('luxury_quote_id')
                ->nullable()
                ->after('project_id')
                ->constrained('luxury_quotes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('staff_contracts', function (Blueprint $table): void {
            $table->dropForeign(['luxury_quote_id']);
            $table->dropColumn('luxury_quote_id');
        });
    }
};
