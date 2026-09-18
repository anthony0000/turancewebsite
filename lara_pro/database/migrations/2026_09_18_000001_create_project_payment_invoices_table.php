<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_payment_invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->restrictOnDelete();
            $table->foreignId('original_invoice_id')->nullable()->constrained('luxury_quotes')->nullOnDelete();
            $table->string('invoice_number')->unique();
            $table->string('status', 20)->default('unpaid')->index();
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->string('payment_terms')->default('Due upon receipt');
            $table->string('original_invoice_number')->nullable();

            $table->string('client_company');
            $table->string('client_name')->nullable();
            $table->string('client_title')->nullable();
            $table->string('client_email')->nullable();
            $table->string('client_phone', 80)->nullable();
            $table->string('project_title');
            $table->string('project_category')->nullable();
            $table->text('description');
            $table->string('payment_description');

            $table->string('currency', 3)->default('USD');
            $table->string('local_currency', 3)->default('NGN');
            $table->decimal('contract_value', 14, 2);
            $table->decimal('completion_percentage', 5, 2);
            $table->decimal('subtotal_amount', 14, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2);
            $table->decimal('exchange_rate', 14, 4)->default(1370);

            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number', 100)->nullable();
            $table->string('prepared_by')->nullable();
            $table->text('notes')->nullable();

            $table->timestamp('paid_at')->nullable()->index();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('payment_reference')->nullable();
            $table->text('payment_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['project_id', 'issue_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_payment_invoices');
    }
};
