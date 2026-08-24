<?php

use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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

it('shows project portfolio charts and the file workspace entry point', function () {
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
        ->assertSee('Projects by status')
        ->assertSee('Files by project')
        ->assertSee('Northstar Client Portal');

    $this
        ->withSession(projectAdminSession())
        ->get(route('admin.projects.show', $project))
        ->assertOk()
        ->assertSee('Create share link');
});

it('stores project files privately, shares one file, and can revoke the link', function () {
    Storage::fake('local');

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

    Storage::disk('local')->assertExists($projectFile->path);
    Storage::disk('public')->assertMissing($projectFile->path);

    $this
        ->withSession(projectAdminSession())
        ->get(route('admin.projects.files.download', $projectFile))
        ->assertOk()
        ->assertHeader('Content-Disposition', 'attachment; filename=approved-reference.pdf');

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

it('removes the stored project file and keeps project access permission scoped', function () {
    Storage::fake('local');

    $project = Project::query()->create([
        'project_number' => 'TT-PRJ-REMOVE-001',
        'name' => 'Removal Project',
        'status' => 'active',
    ]);

    $path = 'projects/files/'.$project->id.'/remove-me.pdf';
    Storage::disk('local')->put($path, 'private project file');
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
    Storage::disk('local')->assertMissing($path);
});
