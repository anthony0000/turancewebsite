<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->text('biggest_challenge')->nullable();
            $table->string('problem_duration')->nullable();
            $table->text('previous_attempts')->nullable();
            $table->text('message')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('contact_messages')->whereNull('message')->update(['message' => '']);

        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->dropColumn(['biggest_challenge', 'problem_duration', 'previous_attempts']);
            $table->text('message')->nullable(false)->change();
        });
    }
};
