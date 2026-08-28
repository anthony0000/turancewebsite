<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        $this->moveProjectFileBlobs();
        $this->moveStaffContractBlobs();

        Schema::dropIfExists('project_file_contents');
        Schema::dropIfExists('staff_contract_document_contents');
    }

    public function down(): void
    {
        $this->createProjectFileContentsTable();
        $this->createStaffContractContentsTable();

        $this->restoreProjectFileBlobs();
        $this->restoreStaffContractBlobs();
    }

    private function moveProjectFileBlobs(): void
    {
        if (! Schema::hasTable('project_file_contents')) {
            return;
        }

        $disk = Storage::disk('public_uploads');
        $ids = DB::table('project_file_contents')->orderBy('project_file_id')->pluck('project_file_id');

        foreach ($ids as $projectFileId) {
            $row = DB::table('project_file_contents')
                ->join('project_files', 'project_files.id', '=', 'project_file_contents.project_file_id')
                ->where('project_file_contents.project_file_id', $projectFileId)
                ->first(['project_file_contents.contents', 'project_files.path']);

            if ($row === null) {
                throw new RuntimeException("Project file {$projectFileId} has blob data but no file metadata.");
            }

            $path = $this->safeRelativePath($row->path, "project file {$projectFileId}");

            if (! $disk->put($path, $row->contents)) {
                throw new RuntimeException("Project file {$projectFileId} could not be copied to private storage.");
            }
        }
    }

    private function moveStaffContractBlobs(): void
    {
        if (! Schema::hasTable('staff_contract_document_contents')) {
            return;
        }

        $disk = Storage::disk('public_uploads');
        $ids = DB::table('staff_contract_document_contents')->orderBy('staff_contract_id')->pluck('staff_contract_id');

        foreach ($ids as $contractId) {
            $row = DB::table('staff_contract_document_contents')
                ->join('staff_contracts', 'staff_contracts.id', '=', 'staff_contract_document_contents.staff_contract_id')
                ->where('staff_contract_document_contents.staff_contract_id', $contractId)
                ->first(['staff_contract_document_contents.contents', 'staff_contracts.signed_document_path']);

            if ($row === null) {
                throw new RuntimeException("Staff contract {$contractId} has blob data but no contract metadata.");
            }

            $path = $this->safeRelativePath($row->signed_document_path, "staff contract {$contractId}");

            if (! $disk->put($path, $row->contents)) {
                throw new RuntimeException("Staff contract {$contractId} could not be copied to private storage.");
            }
        }
    }

    private function restoreProjectFileBlobs(): void
    {
        $disk = Storage::disk('public_uploads');

        DB::table('project_files')
            ->select(['id', 'path'])
            ->whereNotNull('path')
            ->orderBy('id')
            ->get()
            ->each(function (object $file) use ($disk): void {
                $path = $this->safeRelativePath($file->path, "project file {$file->id}");

                if (! $disk->exists($path)) {
                    throw new RuntimeException("Project file {$file->id} is missing from private storage.");
                }

                DB::table('project_file_contents')->insert([
                    'project_file_id' => $file->id,
                    'contents' => $disk->get($path),
                ]);
            });
    }

    private function restoreStaffContractBlobs(): void
    {
        $disk = Storage::disk('public_uploads');

        DB::table('staff_contracts')
            ->select(['id', 'signed_document_path'])
            ->whereNotNull('signed_document_path')
            ->orderBy('id')
            ->get()
            ->each(function (object $contract) use ($disk): void {
                $path = $this->safeRelativePath($contract->signed_document_path, "staff contract {$contract->id}");

                if (! $disk->exists($path)) {
                    throw new RuntimeException("Signed document for contract {$contract->id} is missing from private storage.");
                }

                DB::table('staff_contract_document_contents')->insert([
                    'staff_contract_id' => $contract->id,
                    'contents' => $disk->get($path),
                ]);
            });
    }

    private function createProjectFileContentsTable(): void
    {
        if (Schema::hasTable('project_file_contents')) {
            return;
        }

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

    private function createStaffContractContentsTable(): void
    {
        if (Schema::hasTable('staff_contract_document_contents')) {
            return;
        }

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

    private function safeRelativePath(mixed $path, string $label): string
    {
        $path = ltrim(trim((string) $path), '/\\');

        $normalizedPath = str_replace('\\', '/', $path);

        if ($path === '' || in_array('..', explode('/', $normalizedPath), true)) {
            throw new RuntimeException("The {$label} has no safe storage path.");
        }

        return $path;
    }
};
