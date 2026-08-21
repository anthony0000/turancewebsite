<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->string('project_number')->unique();
            $table->string('name');
            $table->string('client_name')->nullable();
            $table->string('client_company')->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('staff_contracts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('contract_number')->unique();
            $table->string('status', 30)->default('draft')->index();
            $table->string('staff_name');
            $table->string('staff_email')->nullable();
            $table->string('staff_phone', 80)->nullable();
            $table->string('staff_role');
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->string('currency', 3)->default('NGN');
            $table->decimal('agreed_fee', 12, 2)->default(0);
            $table->text('payment_terms');
            $table->longText('scope_of_work');
            $table->longText('terms');
            $table->string('company_name');
            $table->string('company_signatory_name')->nullable();
            $table->string('company_signatory_title')->nullable();
            $table->date('company_signed_date')->nullable();
            $table->string('staff_signatory_name')->nullable();
            $table->date('staff_signed_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_contracts');
        Schema::dropIfExists('projects');
    }
};
