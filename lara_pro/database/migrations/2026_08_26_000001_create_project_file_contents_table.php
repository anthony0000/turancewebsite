<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_file_contents', function (Blueprint $table): void {
            $table->foreignId('project_file_id')
                ->primary()
                ->constrained('project_files')
                ->cascadeOnDelete();
            $table->binary('contents');
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE project_file_contents MODIFY contents LONGBLOB NOT NULL');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_file_contents');
    }
};
