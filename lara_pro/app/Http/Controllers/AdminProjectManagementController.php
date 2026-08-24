<?php

namespace App\Http\Controllers;

use App\Models\BoardColumn;
use App\Models\Client;
use App\Models\Comment;
use App\Models\Label;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Models\Sprint;
use App\Models\SavedFilter;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use App\Support\AdminAccess;
use App\Support\ProjectManagementAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminProjectManagementController extends Controller
{
    private const STATUSES = ['planning', 'active', 'on_hold', 'completed', 'cancelled', 'archived'];
    private const PRIORITIES = ['low', 'medium', 'high', 'urgent'];
    private const TASK_TYPES = ['task', 'feature', 'bug', 'improvement', 'research', 'design', 'meeting', 'support'];

    public function dashboard(Request $request): View
    {
        $projects = $this->visibleProjects()->where('status', '!=', 'archived')->get();
        $projectIds = $projects->modelKeys();
        $tasks = $this->filteredTasks($request, $projectIds)->get();
        $activeProjects = $projects->whereIn('status', ['planning', 'active', 'on_hold']);
        $completedProjects = $projects->where('status', 'completed');
        $overdueTasks = $tasks->filter(fn (Task $task) => $task->is_overdue);
        $todayTasks = $tasks->filter(fn (Task $task) => $task->due_on?->isToday());
        $weekStart = now()->startOfWeek();
        $completedThisWeek = $tasks->filter(fn (Task $task) => $task->completed_at?->greaterThanOrEqualTo($weekStart));

        $recentActivity = DB::table('activity_logs')
            ->leftJoin('users', 'users.id', '=', 'activity_logs.actor_id')
            ->leftJoin('projects', 'projects.id', '=', 'activity_logs.project_id')
            ->whereIn('activity_logs.project_id', $projectIds ?: [0])
            ->latest('activity_logs.created_at')
            ->limit(10)
            ->get([
                'activity_logs.*',
                'users.name as actor_name',
                'projects.name as project_name',
            ]);

        $workload = $tasks->whereNotNull('assignee_id')
            ->groupBy('assignee_id')
            ->map(fn ($group) => [
                'name' => $group->first()->assignee?->name ?? 'Unassigned',
                'count' => $group->count(),
                'hours' => round((float) $group->sum('estimated_hours'), 1),
            ])->values();

        return view('admin.project-management.dashboard', [
            'projects' => $projects,
            'activeProjects' => $activeProjects,
            'tasks' => $tasks,
            'totalProjects' => $activeProjects->count(),
            'completedProjects' => $completedProjects->count(),
            'overdueProjects' => $activeProjects->filter(fn (Project $project) => $project->ends_on?->isBefore(today()))->count(),
            'myTasks' => $tasks->where('assignee_id', ProjectManagementAccess::user()?->id)->count(),
            'todayTasks' => $todayTasks->count(),
            'overdueTasks' => $overdueTasks->count(),
            'completedThisWeek' => $completedThisWeek->count(),
            'upcomingMilestones' => Milestone::query()->whereIn('project_id', $projectIds ?: [0])->where('status', '!=', 'completed')->whereNotNull('due_on')->orderBy('due_on')->limit(8)->with('project')->get(),
            'recentActivity' => $recentActivity,
            'workload' => $workload,
            'statusBreakdown' => $tasks->groupBy('status')->map->count(),
            'priorityBreakdown' => $tasks->groupBy('priority')->map->count(),
            'filters' => $request->only(['project_id', 'assignee_id', 'priority', 'status', 'date_from', 'date_to']),
            'members' => $this->availableUsers(),
            'savedFilters' => SavedFilter::query()->where('user_id', ProjectManagementAccess::user()?->id)->latest()->get(),
        ]);
    }

    public function storeSavedFilter(Request $request): RedirectResponse
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:80'], 'filters' => ['nullable', 'array']]);
        SavedFilter::query()->updateOrCreate(['user_id' => ProjectManagementAccess::user()?->id, 'project_id' => null, 'name' => trim($validated['name'])], ['filters' => array_filter($validated['filters'] ?? [], fn ($value) => $value !== null && $value !== '')]);

        return back()->with('status', 'Filter saved for quick access.');
    }

    public function destroySavedFilter(SavedFilter $savedFilter): RedirectResponse
    {
        abort_unless((int) $savedFilter->user_id === (int) ProjectManagementAccess::user()?->id, 403);
        $savedFilter->delete();

        return back()->with('status', 'Saved filter removed.');
    }

    public function projects(Request $request): View
    {
        $query = $this->visibleProjects()
            ->with(['client', 'projectManager'])
            ->withCount(['tasks' => fn (Builder $tasks) => $tasks->whereNull('archived_at'), 'members'])
            ->withCount(['tasks as completed_tasks_count' => fn (Builder $tasks) => $tasks->whereNotNull('completed_at')])
            ->withCount(['tasks as overdue_tasks_count' => fn (Builder $tasks) => $tasks->whereNull('completed_at')->whereDate('due_on', '<', today())]);

        if ($search = trim((string) $request->string('q'))) {
            $query->where(fn (Builder $projects) => $projects
                ->where('name', 'like', '%'.$search.'%')
                ->orWhere('project_number', 'like', '%'.$search.'%')
                ->orWhere('client_name', 'like', '%'.$search.'%')
                ->orWhere('client_company', 'like', '%'.$search.'%'));
        }

        $includeArchived = $request->boolean('archived') || $request->routeIs('admin.project-management.archived');
        $query->when($request->filled('status'), fn (Builder $projects) => $projects->where('status', $request->input('status')));
        $query->when($includeArchived, fn (Builder $projects) => $projects->where('status', 'archived'), fn (Builder $projects) => $projects->where('status', '!=', 'archived'));

        return view('admin.project-management.projects', [
            'projects' => $query->latest('updated_at')->paginate(18)->withQueryString(),
            'includeArchived' => $includeArchived,
            'clients' => Client::query()->orderBy('name')->get(),
            'members' => $this->availableUsers(),
            'statuses' => self::STATUSES,
            'priorities' => self::PRIORITIES,
        ]);
    }

    public function createProject(): View
    {
        return view('admin.project-management.create', [
            'clients' => Client::query()->orderBy('name')->get(),
            'members' => $this->availableUsers(),
            'statuses' => self::STATUSES,
            'priorities' => self::PRIORITIES,
        ]);
    }

    public function storeProject(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->projectRules());
        $client = ! empty($validated['client_id']) ? Client::query()->find($validated['client_id']) : null;

        $project = DB::transaction(function () use ($validated, $client): Project {
            $project = Project::query()->create([
                'project_number' => strtoupper(trim($validated['project_key'])),
                'name' => trim($validated['name']),
                'client_id' => $client?->id,
                'client_name' => $client?->name,
                'client_company' => $client?->company,
                'status' => $validated['status'],
                'priority' => $validated['priority'],
                'starts_on' => $validated['starts_on'] ?? null,
                'ends_on' => $validated['ends_on'] ?? null,
                'description' => ProjectManagementAccess::sanitize($validated['description'] ?? null),
                'project_brief' => ProjectManagementAccess::sanitize($validated['project_brief'] ?? null),
                'project_manager_id' => $validated['project_manager_id'] ?? null,
                'budget' => $validated['budget'] ?? null,
                'estimated_hours' => $validated['estimated_hours'] ?? null,
                'progress_mode' => $validated['progress_mode'] ?? 'tasks',
                'progress_override' => ($validated['progress_mode'] ?? 'tasks') === 'manual' ? ($validated['progress_override'] ?? 0) : null,
            ]);

            ProjectManagementAccess::ensureDefaultColumns($project);

            $memberIds = array_values(array_unique(array_filter(array_map('intval', $validated['member_ids'] ?? []))));
            if ($project->project_manager_id) {
                $memberIds[] = (int) $project->project_manager_id;
            }
            $project->members()->syncWithPivotValues(array_values(array_unique($memberIds)), ['role' => 'member']);
            if ($project->project_manager_id) {
                $project->members()->updateExistingPivot($project->project_manager_id, ['role' => 'manager']);
            }

            ProjectManagementAccess::log($project, 'project.created', Project::class, $project->id, null, ['name' => $project->name]);

            return $project;
        });

        return redirect()->route('admin.project-management.projects.show', $project)->with('status', 'Project created and ready for planning.');
    }

    public function showProject(Project $project): View
    {
        ProjectManagementAccess::ensureVisible($project);
        ProjectManagementAccess::ensureDefaultColumns($project);
        $project->load(['client', 'projectManager', 'members', 'boardColumns', 'labels', 'milestones', 'sprints']);
        $project->loadCount(['tasks' => fn (Builder $tasks) => $tasks->whereNull('archived_at'), 'tasks as completed_tasks_count' => fn (Builder $tasks) => $tasks->whereNull('archived_at')->whereNotNull('completed_at')]);

        $tasks = $project->tasks()
            ->with(['project', 'assignee', 'labels'])
            ->withCount(['subtasks', 'subtasks as completed_subtasks_count', 'comments', 'attachments'])
            ->whereNull('archived_at')
            ->orderBy('position')
            ->get();

        return view('admin.project-management.project', [
            'project' => $project,
            'tasks' => $tasks,
            'members' => $this->availableUsers(),
            'recentActivity' => $project->activityLogs()->with('actor')->latest()->limit(12)->get(),
            'comments' => $project->comments()->whereNull('task_id')->with('user')->latest()->limit(8)->get(),
        ]);
    }

    public function updateProject(Request $request, Project $project): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        $validated = $request->validate($this->projectRules($project));
        $old = $project->only(['name', 'status', 'priority', 'ends_on', 'project_manager_id', 'progress_mode', 'progress_override']);
        $client = ! empty($validated['client_id']) ? Client::query()->find($validated['client_id']) : null;

        $project->fill([
            'project_number' => strtoupper(trim($validated['project_key'])),
            'name' => trim($validated['name']),
            'client_id' => $client?->id,
            'client_name' => $client?->name,
            'client_company' => $client?->company,
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'starts_on' => $validated['starts_on'] ?? null,
            'ends_on' => $validated['ends_on'] ?? null,
            'description' => ProjectManagementAccess::sanitize($validated['description'] ?? null),
            'project_brief' => ProjectManagementAccess::sanitize($validated['project_brief'] ?? null),
            'project_manager_id' => $validated['project_manager_id'] ?? null,
            'budget' => $validated['budget'] ?? null,
            'estimated_hours' => $validated['estimated_hours'] ?? null,
            'progress_mode' => $validated['progress_mode'] ?? 'tasks',
            'progress_override' => ($validated['progress_mode'] ?? 'tasks') === 'manual' ? ($validated['progress_override'] ?? 0) : null,
            'completed_at' => $validated['status'] === 'completed' ? ($project->completed_at ?: now()) : null,
        ])->save();

        $memberIds = array_values(array_unique(array_filter(array_map('intval', $validated['member_ids'] ?? []))));
        if ($project->project_manager_id) {
            $memberIds[] = (int) $project->project_manager_id;
        }
        $project->members()->syncWithPivotValues(array_values(array_unique($memberIds)), ['role' => 'member']);
        if ($project->project_manager_id) {
            $project->members()->updateExistingPivot($project->project_manager_id, ['role' => 'manager']);
        }

        ProjectManagementAccess::log($project, 'project.updated', Project::class, $project->id, $old, $project->only(array_keys($old)));

        return redirect()->route('admin.project-management.projects.show', $project)->with('status', 'Project settings saved.');
    }

    public function archiveProject(Project $project): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        $project->update(['status' => 'archived', 'archived_at' => now()]);
        ProjectManagementAccess::log($project, 'project.archived', Project::class, $project->id);

        return redirect()->route('admin.project-management.archived')->with('status', 'Project archived.');
    }

    public function restoreProject(Project $project): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        $project->update(['status' => 'active', 'archived_at' => null]);
        ProjectManagementAccess::log($project, 'project.restored', Project::class, $project->id);

        return redirect()->route('admin.project-management.projects.show', $project)->with('status', 'Project restored.');
    }

    public function destroyProject(Project $project): RedirectResponse
    {
        abort_unless(AdminAccess::isFullAdmin(), 403);
        ProjectManagementAccess::ensureVisible($project);
        $project->delete();

        return redirect()->route('admin.project-management.projects')->with('status', 'Project deleted.');
    }

    public function board(Project $project): View
    {
        ProjectManagementAccess::ensureVisible($project);
        ProjectManagementAccess::ensureDefaultColumns($project);
        $project->load(['client', 'boardColumns', 'members', 'labels', 'milestones', 'sprints']);
        $tasks = $project->tasks()
            ->whereNull('archived_at')
            ->with(['project', 'assignee', 'reporter', 'labels'])
            ->withCount(['subtasks', 'subtasks as completed_subtasks_count', 'comments', 'attachments'])
            ->orderBy('position')
            ->get()
            ->groupBy('board_column_id');

        return view('admin.project-management.board', [
            'project' => $project,
            'columns' => $project->boardColumns,
            'tasksByColumn' => $tasks,
            'members' => $this->availableUsers(),
            'labels' => $project->labels,
            'milestones' => $project->milestones,
            'sprints' => $project->sprints,
            'parentTasks' => $project->tasks()->whereNull('parent_task_id')->whereNull('archived_at')->with('project')->orderBy('task_number')->get(),
            'taskTypes' => self::TASK_TYPES,
            'priorities' => self::PRIORITIES,
        ]);
    }

    public function backlog(Project $project): View
    {
        ProjectManagementAccess::ensureVisible($project);
        $project->load(['sprints', 'members']);

        return view('admin.project-management.backlog', [
            'project' => $project,
            'tasks' => $project->tasks()->whereNull('sprint_id')->whereNull('archived_at')->with(['assignee', 'labels'])->orderBy('position')->paginate(30),
            'sprints' => $project->sprints()->latest('starts_on')->get(),
        ]);
    }

    public function sprints(Project $project): View
    {
        ProjectManagementAccess::ensureVisible($project);
        $sprints = $project->sprints()->withCount('tasks')->with(['tasks' => fn ($query) => $query->whereNotNull('completed_at')])->latest('starts_on')->get();

        return view('admin.project-management.sprints', ['project' => $project, 'sprints' => $sprints]);
    }

    public function assignTaskSprint(Request $request, Task $task): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($task->project);
        $validated = $request->validate(['sprint_id' => ['nullable', 'integer', Rule::exists('sprints', 'id')->where('project_id', $task->project_id)]]);
        $task->update(['sprint_id' => $validated['sprint_id'] ?? null]);

        return back()->with('status', $validated['sprint_id'] ? 'Task moved into the sprint.' : 'Task returned to the backlog.');
    }

    public function calendar(Request $request): View
    {
        $projects = $this->visibleProjects()->where('status', '!=', 'archived')->with(['milestones', 'sprints'])->get();
        $projectIds = $projects->modelKeys();
        $view = in_array($request->input('view'), ['month', 'week', 'agenda'], true) ? $request->input('view') : 'agenda';
        $from = match ($view) {
            'month' => today()->startOfMonth(),
            'week' => today()->startOfWeek(),
            default => today(),
        };
        $to = match ($view) {
            'month' => today()->endOfMonth(),
            'week' => today()->endOfWeek(),
            default => today()->addDays(60),
        };
        $tasks = Task::query()->whereIn('project_id', $projectIds ?: [0])->whereNotNull('due_on')->whereNull('archived_at')->whereBetween('due_on', [$from, $to])->with(['project', 'assignee'])->orderBy('due_on')->limit(200)->get();
        $milestones = Milestone::query()->whereIn('project_id', $projectIds ?: [0])->whereBetween('due_on', [$from, $to])->with('project')->orderBy('due_on')->get();

        return view('admin.project-management.calendar', ['projects' => $projects, 'tasks' => $tasks, 'milestones' => $milestones, 'view' => $view, 'rangeLabel' => $from->format('M d').' – '.$to->format('M d, Y')]);
    }

    public function team(): View
    {
        $projects = $this->visibleProjects()->with(['members', 'projectManager'])->get();
        $tasks = Task::query()->whereIn('project_id', $projects->modelKeys() ?: [0])->whereNull('archived_at')->with('assignee')->get();

        return view('admin.project-management.team', ['projects' => $projects, 'members' => $this->availableUsers(), 'workload' => $tasks->whereNotNull('assignee_id')->groupBy('assignee_id')->map->count()]);
    }

    public function reports(Request $request): View
    {
        $projects = $this->visibleProjects()->where('status', '!=', 'archived')->with('client')->get();
        $tasks = $this->filteredTasks($request, $projects->modelKeys())->with(['assignee', 'project'])->get();
        $completed = $tasks->whereNotNull('completed_at');

        return view('admin.project-management.reports', [
            'projects' => $projects,
            'tasks' => $tasks,
            'completed' => $completed,
            'statusBreakdown' => $tasks->groupBy('status')->map->count(),
            'memberBreakdown' => $completed->groupBy('assignee_id')->map(fn ($items) => ['name' => $items->first()->assignee?->name ?? 'Unassigned', 'count' => $items->count()])->values(),
            'estimatedHours' => round((float) $tasks->sum('estimated_hours'), 1),
            'loggedHours' => round((float) TimeEntry::query()->whereIn('task_id', $tasks->modelKeys() ?: [0])->sum('minutes') / 60, 1),
            'members' => $this->availableUsers(),
        ]);
    }

    public function settings(Project $project): View
    {
        ProjectManagementAccess::ensureVisible($project);
        ProjectManagementAccess::ensureDefaultColumns($project);

        return view('admin.project-management.settings', ['project' => $project->load(['boardColumns', 'labels'])]);
    }

    public function task(Task $task): View
    {
        $project = $task->project;
        ProjectManagementAccess::ensureVisible($project);
        $task->load(['project', 'column', 'assignee', 'reporter', 'milestone', 'sprint', 'labels', 'subtasks.assignee', 'comments.user', 'attachments.uploader', 'timeEntries.user', 'checklists.items']);

        return view('admin.project-management.task', [
            'project' => $project,
            'task' => $task,
            'members' => $this->availableUsers(),
            'columns' => $project->boardColumns()->get(),
            'labels' => $project->labels()->get(),
            'milestones' => $project->milestones()->get(),
            'sprints' => $project->sprints()->get(),
            'taskTypes' => self::TASK_TYPES,
            'priorities' => self::PRIORITIES,
        ]);
    }

    public function storeTask(Request $request, Project $project): RedirectResponse|JsonResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        $validated = $request->validate($this->taskRules($project));
        $task = DB::transaction(function () use ($validated, $project): Task {
            $lockedProject = Project::query()->whereKey($project->id)->lockForUpdate()->firstOrFail();
            $column = BoardColumn::query()->whereKey($validated['board_column_id'] ?? null)->where('project_id', $project->id)->first()
                ?: $lockedProject->boardColumns()->orderBy('position')->firstOrFail();
            $position = (int) ($lockedProject->tasks()->where('board_column_id', $column->id)->max('position') ?? -1) + 1;
            $task = $lockedProject->tasks()->create([
                ...$this->taskPayload($validated),
                'board_column_id' => $column->id,
                'status' => Str::snake($column->name),
                'task_number' => ((int) $lockedProject->tasks()->max('task_number')) + 1,
                'position' => $position,
                'reporter_id' => ProjectManagementAccess::user()?->id,
                'completed_at' => $column->is_done ? now() : null,
            ]);
            $task->labels()->sync($this->projectLabelIds($project, $validated['label_ids'] ?? []));
            ProjectManagementAccess::log($lockedProject, 'task.created', Task::class, $task->id, null, ['title' => $task->title], taskId: $task->id);
            if ($task->assignee_id) {
                ProjectManagementAccess::notify($lockedProject, 'task.assigned', 'You were assigned '.$task->title, route('admin.project-management.tasks.show', $task), ProjectManagementAccess::user()?->id);
            }

            return $task;
        });

        if ($request->expectsJson()) {
            return response()->json(['data' => $task->load(['assignee', 'column', 'labels']), 'message' => 'Task created.'], 201);
        }

        return redirect()->route('admin.project-management.tasks.show', $task)->with('status', 'Task created.');
    }

    public function updateTask(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        $project = $task->project;
        ProjectManagementAccess::ensureVisible($project);
        $validated = $request->validate($this->taskRules($project, $task));
        $old = $task->only(['title', 'priority', 'assignee_id', 'due_on', 'status', 'sprint_id', 'milestone_id']);
        $task->fill($this->taskPayload($validated));
        if (! empty($validated['board_column_id'])) {
            $column = BoardColumn::query()->whereKey($validated['board_column_id'])->where('project_id', $project->id)->firstOrFail();
            $task->board_column_id = $column->id;
            $task->status = Str::snake($column->name);
            $task->completed_at = $column->is_done ? ($task->completed_at ?: now()) : null;
        }
        $task->save();
        if (array_key_exists('label_ids', $validated)) {
            $task->labels()->sync($this->projectLabelIds($project, $validated['label_ids'] ?? []));
        }
        ProjectManagementAccess::log($project, 'task.updated', Task::class, $task->id, $old, $task->only(array_keys($old)), taskId: $task->id);

        if ($request->expectsJson()) {
            return response()->json(['data' => $task->fresh()->load(['assignee', 'column', 'labels']), 'message' => 'Task updated.']);
        }

        return redirect()->route('admin.project-management.tasks.show', $task)->with('status', 'Task saved.');
    }

    public function moveTask(Request $request, Task $task): JsonResponse
    {
        $project = $task->project;
        ProjectManagementAccess::ensureVisible($project);
        $validated = $request->validate([
            'board_column_id' => ['required', 'integer', Rule::exists('board_columns', 'id')->where('project_id', $project->id)],
            'position' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($task, $project, $validated): void {
            $column = BoardColumn::query()->whereKey($validated['board_column_id'])->where('project_id', $project->id)->firstOrFail();
            $destination = Task::query()->where('project_id', $project->id)->where('board_column_id', $column->id)->whereNull('archived_at')->where('id', '!=', $task->id)->orderBy('position')->lockForUpdate()->get()->values();
            $position = min((int) $validated['position'], $destination->count());
            $destination->splice($position, 0, [$task->fresh()]);
            foreach ($destination as $index => $item) {
                $item->forceFill(['board_column_id' => $column->id, 'position' => $index])->save();
            }
            $task->forceFill([
                'board_column_id' => $column->id,
                'status' => Str::snake($column->name),
                'completed_at' => $column->is_done ? ($task->completed_at ?: now()) : null,
            ])->save();
            ProjectManagementAccess::log($project, 'task.moved', Task::class, $task->id, null, ['column_id' => $column->id, 'position' => $position], taskId: $task->id);
        });

        return response()->json(['message' => 'Task position saved.', 'task_id' => $task->id]);
    }

    public function storeColumn(Request $request, Project $project): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        $validated = $request->validate(['name' => ['required', 'string', 'max:80'], 'color' => ['required', 'string', 'max:20'], 'is_done' => ['nullable', 'boolean']]);
        $column = $project->boardColumns()->create([...$validated, 'position' => (int) ($project->boardColumns()->max('position') ?? -1) + 1, 'is_done' => (bool) ($validated['is_done'] ?? false)]);
        ProjectManagementAccess::log($project, 'board-column.created', BoardColumn::class, $column->id);

        return back()->with('status', 'Board column added.');
    }

    public function updateColumn(Request $request, BoardColumn $column): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($column->project);
        $validated = $request->validate(['name' => ['required', 'string', 'max:80'], 'color' => ['required', 'string', 'max:20'], 'is_done' => ['nullable', 'boolean']]);
        $column->update([...$validated, 'is_done' => (bool) ($validated['is_done'] ?? false)]);

        return back()->with('status', 'Board column updated.');
    }

    public function deleteColumn(BoardColumn $column): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($column->project);
        abort_if($column->tasks()->exists(), 422, 'Move the column tasks before deleting this column.');
        abort_if($column->project->boardColumns()->count() <= 1, 422, 'A project must keep at least one board column.');
        $column->delete();

        return back()->with('status', 'Board column removed.');
    }

    public function storeLabel(Request $request, Project $project): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        $validated = $request->validate(['name' => ['required', 'string', 'max:60'], 'color' => ['required', 'string', 'max:20']]);
        $project->labels()->create($validated);

        return back()->with('status', 'Label added.');
    }

    public function storeMilestone(Request $request, Project $project): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        $validated = $request->validate(['title' => ['required', 'string', 'max:160'], 'description' => ['nullable', 'string', 'max:2000'], 'due_on' => ['nullable', 'date'], 'owner_id' => ['nullable', 'integer', 'exists:users,id']]);
        $project->milestones()->create([...$validated, 'description' => ProjectManagementAccess::sanitize($validated['description'] ?? null)]);

        return back()->with('status', 'Milestone added.');
    }

    public function storeSprint(Request $request, Project $project): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        $validated = $request->validate(['name' => ['required', 'string', 'max:160'], 'goal' => ['nullable', 'string', 'max:2000'], 'starts_on' => ['nullable', 'date'], 'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on']]);
        $project->sprints()->create([...$validated, 'goal' => ProjectManagementAccess::sanitize($validated['goal'] ?? null)]);

        return back()->with('status', 'Sprint created.');
    }

    public function startSprint(Sprint $sprint): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($sprint->project);
        abort_if($sprint->project->sprints()->where('status', 'active')->where('id', '!=', $sprint->id)->exists(), 422, 'Only one active sprint is allowed per project.');
        $sprint->update(['status' => 'active', 'starts_on' => $sprint->starts_on ?: today()]);

        return back()->with('status', 'Sprint started.');
    }

    public function completeSprint(Request $request, Sprint $sprint): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($sprint->project);
        $validated = $request->validate(['move_to_sprint_id' => ['nullable', 'integer', Rule::exists('sprints', 'id')->where('project_id', $sprint->project_id)] ]);
        $sprint->tasks()->whereNull('completed_at')->update(['sprint_id' => $validated['move_to_sprint_id'] ?? null]);
        $sprint->update(['status' => 'completed', 'ends_on' => $sprint->ends_on ?: today()]);

        return back()->with('status', 'Sprint completed and incomplete work moved.');
    }

    public function storeMember(Request $request, Project $project): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        $validated = $request->validate(['user_id' => ['required', 'integer', 'exists:users,id'], 'role' => ['required', Rule::in(['manager', 'member', 'viewer'])]]);
        $project->members()->syncWithoutDetaching([$validated['user_id'] => ['role' => $validated['role']]]);
        ProjectManagementAccess::notify($project, 'project.invitation', 'You were added to '.$project->name, route('admin.project-management.projects.show', $project), ProjectManagementAccess::user()?->id);

        return back()->with('status', 'Team member added.');
    }

    public function removeMember(Project $project, User $user): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        $project->members()->detach($user->id);

        return back()->with('status', 'Team member removed.');
    }

    public function storeComment(Request $request, Project $project, ?Task $task = null): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        if ($task) {
            abort_unless((int) $task->project_id === (int) $project->id, 404);
        }
        $validated = $request->validate(['body' => ['required', 'string', 'max:10000'], 'parent_id' => ['nullable', 'integer', 'exists:comments,id']]);
        $comment = Comment::query()->create(['project_id' => $project->id, 'task_id' => $task?->id, 'user_id' => ProjectManagementAccess::user()?->id, 'parent_id' => $validated['parent_id'] ?? null, 'body' => ProjectManagementAccess::sanitize($validated['body'])]);
        ProjectManagementAccess::log($project, 'comment.created', Comment::class, $comment->id, taskId: $task?->id);
        foreach ($project->members()->get()->filter(fn (User $member) => $member->id !== ProjectManagementAccess::user()?->id && Str::contains(strtolower($comment->body), '@'.strtolower(Str::before($member->name, ' ')))) as $mentioned) {
            ProjectManagementAccess::notify($project, 'comment.mention', 'You were mentioned in a comment', $task ? route('admin.project-management.tasks.show', $task) : route('admin.project-management.projects.show', $project), ProjectManagementAccess::user()?->id);
        }

        return back()->with('status', 'Comment added.');
    }

    public function storeTimeEntry(Request $request, Task $task): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($task->project);
        $validated = $request->validate(['minutes' => ['required', 'integer', 'min:1', 'max:1440'], 'description' => ['nullable', 'string', 'max:1000'], 'started_at' => ['nullable', 'date']]);
        TimeEntry::query()->create(['project_id' => $task->project_id, 'task_id' => $task->id, 'user_id' => ProjectManagementAccess::user()?->id, 'started_at' => $validated['started_at'] ?? now()->subMinutes($validated['minutes']), 'ended_at' => now(), 'minutes' => $validated['minutes'], 'description' => ProjectManagementAccess::sanitize($validated['description'] ?? null)]);
        ProjectManagementAccess::log($task->project, 'time-entry.created', TimeEntry::class, null, taskId: $task->id);

        return back()->with('status', 'Time logged.');
    }

    public function startTimer(Task $task): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($task->project);
        abort_if(TimeEntry::query()->where('user_id', ProjectManagementAccess::user()?->id)->whereNull('ended_at')->exists(), 422, 'Stop your active timer before starting another one.');
        TimeEntry::query()->create(['project_id' => $task->project_id, 'task_id' => $task->id, 'user_id' => ProjectManagementAccess::user()?->id, 'started_at' => now(), 'minutes' => 0]);

        return back()->with('status', 'Timer started.');
    }

    public function stopTimer(Task $task): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($task->project);
        $entry = TimeEntry::query()->where('task_id', $task->id)->where('user_id', ProjectManagementAccess::user()?->id)->whereNull('ended_at')->latest('started_at')->firstOrFail();
        $entry->update(['ended_at' => now(), 'minutes' => max(1, $entry->started_at->diffInMinutes(now()))]);

        return back()->with('status', 'Timer stopped and time saved.');
    }

    public function storeAttachment(Request $request, Task $task): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($task->project);
        $validated = $request->validate(['file' => ['required', 'file', 'max:51200', 'mimes:pdf,doc,docx,xls,xlsx,csv,ppt,pptx,txt,jpg,jpeg,png,webp,zip']]);
        /** @var UploadedFile $file */
        $file = $validated['file'];
        $path = $file->storeAs('project-management/'.$task->project_id.'/'.$task->id, Str::uuid().'.'.strtolower($file->getClientOriginalExtension() ?: 'file'), 'local');
        ProjectAttachment::query()->create(['project_id' => $task->project_id, 'task_id' => $task->id, 'uploaded_by' => ProjectManagementAccess::user()?->id, 'original_name' => Str::limit($file->getClientOriginalName(), 255, ''), 'path' => $path, 'mime_type' => $file->getMimeType(), 'size' => $file->getSize()]);

        return back()->with('status', 'Attachment uploaded securely.');
    }

    public function downloadAttachment(ProjectAttachment $attachment): BinaryFileResponse
    {
        ProjectManagementAccess::ensureVisible($attachment->project);
        abort_unless(Storage::disk('local')->exists($attachment->path), 404);

        return response()->download(Storage::disk('local')->path($attachment->path), $attachment->original_name, ['X-Content-Type-Options' => 'nosniff']);
    }

    public function storeChecklistItem(Request $request, Task $task): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($task->project);
        $validated = $request->validate(['checklist_id' => ['required', 'integer', Rule::exists('checklists', 'id')->where('task_id', $task->id)], 'content' => ['required', 'string', 'max:500']]);
        $checklist = $task->checklists()->findOrFail($validated['checklist_id']);
        $checklist->items()->create(['content' => trim($validated['content']), 'position' => (int) ($checklist->items()->max('position') ?? -1) + 1]);

        return back()->with('status', 'Checklist item added.');
    }

    public function storeChecklist(Request $request, Task $task): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($task->project);
        $validated = $request->validate(['title' => ['required', 'string', 'max:120']]);
        $task->checklists()->create(['title' => trim($validated['title']), 'position' => (int) ($task->checklists()->max('position') ?? -1) + 1]);

        return back()->with('status', 'Checklist added.');
    }

    public function toggleChecklistItem(\App\Models\ChecklistItem $item): RedirectResponse
    {
        ProjectManagementAccess::ensureVisible($item->checklist->task->project);
        $item->update(['is_complete' => ! $item->is_complete]);

        return back();
    }

    public function notifications(): View
    {
        $notifications = DB::table('notifications')->where('notifiable_type', User::class)->where('notifiable_id', ProjectManagementAccess::user()?->id)->latest()->paginate(30);

        return view('admin.project-management.notifications', compact('notifications'));
    }

    public function markNotification(string $notification): RedirectResponse
    {
        DB::table('notifications')->where('id', $notification)->where('notifiable_type', User::class)->where('notifiable_id', ProjectManagementAccess::user()?->id)->update(['read_at' => now()]);

        return back();
    }

    public function search(Request $request): View
    {
        $term = trim((string) $request->string('q'));
        $projects = $this->visibleProjects()->when($term !== '', fn (Builder $query) => $query->where(fn (Builder $q) => $q->where('name', 'like', '%'.$term.'%')->orWhere('project_number', 'like', '%'.$term.'%')->orWhere('client_name', 'like', '%'.$term.'%')))->limit(25)->get();
        $tasks = Task::query()->whereIn('project_id', $this->visibleProjects()->pluck('projects.id')->all() ?: [0])->when($term !== '', fn (Builder $query) => $query->where(fn (Builder $q) => $q->where('title', 'like', '%'.$term.'%')->orWhere('description', 'like', '%'.$term.'%')))->with('project')->limit(50)->get();

        return view('admin.project-management.search', compact('term', 'projects', 'tasks'));
    }

    private function visibleProjects(): Builder
    {
        return ProjectManagementAccess::scopeVisible(Project::query());
    }

    private function filteredTasks(Request $request, array $projectIds): Builder
    {
        return Task::query()->whereIn('project_id', $projectIds ?: [0])->whereNull('archived_at')->with(['assignee', 'project'])->when($request->filled('project_id'), fn (Builder $query) => $query->where('project_id', (int) $request->input('project_id')))->when($request->filled('assignee_id'), fn (Builder $query) => $query->where('assignee_id', (int) $request->input('assignee_id')))->when($request->filled('priority'), fn (Builder $query) => $query->where('priority', $request->input('priority')))->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->input('status')))->when($request->filled('date_from'), fn (Builder $query) => $query->whereDate('due_on', '>=', $request->input('date_from')))->when($request->filled('date_to'), fn (Builder $query) => $query->whereDate('due_on', '<=', $request->input('date_to')));
    }

    private function availableUsers()
    {
        return User::query()->whereIn('role', [AdminAccess::ROLE_ADMIN, AdminAccess::ROLE_SUBACCOUNT])->where('is_active', true)->orderBy('name')->get();
    }

    private function projectRules(?Project $project = null): array
    {
        return [
            'project_key' => ['required', 'string', 'max:30', 'alpha_dash', Rule::unique('projects', 'project_number')->ignore($project?->id)],
            'name' => ['required', 'string', 'max:255'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'status' => ['required', Rule::in(self::STATUSES)],
            'priority' => ['required', Rule::in(self::PRIORITIES)],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'description' => ['nullable', 'string', 'max:10000'],
            'project_brief' => ['nullable', 'string', 'max:20000'],
            'project_manager_id' => ['nullable', 'integer', 'exists:users,id'],
            'member_ids' => ['nullable', 'array'],
            'member_ids.*' => ['integer', 'exists:users,id'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'estimated_hours' => ['nullable', 'numeric', 'min:0'],
            'progress_mode' => ['required', Rule::in(['tasks', 'manual'])],
            'progress_override' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }

    private function taskRules(Project $project, ?Task $task = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:20000'],
            'type' => ['required', Rule::in(self::TASK_TYPES)],
            'priority' => ['required', Rule::in(self::PRIORITIES)],
            'board_column_id' => ['nullable', 'integer', Rule::exists('board_columns', 'id')->where('project_id', $project->id)],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'reporter_id' => ['nullable', 'integer', 'exists:users,id'],
            'starts_on' => ['nullable', 'date'],
            'due_on' => ['nullable', 'date'],
            'milestone_id' => ['nullable', 'integer', Rule::exists('milestones', 'id')->where('project_id', $project->id)],
            'sprint_id' => ['nullable', 'integer', Rule::exists('sprints', 'id')->where('project_id', $project->id)],
            'parent_task_id' => ['nullable', 'integer', Rule::exists('tasks', 'id')->where(fn ($query) => $query->where('project_id', $project->id)->when($task, fn ($query) => $query->where('id', '!=', $task->id)))],
            'estimated_hours' => ['nullable', 'numeric', 'min:0'],
            'story_points' => ['nullable', 'numeric', 'min:0'],
            'label_ids' => ['nullable', 'array'],
            'label_ids.*' => ['integer'],
        ];
    }

    private function taskPayload(array $validated): array
    {
        return [
            'title' => trim($validated['title']),
            'description' => ProjectManagementAccess::sanitize($validated['description'] ?? null),
            'type' => $validated['type'],
            'priority' => $validated['priority'],
            'assignee_id' => $validated['assignee_id'] ?? null,
            'reporter_id' => $validated['reporter_id'] ?? null,
            'starts_on' => $validated['starts_on'] ?? null,
            'due_on' => $validated['due_on'] ?? null,
            'milestone_id' => $validated['milestone_id'] ?? null,
            'sprint_id' => $validated['sprint_id'] ?? null,
            'parent_task_id' => $validated['parent_task_id'] ?? null,
            'estimated_hours' => $validated['estimated_hours'] ?? null,
            'story_points' => $validated['story_points'] ?? null,
        ];
    }

    private function projectLabelIds(Project $project, array $labelIds): array
    {
        return Label::query()->where('project_id', $project->id)->whereIn('id', array_map('intval', $labelIds))->pluck('id')->all();
    }
}
