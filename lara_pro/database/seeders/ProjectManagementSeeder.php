<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\User;
use App\Support\AdminAccess;
use App\Support\ProjectManagementAccess;
use Illuminate\Database\Seeder;

class ProjectManagementSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('role', AdminAccess::ROLE_ADMIN)->first();
        if (! $admin) {
            $this->command?->warn('Run AdminUserSeeder first so project records have an owner.');
            return;
        }

        $team = collect([
            ['name' => 'Amina Yusuf', 'email' => 'amina.project@example.com', 'job_title' => 'Project Manager'],
            ['name' => 'David Okafor', 'email' => 'david.engineering@example.com', 'job_title' => 'Product Engineer'],
            ['name' => 'Grace Eze', 'email' => 'grace.design@example.com', 'job_title' => 'Product Designer'],
        ])->map(fn (array $attributes) => User::query()->firstOrCreate(
            ['email' => $attributes['email']],
            [...$attributes, 'password' => 'password', 'role' => AdminAccess::ROLE_SUBACCOUNT, 'permissions' => ['projects'], 'is_active' => true, 'email_verified_at' => now()]
        ));

        $clients = collect([
            ['name' => 'Asterion Holdings', 'company' => 'Asterion Holdings', 'email' => 'hello@asterion.example'],
            ['name' => 'Northstar Education', 'company' => 'Northstar Education', 'email' => 'team@northstar.example'],
        ])->map(fn (array $attributes) => Client::query()->updateOrCreate(['email' => $attributes['email']], $attributes));

        $projects = [
            ['key' => 'WEB', 'name' => 'Asterion Client Portal', 'client' => $clients[0], 'status' => 'active', 'priority' => 'high'],
            ['key' => 'NSE', 'name' => 'Northstar Learning Refresh', 'client' => $clients[1], 'status' => 'planning', 'priority' => 'medium'],
            ['key' => 'OPS', 'name' => 'Internal Operations Sprint', 'client' => null, 'status' => 'on_hold', 'priority' => 'low'],
        ];

        foreach ($projects as $definition) {
            $manager = $team->first();
            $project = Project::query()->updateOrCreate(
                ['project_number' => $definition['key']],
                ['name' => $definition['name'], 'client_id' => $definition['client']?->id, 'client_name' => $definition['client']?->name, 'client_company' => $definition['client']?->company, 'status' => $definition['status'], 'priority' => $definition['priority'], 'project_manager_id' => $manager->id, 'starts_on' => today()->subDays(8), 'ends_on' => today()->addDays(35), 'description' => 'Development seed record for the project-management workspace.', 'estimated_hours' => 160]
            );
            ProjectManagementAccess::ensureDefaultColumns($project);
            $project->members()->syncWithoutDetaching([$manager->id => ['role' => 'manager'], ...$team->skip(1)->mapWithKeys(fn (User $user) => [$user->id => ['role' => 'member']])->all()]);

            $columns = $project->boardColumns()->orderBy('position')->get();
            $taskDefinitions = [['title' => 'Confirm project brief', 'priority' => 'high', 'column' => 0], ['title' => 'Prepare first delivery review', 'priority' => 'medium', 'column' => 1], ['title' => 'Build responsive task view', 'priority' => 'urgent', 'column' => 2], ['title' => 'Share progress with client', 'priority' => 'low', 'column' => 5]];
            foreach ($taskDefinitions as $index => $taskDefinition) {
                Task::query()->firstOrCreate(['project_id' => $project->id, 'task_number' => $index + 1], ['board_column_id' => $columns[$taskDefinition['column']]->id, 'reporter_id' => $admin->id, 'assignee_id' => $team[$index % $team->count()]->id, 'title' => $taskDefinition['title'], 'description' => 'Development seed task with a realistic delivery description.', 'type' => 'task', 'status' => str()->snake($columns[$taskDefinition['column']]->name), 'priority' => $taskDefinition['priority'], 'due_on' => today()->addDays($index + 2), 'position' => $index, 'estimated_hours' => 4, 'completed_at' => $columns[$taskDefinition['column']]->is_done ? now() : null]);
            }
            $sprint = Sprint::query()->firstOrCreate(['project_id' => $project->id, 'name' => 'Sprint 01'], ['goal' => 'Establish a dependable first delivery slice.', 'starts_on' => today(), 'ends_on' => today()->addDays(14), 'status' => 'active']);
            $project->tasks()->whereNull('sprint_id')->where('status', '!=', 'completed')->limit(2)->update(['sprint_id' => $sprint->id]);
        }
    }
}
