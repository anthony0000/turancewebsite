<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_credentials', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('service_name');
            $table->string('credential_type', 80)->default('Login');
            $table->string('access_url', 2048)->nullable();
            $table->longText('username')->nullable();
            $table->longText('secret');
            $table->longText('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['project_id', 'service_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_credentials');
    }
};
