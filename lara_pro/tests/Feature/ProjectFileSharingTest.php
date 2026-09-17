<?php

use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\User;
use App\Support\AdminAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function projectAdminSession(): array
{
    return [
        config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated') => true,
        'luxury_quote_admin_email' => 'admin@example.com',
        'admin_role' => 'admin',
    ];
}

it('does not keep file payload tables in the database', function () {
    expect(Schema::hasTable('project_file_contents'))->toBeFalse()
        ->and(Schema::hasTable('staff_contract_document_contents'))->toBeFalse();
});

it('shows the company and project document library', function () {
    $project = Project::query()->create([
        'project_number' => 'TT-PRJ-CHART-001',
        'name' => 'Northstar Client Portal',
        'client_company' => 'Asterion Holdings',
        'status' => 'active',
    ]);

    ProjectFile::query()->create([
        'project_id' => $project->id,
        'original_name' => 'northstar-brief.pdf',
        'path' => 'projects/files/'.$project->id.'/brief.pdf',
        'mime_type' => 'application/pdf',
        'size' => 1024,
    ]);

    $this
        ->withSession(projectAdminSession())
        ->get(route('admin.projects.index'))
        ->assertOk()
        ->assertSee('File management')
        ->assertSee('Company documents')
        ->assertSee('Browse by location')
        ->assertSee('Recent files')
        ->assertSee('Add to the library')
        ->assertSee('role="dialog"', false)
        ->assertSee('data-upload-open', false)
        ->assertSee('data-file-dropzone', false)
        ->assertSee('data-file-preview', false)
        ->assertSee('data-file-preview-modal', false)
        ->assertDontSee('target="_blank" rel="noopener" title="Preview"', false)
        ->assertSee('name="files[]"', false)
        ->assertSee('multiple', false)
        ->assertSee('Add folder and description')
        ->assertSee('Choose a project')
        ->assertSee('All documents')
        ->assertSee('Download')
        ->assertSee('Update')
        ->assertSee('Remove')
        ->assertSee('file-access-badge--private', false)
        ->assertSee('Northstar Client Portal');

    $this
        ->withSession(projectAdminSession())
        ->get(route('admin.projects.show', $project))
        ->assertOk()
        ->assertSee('data-file-preview', false)
        ->assertSee('data-file-preview-modal', false)
        ->assertSee('Create share link');
});

it('stores and filters company documents without a project record', function () {
    Storage::fake('public_uploads');

    $project = Project::query()->create([
        'project_number' => 'TT-PRJ-FILTER-001',
        'name' => 'Website Delivery',
        'status' => 'active',
    ]);
    ProjectFile::query()->create([
        'project_id' => $project->id,
        'document_scope' => 'project',
        'original_name' => 'website-handover.pdf',
        'path' => 'projects/files/'.$project->id.'/handover.pdf',
        'mime_type' => 'application/pdf',
        'size' => 1024,
    ]);

    $this
        ->withSession(projectAdminSession())
        ->postJson(route('admin.projects.files.external.store'), [
            'document_scope' => 'company',
            'folder' => 'Finance',
            'file' => UploadedFile::fake()->create('annual-budget.xlsx', 64, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            'description' => 'Approved company budget.',
        ])
        ->assertCreated()
        ->assertJsonPath('data.project_id', null)
        ->assertJsonPath('data.document_scope', 'company')
        ->assertJsonPath('data.folder', 'Finance');

    $companyDocument = ProjectFile::query()->where('document_scope', 'company')->firstOrFail();

    expect($companyDocument->project_id)->toBeNull()
        ->and($companyDocument->locationLabel())->toBe('Company / Finance')
        ->and($companyDocument->path)->toContain('/company/');

    Storage::disk('public_uploads')->assertExists($companyDocument->path);

    $this
        ->withSession(projectAdminSession())
        ->get(route('admin.projects.index', ['scope' => 'company', 'q' => 'budget']))
        ->assertOk()
        ->assertSee('annual-budget.xlsx')
        ->assertSee('Finance')
        ->assertDontSee('website-handover.pdf');

    $this
        ->withSession(projectAdminSession())
        ->post(route('admin.projects.files.share', $companyDocument), ['return_to' => 'index'])
        ->assertRedirect(route('admin.projects.index'));

    $companyDocument->refresh();
    $this->get(route('project-files.share', $companyDocument->share_token))
        ->assertOk()
        ->assertSee('Company documents')
        ->assertSee('annual-budget.xlsx');
});

it('uploads an external project file with ajax even when the project has no staff contract', function () {
    Storage::fake('public_uploads');

    $project = Project::query()->create([
        'project_number' => 'TT-PRJ-EXTERNAL-001',
        'name' => 'External Files Project',
        'status' => 'active',
    ]);

    $this
        ->withSession(projectAdminSession())
        ->postJson(route('admin.projects.files.external.store'), [
            'project_id' => $project->id,
            'file' => UploadedFile::fake()->create('client-reference.docx', 64, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            'description' => 'Reference material for the staff handoff.',
        ])
        ->assertCreated()
        ->assertJsonPath('data.project_id', $project->id)
        ->assertJsonPath('data.original_name', 'client-reference.docx');

    $projectFile = ProjectFile::query()->firstOrFail();

    expect($projectFile->project_id)->toBe($project->id)
        ->and($projectFile->original_name)->toBe('client-reference.docx')
        ->and($projectFile->description)->toBe('Reference material for the staff handoff.');

    Storage::disk('public_uploads')->assertExists($projectFile->path);
});

it('uploads multiple project files in one ajax request', function () {
    Storage::fake('public_uploads');

    $project = Project::query()->create([
        'project_number' => 'TT-PRJ-BATCH-001',
        'name' => 'Batch Upload Project',
        'status' => 'active',
    ]);

    $this
        ->withSession(projectAdminSession())
        ->postJson(route('admin.projects.files.external.store'), [
            'document_scope' => 'project',
            'project_id' => $project->id,
            'folder' => 'Handover',
            'files' => [
                UploadedFile::fake()->create('project-brief.pdf', 24, 'application/pdf'),
                UploadedFile::fake()->create('project-budget.xlsx', 32, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            ],
        ])
        ->assertCreated()
        ->assertJsonPath('data.count', 2)
        ->assertJsonPath('data.files.0.original_name', 'project-brief.pdf')
        ->assertJsonPath('data.files.1.original_name', 'project-budget.xlsx');

    $projectFiles = ProjectFile::query()->where('project_id', $project->id)->orderBy('id')->get();

    expect($projectFiles)->toHaveCount(2)
        ->and($projectFiles->pluck('folder')->all())->toBe(['Handover', 'Handover']);

    $projectFiles->each(fn (ProjectFile $file) => Storage::disk('public_uploads')->assertExists($file->path));
});

it('limits each upload batch to ten files', function () {
    Storage::fake('public_uploads');

    $files = collect(range(1, 11))
        ->map(fn (int $number) => UploadedFile::fake()->create("document-{$number}.pdf", 4, 'application/pdf'))
        ->all();

    $this
        ->withSession(projectAdminSession())
        ->postJson(route('admin.projects.files.external.store'), [
            'document_scope' => 'company',
            'files' => $files,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('files');

    expect(ProjectFile::query()->count())->toBe(0);
});

it('stores project files privately, shares one file, and can revoke the link', function () {
    Storage::fake('public_uploads');

    $project = Project::query()->create([
        'project_number' => 'TT-PRJ-SHARE-001',
        'name' => 'Private Handoff Project',
        'status' => 'active',
    ]);

    $file = UploadedFile::fake()->create('approved-reference.pdf', 128, 'application/pdf');

    $this
        ->withSession(projectAdminSession())
        ->post(route('admin.projects.files.store', $project), [
            'file' => $file,
            'description' => 'Approved reference file for the final handoff.',
        ])
        ->assertRedirect(route('admin.projects.show', $project));

    $projectFile = ProjectFile::query()->firstOrFail();

    expect($projectFile->original_name)->toBe('approved-reference.pdf')
        ->and($projectFile->is_shared)->toBeFalse()
        ->and($projectFile->description)->toBe('Approved reference file for the final handoff.');

    Storage::disk('public_uploads')->assertExists($projectFile->path);
    Storage::disk('public')->assertMissing($projectFile->path);

    $this
        ->withSession(projectAdminSession())
        ->get(route('admin.projects.files.download', $projectFile))
        ->assertOk()
        ->assertHeader('Content-Disposition', 'attachment; filename=approved-reference.pdf');

    $this
        ->withSession(projectAdminSession())
        ->get(route('admin.projects.files.preview', $projectFile))
        ->assertOk()
        ->assertHeader('Content-Disposition', 'inline; filename="approved-reference.pdf"');

    $this
        ->withSession(projectAdminSession())
        ->post(route('admin.projects.files.share', $projectFile))
        ->assertRedirect(route('admin.projects.show', $project));

    $projectFile->refresh();

    expect($projectFile->is_shared)->toBeTrue()
        ->and($projectFile->share_token)->toHaveLength(64)
        ->and($projectFile->shared_at)->not->toBeNull();

    $this
        ->get(route('project-files.share', $projectFile->share_token))
        ->assertOk()
        ->assertSee('Private Handoff Project')
        ->assertSee('approved-reference.pdf')
        ->assertSee('Download file');

    $this
        ->get(route('project-files.download', $projectFile->share_token))
        ->assertOk()
        ->assertHeader('Content-Disposition', 'attachment; filename=approved-reference.pdf');

    $this
        ->withSession(projectAdminSession())
        ->post(route('admin.projects.files.share', $projectFile))
        ->assertRedirect(route('admin.projects.show', $project));

    $projectFile->refresh();

    expect($projectFile->is_shared)->toBeFalse();

    $this->get(route('project-files.share', $projectFile->share_token))->assertNotFound();
});

it('updates project file details and replaces the stored file without creating a duplicate', function () {
    Storage::fake('public_uploads');

    $project = Project::query()->create([
        'project_number' => 'TT-PRJ-UPDATE-001',
        'name' => 'File Update Project',
        'status' => 'active',
    ]);

    $oldPath = 'projects/files/'.$project->id.'/original.pdf';
    Storage::disk('public_uploads')->put($oldPath, 'original project file');
    $projectFile = ProjectFile::query()->create([
        'project_id' => $project->id,
        'original_name' => 'original.pdf',
        'path' => $oldPath,
        'mime_type' => 'application/pdf',
        'size' => 20,
        'description' => 'Original description',
    ]);

    $this
        ->withSession(projectAdminSession())
        ->post(route('admin.projects.files.update', $projectFile), [
            '_method' => 'PUT',
            'file' => UploadedFile::fake()->createWithContent('updated-reference.txt', 'updated document bytes'),
            'description' => 'Updated reference material.',
        ], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])
        ->assertOk()
        ->assertJsonPath('data.original_name', 'updated-reference.txt')
        ->assertJsonPath('data.description', 'Updated reference material.');

    $projectFile->refresh();

    expect(ProjectFile::query()->count())->toBe(1)
        ->and($projectFile->original_name)->toBe('updated-reference.txt')
        ->and($projectFile->description)->toBe('Updated reference material.')
        ->and($projectFile->path)->not->toBe($oldPath);

    Storage::disk('public_uploads')->assertMissing($oldPath);
    expect(Storage::disk('public_uploads')->get($projectFile->path))
        ->toBe('updated document bytes');

    $this
        ->withSession(projectAdminSession())
        ->get(route('admin.projects.files.download', $projectFile))
        ->assertOk()
        ->assertHeader('Content-Disposition', 'attachment; filename=updated-reference.txt');
});

it('removes the stored project file and keeps project access permission scoped', function () {
    Storage::fake('public_uploads');

    $project = Project::query()->create([
        'project_number' => 'TT-PRJ-REMOVE-001',
        'name' => 'Removal Project',
        'status' => 'active',
    ]);

    $path = 'projects/files/'.$project->id.'/remove-me.pdf';
    Storage::disk('public_uploads')->put($path, 'private project file');
    $projectFile = ProjectFile::query()->create([
        'project_id' => $project->id,
        'original_name' => 'remove-me.pdf',
        'path' => $path,
        'mime_type' => 'application/pdf',
        'size' => 20,
    ]);

    $this
        ->withSession([
            ...projectAdminSession(),
            'admin_role' => 'subaccount',
            'admin_permissions' => ['staff-contracts'],
        ])
        ->get(route('admin.projects.index'))
        ->assertForbidden();

    $this
        ->withSession(projectAdminSession())
        ->delete(route('admin.projects.files.destroy', $projectFile))
        ->assertRedirect(route('admin.projects.show', $project));

    expect(ProjectFile::query()->find($projectFile->id))->toBeNull();
    Storage::disk('public_uploads')->assertMissing($path);
});

it('lets subaccounts view projects while limiting project file access separately', function () {
    Storage::fake('public_uploads');

    $project = Project::query()->create([
        'project_number' => 'TT-PRJ-LIMIT-001',
        'name' => 'Limited File Access Project',
        'status' => 'active',
    ]);

    $path = 'projects/files/'.$project->id.'/restricted.pdf';
    Storage::disk('public_uploads')->put($path, 'restricted project file');
    $projectFile = ProjectFile::query()->create([
        'project_id' => $project->id,
        'original_name' => 'restricted.pdf',
        'path' => $path,
        'mime_type' => 'application/pdf',
        'size' => 24,
    ]);

    $limitedSession = [
        config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated') => true,
        'luxury_quote_admin_email' => 'subaccount@example.com',
        'admin_role' => 'subaccount',
        'admin_permissions' => ['projects'],
    ];

    $this
        ->withSession($limitedSession)
        ->get(route('admin.projects.index'))
        ->assertOk()
        ->assertSee('File access is limited for this account.')
        ->assertDontSee('Limited File Access Project')
        ->assertDontSee('Upload a file for the project team');

    $this
        ->withSession($limitedSession)
        ->get(route('admin.projects.show', $project))
        ->assertForbidden();

    $this
        ->withSession($limitedSession)
        ->post(route('admin.projects.files.external.store'), [
            'project_id' => $project->id,
            'file' => UploadedFile::fake()->create('blocked.pdf', 10, 'application/pdf'),
        ])
        ->assertForbidden();

    $this
        ->withSession($limitedSession)
        ->get(route('admin.projects.files.download', $projectFile))
        ->assertForbidden();
});

it('shows limited members shared project files only', function () {
    Storage::fake('public_uploads');

    $member = User::factory()->create([
        'role' => AdminAccess::ROLE_SUBACCOUNT,
        'permissions' => ['projects', 'project-files'],
        'is_active' => true,
    ]);
    $project = Project::query()->create([
        'project_number' => 'TT-PRJ-SHARED-001',
        'name' => 'Shared Files Project',
        'status' => 'active',
    ]);
    $project->members()->attach($member->id, ['role' => 'member']);
    $sharedPath = 'projects/files/'.$project->id.'/shared.pdf';
    $privatePath = 'projects/files/'.$project->id.'/private.pdf';
    Storage::disk('public_uploads')->put($sharedPath, 'shared project file');
    Storage::disk('public_uploads')->put($privatePath, 'private project file');
    $sharedFile = ProjectFile::query()->create([
        'project_id' => $project->id,
        'original_name' => 'shared.pdf',
        'path' => $sharedPath,
        'mime_type' => 'application/pdf',
        'size' => 20,
        'is_shared' => true,
        'share_token' => str_repeat('s', 64),
        'shared_at' => now(),
    ]);
    $privateFile = ProjectFile::query()->create([
        'project_id' => $project->id,
        'original_name' => 'private.pdf',
        'path' => $privatePath,
        'mime_type' => 'application/pdf',
        'size' => 21,
    ]);
    $session = [
        config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated') => true,
        'admin_user_id' => $member->id,
        'admin_user_name' => $member->name,
        'admin_role' => $member->role,
        'admin_permissions' => ['projects'],
        'luxury_quote_admin_email' => $member->email,
    ];

    $this->withSession($session)
        ->get(route('admin.projects.index'))
        ->assertOk()
        ->assertSee('shared.pdf')
        ->assertDontSee('private.pdf')
        ->assertSee('Shared project files')
        ->assertDontSee('Manage files')
        ->assertDontSee('Projects by status')
        ->assertDontSee('Files by project')
        ->assertDontSee('Shared files only');

    $this->withSession($session)
        ->get(route('admin.projects.show', $project))
        ->assertOk()
        ->assertSee('shared.pdf')
        ->assertDontSee('private.pdf')
        ->assertSee('Shared project files')
        ->assertDontSee('Create share link');

    $this->withSession($session)->get(route('admin.projects.files.download', $sharedFile))->assertOk();
    $this->withSession($session)->get(route('admin.projects.files.download', $privateFile))->assertForbidden();
    $this->withSession($session)->post(route('admin.projects.files.store', $project), [
        'file' => UploadedFile::fake()->create('blocked.pdf', 10, 'application/pdf'),
    ])->assertForbidden();
    $this->withSession($session)->post(route('admin.projects.files.share', $sharedFile))->assertForbidden();
    $this->withSession($session)->put(route('admin.projects.files.update', $sharedFile), [
        'description' => 'Blocked update',
    ])->assertForbidden();
    $this->withSession($session)->delete(route('admin.projects.files.destroy', $sharedFile))->assertForbidden();
});
