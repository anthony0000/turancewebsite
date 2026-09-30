<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_visits', function (Blueprint $table) {
            $table->char('country_code', 2)->nullable()->after('ip_address');
            $table->string('country_name', 100)->nullable()->after('country_code');
            $table->index(['country_code', 'created_at']);
            $table->index('ip_address');
        });

        Schema::create('visit_country_lookups', function (Blueprint $table) {
            $table->string('ip_address', 45)->primary();
            $table->char('country_code', 2)->nullable();
            $table->string('country_name', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_country_lookups');

        Schema::table('page_visits', function (Blueprint $table) {
            $table->dropIndex(['country_code', 'created_at']);
            $table->dropIndex(['ip_address']);
            $table->dropColumn(['country_code', 'country_name']);
        });
    }
};
