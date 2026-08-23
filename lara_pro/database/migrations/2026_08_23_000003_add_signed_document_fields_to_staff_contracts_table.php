<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_contracts', function (Blueprint $table): void {
            $table->string('signed_document_path')->nullable()->after('staff_signed_date');
            $table->string('signed_document_original_name')->nullable()->after('signed_document_path');
            $table->string('signed_document_mime', 127)->nullable()->after('signed_document_original_name');
            $table->unsignedBigInteger('signed_document_size')->nullable()->after('signed_document_mime');
        });
    }

    public function down(): void
    {
        Schema::table('staff_contracts', function (Blueprint $table): void {
            $table->dropColumn([
                'signed_document_path',
                'signed_document_original_name',
                'signed_document_mime',
                'signed_document_size',
            ]);
        });
    }
};
