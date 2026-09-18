<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_payment_receipts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->restrictOnDelete();
            $table->foreignId('project_payment_invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('staff_contract_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payment_type', 40)->index();
            $table->string('counterparty')->nullable();
            $table->decimal('amount', 14, 2);
            $table->string('currency', 3);
            $table->date('paid_on')->index();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->string('receipt_path');
            $table->string('receipt_original_name');
            $table->string('receipt_mime', 127)->nullable();
            $table->unsignedBigInteger('receipt_size')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['project_id', 'paid_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_payment_receipts');
    }
};
