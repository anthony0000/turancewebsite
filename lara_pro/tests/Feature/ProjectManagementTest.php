<?php

use App\Models\BoardColumn;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Support\AdminAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function projectManagementSession(User $user): array
{
    return [
        config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated') => true,
        'admin_user_id' => $user->id,
        'admin_user_name' => $user->name,
        'admin_role' => $user->role,
        'admin_permissions' => $user->permissions ?? [],
        'luxury_quote_admin_email' => $user->email,
    ];
}

it('creates a project with default workflow columns and team membership', function () {
    $admin = User::factory()->create(['role' => AdminAccess::ROLE_ADMIN, 'permissions' => AdminAccess::permissionKeys(), 'is_active' => true]);
    $member = User::factory()->create(['role' => AdminAccess::ROLE_SUBACCOUNT, 'permissions' => ['projects'], 'is_active' => true]);

    $this->withSession(projectManagementSession($admin))->post(route('admin.project-management.projects.store'), [
        'project_key' => 'WEB',
        'name' => 'Client Portal',
        'status' => 'planning',
        'priority' => 'high',
        'member_ids' => [$member->id],
        'progress_mode' => 'tasks',
    ])->assertRedirect();

    $project = Project::query()->where('project_number', 'WEB')->firstOrFail();

    expect($project->boardColumns()->count())->toBe(6)
        ->and($project->members()->whereKey($member->id)->exists())->toBeTrue()
        ->and($project->activityLogs()->where('action', 'project.created')->exists())->toBeTrue();
});

it('creates tasks and persists status and ordering through the board endpoint', function () {
    $admin = User::factory()->create(['role' => AdminAccess::ROLE_ADMIN, 'permissions' => AdminAccess::permissionKeys(), 'is_active' => true]);
    $project = Project::query()->create(['project_number' => 'OPS', 'name' => 'Operations', 'status' => 'active', 'priority' => 'medium']);
    $columns = collect([
        BoardColumn::query()->create(['project_id' => $project->id, 'name' => 'To Do', 'position' => 0]),
        BoardColumn::query()->create(['project_id' => $project->id, 'name' => 'Completed', 'position' => 1, 'is_done' => true]),
    ]);

    $this->withSession(projectManagementSession($admin))->post(route('admin.project-management.tasks.store', $project), [
        'title' => 'First task', 'type' => 'task', 'priority' => 'medium', 'board_column_id' => $columns[0]->id,
    ])->assertRedirect();
    $task = Task::query()->firstOrFail();

    $this->withSession(projectManagementSession($admin))->patchJson(route('admin.project-management.tasks.move', $task), [
        'board_column_id' => $columns[1]->id, 'position' => 0,
    ])->assertOk()->assertJsonPath('task_id', $task->id);

    expect($task->fresh()->status)->toBe('completed')
        ->and($task->fresh()->completed_at)->not->toBeNull()
        ->and($task->fresh()->position)->toBe(0);
});

it('keeps limited accounts out of the project management workspace', function () {
    $admin = User::factory()->create(['role' => AdminAccess::ROLE_ADMIN, 'permissions' => AdminAccess::permissionKeys(), 'is_active' => true]);
    $member = User::factory()->create(['role' => AdminAccess::ROLE_SUBACCOUNT, 'permissions' => ['projects'], 'is_active' => true]);
    $other = User::factory()->create(['role' => AdminAccess::ROLE_SUBACCOUNT, 'permissions' => ['projects'], 'is_active' => true]);
    $project = Project::query()->create(['project_number' => 'PRIVATE', 'name' => 'Private Work', 'status' => 'active', 'priority' => 'high']);
    $project->members()->attach($member->id, ['role' => 'member']);

    $this->withSession(projectManagementSession($other))->get(route('admin.project-management.projects.show', $project))->assertForbidden();
    $this->withSession(projectManagementSession($member))->get(route('admin.project-management.projects.show', $project))->assertForbidden();
    $this->withSession(projectManagementSession($admin))->get(route('admin.project-management.projects.show', $project))->assertOk();
});

it('limits members to their assigned tasks across web and API surfaces', function () {
    $admin = User::factory()->create(['role' => AdminAccess::ROLE_ADMIN, 'permissions' => AdminAccess::permissionKeys(), 'is_active' => true]);
    $member = User::factory()->create(['role' => AdminAccess::ROLE_SUBACCOUNT, 'permissions' => ['projects'], 'is_active' => true]);
    $project = Project::query()->create(['project_number' => 'SCOPE', 'name' => 'Scoped Work', 'status' => 'active', 'priority' => 'medium']);
    $column = $project->boardColumns()->create(['name' => 'To Do', 'position' => 0]);
    $project->members()->attach($member->id, ['role' => 'member']);
    $assigned = $project->tasks()->create(['task_number' => 1, 'title' => 'Member task', 'type' => 'task', 'priority' => 'medium', 'status' => 'to_do', 'board_column_id' => $column->id, 'assignee_id' => $member->id, 'reporter_id' => $admin->id, 'position' => 0]);
    $hidden = $project->tasks()->create(['task_number' => 2, 'title' => 'Private teammate task', 'type' => 'task', 'priority' => 'high', 'status' => 'to_do', 'board_column_id' => $column->id, 'assignee_id' => $admin->id, 'reporter_id' => $admin->id, 'position' => 1]);
    $session = projectManagementSession($member);

    $this->withSession($session)->get(route('admin.project-management.dashboard'))->assertOk()->assertSee('My tasks')->assertSee('Workspace')->assertSee('Member task')->assertDontSee('Private teammate task')->assertDontSee('Project overview')->assertDontSee('<span class="admin-nav-label">Create</span>')->assertDontSee('Search anything...')->assertDontSee('New Project');
    $this->withSession($session)->get(route('admin.project-management.board', $project))->assertForbidden();
    $this->withSession($session)->get(route('admin.project-management.tasks.show', $assigned))->assertOk()->assertSee('Member task');
    $this->withSession($session)->get(route('admin.project-management.tasks.show', $hidden))->assertForbidden();
    $this->withSession($session)->getJson(route('admin.project-management.api.projects.show', $project))->assertForbidden();
    $this->withSession($session)->getJson(route('admin.project-management.api.search', ['q' => 'task']))->assertForbidden();
});

it('sanitizes comments, records time, and exposes protected JSON project data', function () {
    $admin = User::factory()->create(['role' => AdminAccess::ROLE_ADMIN, 'permissions' => AdminAccess::permissionKeys(), 'is_active' => true]);
    $project = Project::query()->create(['project_number' => 'API', 'name' => 'API Project', 'status' => 'active', 'priority' => 'medium']);
    $project->boardColumns()->create(['name' => 'To Do', 'position' => 0]);
    $task = $project->tasks()->create(['task_number' => 1, 'title' => 'Review API', 'type' => 'task', 'priority' => 'low', 'status' => 'to_do', 'board_column_id' => $project->boardColumns()->first()->id, 'reporter_id' => $admin->id, 'position' => 0]);

    $this->withSession(projectManagementSession($admin))->post(route('admin.project-management.tasks.comments.store', ['project' => $project, 'task' => $task]), ['body' => '<script>alert(1)</script>Useful update'])->assertRedirect();
    $this->withSession(projectManagementSession($admin))->post(route('admin.project-management.tasks.time.store', $task), ['minutes' => 45, 'description' => 'Reviewed the endpoint contract'])->assertRedirect();
    $this->withSession(projectManagementSession($admin))->getJson(route('admin.project-management.api.projects.show', $project))->assertOk()->assertJsonPath('data.key', 'API')->assertJsonPath('data.tasks.0.key', 'API-1');

    expect($task->comments()->first()->body)->toBe('alert(1)Useful update')
        ->and($task->timeEntries()->sum('minutes'))->toBe(45);
});

it('renders the operational project-management surfaces with live records', function () {
    $admin = User::factory()->create(['role' => AdminAccess::ROLE_ADMIN, 'permissions' => AdminAccess::permissionKeys(), 'is_active' => true]);
    $project = Project::query()->create(['project_number' => 'VIEW', 'name' => 'View Project', 'status' => 'active', 'priority' => 'medium']);
    $project->boardColumns()->create(['name' => 'To Do', 'position' => 0]);
    $task = $project->tasks()->create(['task_number' => 1, 'title' => 'Visible task', 'type' => 'task', 'priority' => 'medium', 'status' => 'to_do', 'board_column_id' => $project->boardColumns()->first()->id, 'reporter_id' => $admin->id, 'position' => 0]);
    $session = projectManagementSession($admin);

    $this->withSession($session)->get(route('admin.project-management.dashboard'))->assertOk()->assertSee('Project overview');
    $this->withSession($session)->get(route('admin.project-management.board', $project))->assertOk()->assertSee('Visible task');
    $this->withSession($session)->get(route('admin.project-management.tasks.show', $task))->assertOk()->assertSee('Task details');
    $this->withSession($session)->get(route('admin.project-management.reports'))->assertOk()->assertSee('Operational reporting');
    $this->withSession($session)->get(route('admin.project-management.calendar'))->assertOk()->assertSee('Planning calendar');
});

it('keeps project management to one highlighted sidebar destination', function () {
    $admin = User::factory()->create(['role' => AdminAccess::ROLE_ADMIN, 'permissions' => AdminAccess::permissionKeys(), 'is_active' => true]);

    $response = $this->withSession(projectManagementSession($admin))
        ->get(route('admin.project-management.dashboard'))
        ->assertOk();

    expect(substr_count($response->getContent(), 'class="admin-nav-link active"'))->toBe(1)
        ->and($response->getContent())->toContain('<strong>Projects</strong>');
});
