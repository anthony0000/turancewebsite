<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\BoardColumn;
use App\Models\Checklist;
use App\Models\Comment;
use App\Models\Milestone;
use App\Models\ProjectAttachment;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Support\ProjectManagementAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProjectManagementApiController extends Controller
{
    public function projects(Request $request): JsonResponse
    {
        $projects = ProjectManagementAccess::scopeVisible(Project::query())
            ->where('status', '!=', 'archived')
            ->with(['client', 'projectManager'])
            ->withCount(['tasks' => fn (Builder $tasks) => $tasks->whereNull('archived_at'), 'tasks as completed_tasks_count' => fn (Builder $tasks) => $tasks->whereNull('archived_at')->whereNotNull('completed_at')])
            ->when($request->filled('q'), fn (Builder $query) => $query->where(fn (Builder $q) => $q->where('name', 'like', '%'.$request->input('q').'%')->orWhere('project_number', 'like', '%'.$request->input('q').'%')))
            ->latest('updated_at')
            ->paginate(min(100, max(1, (int) $request->input('per_page', 25))))
            ->through(fn (Project $project) => $this->projectData($project));

        return response()->json(['data' => $projects->items(), 'meta' => ['current_page' => $projects->currentPage(), 'last_page' => $projects->lastPage(), 'total' => $projects->total()]]);
    }

    public function project(Project $project): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        ProjectManagementAccess::ensureDefaultColumns($project);
        $project->load(['client', 'projectManager', 'members', 'boardColumns', 'labels', 'milestones', 'sprints']);
        $project->loadCount(['tasks' => fn (Builder $tasks) => $tasks->whereNull('archived_at'), 'tasks as completed_tasks_count' => fn (Builder $tasks) => $tasks->whereNull('archived_at')->whereNotNull('completed_at')]);

        return response()->json(['data' => $this->projectData($project, true)]);
    }

    public function storeProject(Request $request): JsonResponse
    {
        $validated = $request->validate(['project_key' => ['required', 'string', 'max:30', 'alpha_dash', 'unique:projects,project_number'], 'name' => ['required', 'string', 'max:255'], 'client_id' => ['nullable', 'integer', 'exists:clients,id'], 'status' => ['required', Rule::in(['planning', 'active', 'on_hold', 'completed', 'cancelled'])], 'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])], 'starts_on' => ['nullable', 'date'], 'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'], 'project_manager_id' => ['nullable', 'integer', 'exists:users,id'], 'description' => ['nullable', 'string', 'max:10000']]);
        $project = DB::transaction(function () use ($validated): Project {
            $project = Project::query()->create(['project_number' => strtoupper(trim($validated['project_key'])), 'name' => trim($validated['name']), 'client_id' => $validated['client_id'] ?? null, 'status' => $validated['status'], 'priority' => $validated['priority'], 'starts_on' => $validated['starts_on'] ?? null, 'ends_on' => $validated['ends_on'] ?? null, 'project_manager_id' => $validated['project_manager_id'] ?? null, 'description' => ProjectManagementAccess::sanitize($validated['description'] ?? null)]);
            ProjectManagementAccess::ensureDefaultColumns($project);
            if ($project->project_manager_id) {
                $project->members()->attach($project->project_manager_id, ['role' => 'manager']);
            }
            ProjectManagementAccess::log($project, 'project.created', Project::class, $project->id);

            return $project;
        });

        return response()->json(['data' => $this->projectData($project->fresh()->load(['client', 'projectManager'])), 'message' => 'Project created.'], 201);
    }

    public function members(Project $project): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($project);

        return response()->json(['data' => $project->members()->get()->map(fn ($member) => ['id' => $member->id, 'name' => $member->name, 'email' => $member->email, 'role' => $member->pivot->role])->values()]);
    }

    public function addMember(Request $request, Project $project): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        $validated = $request->validate(['user_id' => ['required', 'integer', 'exists:users,id'], 'role' => ['required', Rule::in(['manager', 'member', 'viewer'])]]);
        $project->members()->syncWithoutDetaching([$validated['user_id'] => ['role' => $validated['role']]]);

        return response()->json(['data' => ['user_id' => (int) $validated['user_id'], 'role' => $validated['role']], 'message' => 'Member added.'], 201);
    }

    public function removeMember(Project $project, \App\Models\User $user): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        $project->members()->detach($user->id);

        return response()->json(['data' => null, 'message' => 'Member removed.']);
    }

    public function columns(Project $project): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        ProjectManagementAccess::ensureDefaultColumns($project);

        return response()->json(['data' => $project->boardColumns()->get()->map->only(['id', 'name', 'color', 'position', 'is_done'])->values()]);
    }

    public function storeColumn(Request $request, Project $project): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        $validated = $request->validate(['name' => ['required', 'string', 'max:80'], 'color' => ['required', 'string', 'max:20'], 'is_done' => ['nullable', 'boolean']]);
        $column = $project->boardColumns()->create([...$validated, 'position' => (int) ($project->boardColumns()->max('position') ?? -1) + 1, 'is_done' => (bool) ($validated['is_done'] ?? false)]);

        return response()->json(['data' => $column, 'message' => 'Column created.'], 201);
    }

    public function updateColumn(Request $request, BoardColumn $column): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($column->project);
        $validated = $request->validate(['name' => ['required', 'string', 'max:80'], 'color' => ['required', 'string', 'max:20'], 'is_done' => ['nullable', 'boolean']]);
        $column->update([...$validated, 'is_done' => (bool) ($validated['is_done'] ?? false)]);

        return response()->json(['data' => $column->fresh(), 'message' => 'Column updated.']);
    }

    public function deleteColumn(BoardColumn $column): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($column->project);
        abort_if($column->tasks()->exists(), 422, 'Move tasks before deleting this column.');
        $column->delete();

        return response()->json(['data' => null, 'message' => 'Column deleted.']);
    }

    public function storeTask(Request $request, Project $project): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['task', 'feature', 'bug', 'improvement', 'research', 'design', 'meeting', 'support'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'board_column_id' => ['nullable', 'integer', Rule::exists('board_columns', 'id')->where('project_id', $project->id)],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'due_on' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:20000'],
        ]);
        ProjectManagementAccess::ensureDefaultColumns($project);
        $column = $project->boardColumns()->whereKey($validated['board_column_id'] ?? null)->first() ?: $project->boardColumns()->first();
        $task = DB::transaction(function () use ($project, $column, $validated): Task {
            $locked = Project::query()->whereKey($project->id)->lockForUpdate()->firstOrFail();
            $task = $locked->tasks()->create([
                'board_column_id' => $column->id,
                'task_number' => ((int) $locked->tasks()->max('task_number')) + 1,
                'title' => trim($validated['title']),
                'description' => ProjectManagementAccess::sanitize($validated['description'] ?? null),
                'type' => $validated['type'],
                'priority' => $validated['priority'],
                'status' => Str::snake($column->name),
                'assignee_id' => $validated['assignee_id'] ?? null,
                'reporter_id' => ProjectManagementAccess::user()?->id,
                'due_on' => $validated['due_on'] ?? null,
                'position' => ((int) $locked->tasks()->where('board_column_id', $column->id)->max('position')) + 1,
                'completed_at' => $column->is_done ? now() : null,
            ]);
            ProjectManagementAccess::log($locked, 'task.created', Task::class, $task->id, taskId: $task->id);

            return $task;
        });

        return response()->json(['data' => $task->load(['project', 'column', 'assignee']), 'message' => 'Task created.'], 201);
    }

    public function updateTask(Request $request, Task $task): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($task->project);
        $validated = $request->validate(['title' => ['sometimes', 'required', 'string', 'max:255'], 'priority' => ['sometimes', Rule::in(['low', 'medium', 'high', 'urgent'])], 'assignee_id' => ['nullable', 'integer', 'exists:users,id'], 'due_on' => ['nullable', 'date'], 'description' => ['nullable', 'string', 'max:20000'], 'parent_task_id' => ['nullable', 'integer', Rule::exists('tasks', 'id')->where('project_id', $task->project_id)->where('id', '!=', $task->id)] ]);
        $task->fill($validated);
        if (array_key_exists('description', $validated)) {
            $task->description = ProjectManagementAccess::sanitize($validated['description']);
        }
        $task->save();

        return response()->json(['data' => $task->fresh()->load(['project', 'column', 'assignee']), 'message' => 'Task updated.']);
    }

    public function moveTask(Request $request, Task $task): JsonResponse
    {
        $request->headers->set('Accept', 'application/json');

        return app(AdminProjectManagementController::class)->moveTask($request, $task);
    }

    public function comments(Project $project, ?Task $task = null): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        if ($task) {
            abort_unless((int) $task->project_id === (int) $project->id, 404);
        }

        return response()->json(['data' => Comment::query()->where('project_id', $project->id)->when($task, fn (Builder $query) => $query->where('task_id', $task->id))->with('user')->latest()->paginate(30)]);
    }

    public function storeComment(Request $request, Project $project, ?Task $task = null): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        if ($task) {
            abort_unless((int) $task->project_id === (int) $project->id, 404);
        }
        $validated = $request->validate(['body' => ['required', 'string', 'max:10000'], 'parent_id' => ['nullable', 'integer', 'exists:comments,id']]);
        $comment = Comment::query()->create(['project_id' => $project->id, 'task_id' => $task?->id, 'user_id' => ProjectManagementAccess::user()?->id, 'parent_id' => $validated['parent_id'] ?? null, 'body' => ProjectManagementAccess::sanitize($validated['body'])]);
        ProjectManagementAccess::log($project, 'comment.created', Comment::class, $comment->id, taskId: $task?->id);

        return response()->json(['data' => $comment->load('user'), 'message' => 'Comment created.'], 201);
    }

    public function storeMilestone(Request $request, Project $project): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        $validated = $request->validate(['title' => ['required', 'string', 'max:160'], 'description' => ['nullable', 'string', 'max:2000'], 'due_on' => ['nullable', 'date'], 'owner_id' => ['nullable', 'integer', 'exists:users,id']]);
        $milestone = $project->milestones()->create([...$validated, 'description' => ProjectManagementAccess::sanitize($validated['description'] ?? null)]);

        return response()->json(['data' => $milestone, 'message' => 'Milestone created.'], 201);
    }

    public function storeSprint(Request $request, Project $project): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($project);
        $validated = $request->validate(['name' => ['required', 'string', 'max:160'], 'goal' => ['nullable', 'string', 'max:2000'], 'starts_on' => ['nullable', 'date'], 'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on']]);
        $sprint = $project->sprints()->create([...$validated, 'goal' => ProjectManagementAccess::sanitize($validated['goal'] ?? null)]);

        return response()->json(['data' => $sprint, 'message' => 'Sprint created.'], 201);
    }

    public function storeTimeEntry(Request $request, Task $task): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($task->project);
        $validated = $request->validate(['minutes' => ['required', 'integer', 'min:1', 'max:1440'], 'description' => ['nullable', 'string', 'max:1000'], 'started_at' => ['nullable', 'date']]);
        $entry = TimeEntry::query()->create(['project_id' => $task->project_id, 'task_id' => $task->id, 'user_id' => ProjectManagementAccess::user()?->id, 'started_at' => $validated['started_at'] ?? now()->subMinutes($validated['minutes']), 'ended_at' => now(), 'minutes' => $validated['minutes'], 'description' => ProjectManagementAccess::sanitize($validated['description'] ?? null)]);

        return response()->json(['data' => $entry, 'message' => 'Time entry created.'], 201);
    }

    public function storeChecklist(Request $request, Task $task): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($task->project);
        $validated = $request->validate(['title' => ['required', 'string', 'max:120']]);
        $checklist = $task->checklists()->create(['title' => trim($validated['title']), 'position' => (int) ($task->checklists()->max('position') ?? -1) + 1]);

        return response()->json(['data' => $checklist, 'message' => 'Checklist created.'], 201);
    }

    public function storeChecklistItem(Request $request, Task $task): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($task->project);
        $validated = $request->validate(['checklist_id' => ['required', 'integer', Rule::exists('checklists', 'id')->where('task_id', $task->id)], 'content' => ['required', 'string', 'max:500']]);
        $checklist = $task->checklists()->findOrFail($validated['checklist_id']);
        $item = $checklist->items()->create(['content' => trim($validated['content']), 'position' => (int) ($checklist->items()->max('position') ?? -1) + 1]);

        return response()->json(['data' => $item, 'message' => 'Checklist item created.'], 201);
    }

    public function attachments(Task $task): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($task->project);

        return response()->json(['data' => $task->attachments()->with('uploader')->get()->map(fn ($attachment) => ['id' => $attachment->id, 'name' => $attachment->original_name, 'size' => $attachment->size, 'mime_type' => $attachment->mime_type, 'download_url' => route('admin.project-management.attachments.download', $attachment)])->values()]);
    }

    public function storeAttachment(Request $request, Task $task): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($task->project);
        $validated = $request->validate(['file' => ['required', 'file', 'max:51200', 'mimes:pdf,doc,docx,xls,xlsx,csv,ppt,pptx,txt,jpg,jpeg,png,webp,zip']]);
        $file = $validated['file'];
        $path = $file->storeAs('project-management/'.$task->project_id.'/'.$task->id, Str::uuid().'.'.strtolower($file->getClientOriginalExtension() ?: 'file'), 'local');
        $attachment = ProjectAttachment::query()->create(['project_id' => $task->project_id, 'task_id' => $task->id, 'uploaded_by' => ProjectManagementAccess::user()?->id, 'original_name' => Str::limit($file->getClientOriginalName(), 255, ''), 'path' => $path, 'mime_type' => $file->getMimeType(), 'size' => $file->getSize()]);

        return response()->json(['data' => $attachment, 'message' => 'Attachment uploaded.'], 201);
    }

    public function notifications(): JsonResponse
    {
        $notifications = DB::table('notifications')->where('notifiable_type', \App\Models\User::class)->where('notifiable_id', ProjectManagementAccess::user()?->id)->latest()->paginate(30);

        return response()->json(['data' => $notifications->items(), 'meta' => ['current_page' => $notifications->currentPage(), 'last_page' => $notifications->lastPage(), 'total' => $notifications->total()]]);
    }

    public function markNotification(string $notification): JsonResponse
    {
        DB::table('notifications')->where('id', $notification)->where('notifiable_type', \App\Models\User::class)->where('notifiable_id', ProjectManagementAccess::user()?->id)->update(['read_at' => now()]);

        return response()->json(['data' => null, 'message' => 'Notification marked as read.']);
    }

    public function search(Request $request): JsonResponse
    {
        $term = trim((string) $request->input('q', ''));
        $projectIds = ProjectManagementAccess::scopeVisible(Project::query())->pluck('projects.id')->all();
        $projects = Project::query()->whereIn('id', $projectIds ?: [0])->where(fn (Builder $query) => $query->where('name', 'like', '%'.$term.'%')->orWhere('project_number', 'like', '%'.$term.'%')->orWhere('client_name', 'like', '%'.$term.'%'))->limit(25)->get()->map(fn (Project $project) => ['id' => $project->id, 'key' => $project->project_number, 'name' => $project->name]);
        $tasks = Task::query()->whereIn('project_id', $projectIds ?: [0])->where(fn (Builder $query) => $query->where('title', 'like', '%'.$term.'%')->orWhere('description', 'like', '%'.$term.'%'))->with('project')->limit(50)->get()->map(fn (Task $task) => ['id' => $task->id, 'key' => $task->task_key, 'title' => $task->title, 'project' => $task->project->name]);

        return response()->json(['data' => ['projects' => $projects->values(), 'tasks' => $tasks->values()]]);
    }

    public function activity(Project $project): JsonResponse
    {
        ProjectManagementAccess::ensureVisible($project);

        return response()->json(['data' => $project->activityLogs()->with('actor')->latest()->paginate(50)]);
    }

    public function reports(Request $request): JsonResponse
    {
        $projectIds = ProjectManagementAccess::scopeVisible(Project::query())->pluck('projects.id')->all();
        $tasks = Task::query()->whereIn('project_id', $projectIds ?: [0])->whereNull('archived_at')->when($request->filled('project_id'), fn (Builder $query) => $query->where('project_id', $request->integer('project_id')))->get();

        return response()->json(['data' => ['task_count' => $tasks->count(), 'completed_count' => $tasks->whereNotNull('completed_at')->count(), 'status' => $tasks->groupBy('status')->map->count(), 'priority' => $tasks->groupBy('priority')->map->count(), 'estimated_hours' => round((float) $tasks->sum('estimated_hours'), 2)]]);
    }

    private function projectData(Project $project, bool $full = false): array
    {
        $data = ['id' => $project->id, 'key' => $project->project_number, 'name' => $project->name, 'status' => $project->status, 'priority' => $project->priority, 'progress' => $project->progress_percentage, 'client' => $project->client?->only(['id', 'name', 'company']), 'manager' => $project->projectManager?->only(['id', 'name', 'email']), 'starts_on' => optional($project->starts_on)->toDateString(), 'ends_on' => optional($project->ends_on)->toDateString(), 'tasks_count' => $project->tasks_count ?? null, 'completed_tasks_count' => $project->completed_tasks_count ?? null];

        if ($full) {
            $data['members'] = $project->members->map(fn ($member) => ['id' => $member->id, 'name' => $member->name, 'role' => $member->pivot->role])->values();
            $data['columns'] = $project->boardColumns->map(fn ($column) => ['id' => $column->id, 'name' => $column->name, 'position' => $column->position, 'is_done' => $column->is_done])->values();
            $data['labels'] = $project->labels->map->only(['id', 'name', 'color'])->values();
            $data['milestones'] = $project->milestones->map->only(['id', 'title', 'due_on', 'status'])->values();
            $data['sprints'] = $project->sprints->map->only(['id', 'name', 'starts_on', 'ends_on', 'status'])->values();
            $data['tasks'] = $project->tasks()->whereNull('archived_at')->with(['project', 'column', 'assignee', 'labels'])->orderBy('position')->get()->map(fn (Task $task) => ['id' => $task->id, 'key' => $task->task_key, 'title' => $task->title, 'status' => $task->status, 'priority' => $task->priority, 'column_id' => $task->board_column_id, 'assignee' => $task->assignee?->only(['id', 'name']), 'labels' => $task->labels->map->only(['id', 'name', 'color'])->values()])->values();
        }

        return $data;
    }
}
