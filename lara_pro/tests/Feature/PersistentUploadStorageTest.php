<?php

use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\StaffContract;
use App\Support\PersistentUploadStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('moves a legacy upload into persistent storage when it is accessed', function () {
    Storage::fake(PersistentUploadStorage::DISK);
    Storage::fake(PersistentUploadStorage::LEGACY_DISK);

    $path = 'projects/files/12/reference.pdf';
    Storage::disk(PersistentUploadStorage::LEGACY_DISK)->put($path, 'legacy project document');

    expect(PersistentUploadStorage::exists($path))->toBeTrue();

    $absolutePath = PersistentUploadStorage::absolutePath($path);

    expect($absolutePath)->not->toBeNull()
        ->and(file_get_contents($absolutePath))->toBe('legacy project document');
    Storage::disk(PersistentUploadStorage::DISK)->assertExists($path);
    Storage::disk(PersistentUploadStorage::LEGACY_DISK)->assertMissing($path);
});

it('migrates all recorded project and signed-contract uploads before deployment', function () {
    Storage::fake(PersistentUploadStorage::DISK);
    Storage::fake(PersistentUploadStorage::LEGACY_DISK);

    $project = Project::query()->create([
        'project_number' => 'TT-PRJ-PERSIST-001',
        'name' => 'Persistent Files Project',
        'status' => 'active',
    ]);

    $projectPath = 'projects/files/'.$project->id.'/handover.pdf';
    $contractPath = 'staff-contracts/signed-documents/signed-contract.pdf';

    ProjectFile::query()->create([
        'project_id' => $project->id,
        'original_name' => 'handover.pdf',
        'path' => $projectPath,
        'mime_type' => 'application/pdf',
        'size' => 24,
    ]);

    StaffContract::query()->create([
        'project_id' => $project->id,
        'contract_number' => 'TT-SC-PERSIST-001',
        'staff_name' => 'Alex Morgan',
        'staff_role' => 'Designer',
        'payment_terms' => 'Payment after approval.',
        'scope_of_work' => 'Complete the approved design deliverables.',
        'terms' => 'The agreed project terms remain in force.',
        'company_name' => 'Turance Technologies',
        'signed_document_path' => $contractPath,
        'signed_document_original_name' => 'signed-contract.pdf',
        'signed_document_mime' => 'application/pdf',
        'signed_document_size' => 25,
    ]);

    Storage::disk(PersistentUploadStorage::LEGACY_DISK)->put($projectPath, 'project handover document');
    Storage::disk(PersistentUploadStorage::LEGACY_DISK)->put($contractPath, 'signed staff contract');

    $this->artisan('uploads:migrate-persistent')
        ->expectsOutputToContain('2 moved, 0 already stored')
        ->assertSuccessful();

    Storage::disk(PersistentUploadStorage::DISK)->assertExists($projectPath);
    Storage::disk(PersistentUploadStorage::DISK)->assertExists($contractPath);
    Storage::disk(PersistentUploadStorage::LEGACY_DISK)->assertMissing($projectPath);
    Storage::disk(PersistentUploadStorage::LEGACY_DISK)->assertMissing($contractPath);
});
