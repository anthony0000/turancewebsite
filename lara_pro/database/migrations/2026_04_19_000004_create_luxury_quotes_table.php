<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luxury_quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique();
            $table->string('template', 50);
            $table->string('project_category');
            $table->string('company_name');
            $table->string('company_industry')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_title')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('project_title');
            $table->text('executive_summary');
            $table->decimal('investment_amount', 10, 2);
            $table->string('timeline');
            $table->date('valid_until');
            $table->json('scope_items');
            $table->json('outcomes')->nullable();
            $table->json('milestones')->nullable();
            $table->json('optional_addons')->nullable();
            $table->text('intro_message')->nullable();
            $table->text('closing_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('luxury_quotes');
    }
};
