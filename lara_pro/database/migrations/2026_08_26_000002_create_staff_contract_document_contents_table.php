<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_contract_document_contents', function (Blueprint $table): void {
            $table->foreignId('staff_contract_id')
                ->primary()
                ->constrained('staff_contracts')
                ->cascadeOnDelete();
            $table->binary('contents');
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE staff_contract_document_contents MODIFY contents LONGBLOB NOT NULL');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_contract_document_contents');
    }
};
