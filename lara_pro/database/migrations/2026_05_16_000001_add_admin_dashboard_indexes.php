<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('luxury_quotes', function (Blueprint $table) {
            $table->index('created_at', 'luxury_quotes_created_at_index');
            $table->index(['template', 'created_at'], 'luxury_quotes_template_created_at_index');
            $table->index(['project_category', 'created_at'], 'luxury_quotes_project_category_created_at_index');
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->index('created_at', 'contact_messages_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropIndex('contact_messages_created_at_index');
        });

        Schema::table('luxury_quotes', function (Blueprint $table) {
            $table->dropIndex('luxury_quotes_project_category_created_at_index');
            $table->dropIndex('luxury_quotes_template_created_at_index');
            $table->dropIndex('luxury_quotes_created_at_index');
        });
    }
};
