<?php

use App\Models\Project;
use App\Models\ProjectCredential;
use App\Models\User;
use App\Support\AdminAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function credentialAdminSession(): array
{
    return [
        config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated') => true,
        'luxury_quote_admin_email' => 'admin@example.com',
        'admin_role' => AdminAccess::ROLE_ADMIN,
    ];
}

it('stores project credentials encrypted and keeps secrets out of the project page', function () {
    $project = Project::query()->create([
        'project_number' => 'TT-PRJ-CRED-001',
        'name' => 'Atlas Platform',
        'client_company' => 'Atlas Limited',
        'status' => 'active',
    ]);

    $this
        ->withSession(credentialAdminSession())
        ->post(route('admin.credentials.store', $project), [
            'service_name' => 'Production hosting',
            'credential_type' => 'Control panel login',
            'access_url' => 'https://hosting.example.com/login',
            'username' => 'atlas-admin@example.com',
            'secret' => 'Correct-Horse-Battery-99',
            'notes' => 'Rotate after client handover.',
        ])
        ->assertRedirect(route('admin.credentials.show', $project))
        ->assertSessionHas('status', 'Project credential stored securely.');

    $credential = ProjectCredential::query()->firstOrFail();
    $raw = DB::table('project_credentials')->where('id', $credential->id)->first();

    expect($credential->username)->toBe('atlas-admin@example.com')
        ->and($credential->secret)->toBe('Correct-Horse-Battery-99')
        ->and($credential->notes)->toBe('Rotate after client handover.')
        ->and($raw->username)->not->toContain('atlas-admin@example.com')
        ->and($raw->secret)->not->toContain('Correct-Horse-Battery-99')
        ->and($raw->notes)->not->toContain('Rotate after client handover.');

    $this
        ->withSession(credentialAdminSession())
        ->get(route('admin.credentials.show', $project))
        ->assertOk()
        ->assertSee('Project credentials')
        ->assertSee('Production hosting')
        ->assertSee('atlas-admin@example.com')
        ->assertSee('Download handover PDF')
        ->assertSee('data-credential-reveal', false)
        ->assertDontSee('Correct-Horse-Battery-99');

    $this
        ->withSession(credentialAdminSession())
        ->get(route('admin.credentials.index'))
        ->assertOk()
        ->assertSee('Secure access management')
        ->assertSee('Atlas Platform')
        ->assertSee('Project access vaults')
        ->assertSee(route('admin.credentials.show', $project), false);

    $this
        ->withSession(credentialAdminSession())
        ->get(route('admin.projects.show', $project))
        ->assertOk()
        ->assertDontSee('Project credentials')
        ->assertDontSee('Production hosting');
});

it('reveals, updates, and removes only credentials belonging to the requested project', function () {
    $project = Project::query()->create([
        'project_number' => 'TT-PRJ-CRED-002',
        'name' => 'Nova Commerce',
        'status' => 'active',
    ]);
    $otherProject = Project::query()->create([
        'project_number' => 'TT-PRJ-CRED-003',
        'name' => 'Other Project',
        'status' => 'active',
    ]);
    $credential = $project->credentials()->create([
        'service_name' => 'CMS admin',
        'credential_type' => 'Login',
        'username' => 'nova-admin',
        'secret' => 'Initial-Secret',
    ]);

    $revealResponse = $this
        ->withSession(credentialAdminSession())
        ->postJson(route('admin.credentials.reveal', [$project, $credential]))
        ->assertOk()
        ->assertJson(['secret' => 'Initial-Secret']);

    expect($revealResponse->headers->get('Cache-Control'))
        ->toContain('private')
        ->toContain('no-store')
        ->toContain('no-cache')
        ->toContain('must-revalidate');

    $this
        ->withSession(credentialAdminSession())
        ->put(route('admin.credentials.update', [$project, $credential]), [
            'service_name' => 'CMS administrator',
            'credential_type' => 'WordPress login',
            'access_url' => 'https://nova.example.com/wp-admin',
            'username' => 'nova-owner',
            'secret' => '',
            'notes' => 'Use only for content administration.',
        ])
        ->assertRedirect(route('admin.credentials.show', $project));

    expect($credential->refresh()->service_name)->toBe('CMS administrator')
        ->and($credential->secret)->toBe('Initial-Secret');

    $this
        ->withSession(credentialAdminSession())
        ->postJson(route('admin.credentials.reveal', [$otherProject, $credential]))
        ->assertNotFound();

    $this
        ->withSession(credentialAdminSession())
        ->delete(route('admin.credentials.destroy', [$project, $credential]))
        ->assertRedirect(route('admin.credentials.show', $project));

    expect(ProjectCredential::query()->count())->toBe(0);
});

it('downloads a confidential credential collation PDF on the Turance letterhead', function () {
    $project = Project::query()->create([
        'project_number' => 'TT-PRJ-CRED-004',
        'name' => 'Orion Website',
        'client_name' => 'Ada Nwosu',
        'client_company' => 'Orion Ventures',
        'status' => 'completed',
    ]);
    $project->credentials()->create([
        'service_name' => 'Domain registrar',
        'credential_type' => 'Account login',
        'access_url' => 'https://domains.example.com',
        'username' => 'orion-owner',
        'secret' => 'Registrar-Secret',
        'notes' => 'Primary domain account.',
    ]);

    $response = $this
        ->withSession(credentialAdminSession())
        ->get(route('admin.credentials.pdf', $project));

    $response
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertHeader('Content-Disposition', 'attachment; filename=tt-prj-cred-004-credential-collation.pdf');

    expect(substr($response->getContent(), 0, 4))->toBe('%PDF')
        ->and(strlen($response->getContent()))->toBeGreaterThan(1000)
        ->and($response->headers->get('Cache-Control'))->toContain('private')
        ->toContain('no-store')
        ->toContain('no-cache')
        ->toContain('must-revalidate');
});

it('keeps the credential vault and exports restricted to full administrators', function () {
    $member = User::factory()->create([
        'role' => AdminAccess::ROLE_SUBACCOUNT,
        'permissions' => ['projects'],
        'is_active' => true,
    ]);
    $project = Project::query()->create([
        'project_number' => 'TT-PRJ-CRED-005',
        'name' => 'Restricted Vault',
        'status' => 'active',
    ]);
    $project->members()->attach($member->id);
    $credential = $project->credentials()->create([
        'service_name' => 'Production database',
        'credential_type' => 'Database password',
        'secret' => 'Database-Secret',
    ]);
    $session = [
        config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated') => true,
        'admin_user_id' => $member->id,
        'luxury_quote_admin_email' => $member->email,
        'admin_role' => AdminAccess::ROLE_SUBACCOUNT,
        'admin_permissions' => ['projects'],
    ];

    $this
        ->withSession($session)
        ->get(route('admin.projects.show', $project))
        ->assertOk()
        ->assertDontSee('Project credentials')
        ->assertDontSee('Production database')
        ->assertDontSee('Database-Secret');

    $this->withSession($session)->get(route('admin.credentials.index'))->assertForbidden();
    $this->withSession($session)->get(route('admin.credentials.show', $project))->assertForbidden();
    $this->withSession($session)->postJson(route('admin.credentials.reveal', [$project, $credential]))->assertForbidden();
    $this->withSession($session)->get(route('admin.credentials.pdf', $project))->assertForbidden();
    $this->withSession($session)->post(route('admin.credentials.store', $project), [
        'service_name' => 'Attempted entry',
        'credential_type' => 'Login',
        'secret' => 'Blocked',
    ])->assertForbidden();
});
