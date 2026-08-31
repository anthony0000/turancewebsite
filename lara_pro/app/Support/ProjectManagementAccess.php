<?php

namespace App\Support;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class ProjectManagementAccess
{
    public const DEFAULT_COLUMNS = [
        ['name' => 'Backlog', 'color' => '#8b7e6b', 'is_done' => false],
        ['name' => 'To Do', 'color' => '#b8860b', 'is_done' => false],
        ['name' => 'In Progress', 'color' => '#6f5015', 'is_done' => false],
        ['name' => 'In Review', 'color' => '#4b6f8f', 'is_done' => false],
        ['name' => 'Blocked', 'color' => '#b94a3d', 'is_done' => false],
        ['name' => 'Completed', 'color' => '#2f8054', 'is_done' => true],
    ];

    public static function user(): ?User
    {
        return AdminAccess::currentUser();
    }

    public static function canView(Project $project): bool
    {
        if (! AdminAccess::can('projects')) {
            return false;
        }

        if (AdminAccess::isFullAdmin()) {
            return true;
        }

        $userId = self::user()?->id;

        return $userId !== null && (
            (int) $project->project_manager_id === (int) $userId
            || $project->members()->whereKey($userId)->exists()
            || $project->tasks()->where('assignee_id', $userId)->exists()
        );
    }

    public static function canViewSharedFiles(Project $project): bool
    {
        if (! AdminAccess::can('projects')) {
            return false;
        }

        if (AdminAccess::isFullAdmin()) {
            return true;
        }

        $userId = self::user()?->id;

        return $userId !== null && (
            (int) $project->project_manager_id === (int) $userId
            || $project->members()->whereKey($userId)->exists()
        );
    }

    public static function canManage(Project $project): bool
    {
        return self::canView($project) && AdminAccess::isFullAdmin();
    }

    public static function canManageTaskStatus(Task $task): bool
    {
        return self::canView($task->project) && (AdminAccess::isFullAdmin() || AdminAccess::can('project-management'));
    }

    public static function ensureNotificationsWorkspace(): void
    {
        abort_unless(self::user() !== null && AdminAccess::can('projects'), 403);
    }

    public static function isLimitedMember(): bool
    {
        return ! AdminAccess::isFullAdmin();
    }

    public static function ensureFullWorkspace(): void
    {
        abort_unless(AdminAccess::isFullAdmin(), 403);
    }

    public static function ensureProjectCreation(): void
    {
        abort_unless(AdminAccess::isFullAdmin(), 403);
    }

    public static function scopeVisibleTasks(Builder $query): Builder
    {
        if (! self::isLimitedMember()) {
            return $query;
        }

        $userId = self::user()?->id;

        return $userId === null
            ? $query->whereRaw('1 = 0')
            : $query->where('assignee_id', $userId);
    }

    public static function canViewTask(Task $task): bool
    {
        if (! self::canView($task->project)) {
            return false;
        }

        return ! self::isLimitedMember() || (int) $task->assignee_id === (int) self::user()?->id;
    }

    public static function ensureTaskVisible(Task $task): void
    {
        abort_unless(self::canViewTask($task), 403);
    }

    public static function scopeVisible(Builder $query): Builder
    {
        if (AdminAccess::isFullAdmin()) {
            return $query;
        }

        $userId = self::user()?->id;

        if ($userId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $visible) use ($userId): void {
            $visible->where('project_manager_id', $userId)
                ->orWhereHas('members', fn (Builder $members) => $members->whereKey($userId))
                ->orWhereHas('tasks', fn (Builder $tasks) => $tasks->where('assignee_id', $userId));
        });
    }

    public static function scopeVisibleSharedProjects(Builder $query): Builder
    {
        if (AdminAccess::isFullAdmin()) {
            return $query;
        }

        $userId = self::user()?->id;

        if ($userId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $visible) use ($userId): void {
            $visible->where('project_manager_id', $userId)
                ->orWhereHas('members', fn (Builder $members) => $members->whereKey($userId));
        });
    }

    public static function ensureVisible(Project $project): void
    {
        abort_unless(self::canView($project), 403);
    }

    public static function sanitize(?string $value): ?string
    {
        $value = trim(strip_tags((string) $value));

        return $value === '' ? null : $value;
    }

    public static function log(
        ?Project $project,
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $metadata = null,
        ?int $taskId = null,
    ): void {
        DB::table('activity_logs')->insert([
            'project_id' => $project?->id,
            'task_id' => $taskId,
            'actor_id' => self::user()?->id,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'metadata' => $metadata ? json_encode($metadata) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public static function notify(Project $project, string $type, string $message, string $url, ?int $exceptUserId = null, ?array $recipientIds = null): void
    {
        if ($recipientIds === null) {
            $recipientIds = $project->members()->pluck('users.id')->all();

            if ($project->project_manager_id) {
                $recipientIds[] = $project->project_manager_id;
            }
        }

        $recipientIds = array_values(array_unique(array_filter($recipientIds, fn ($id) => (int) $id !== (int) $exceptUserId)));

        foreach ($recipientIds as $recipientId) {
            DB::table('notifications')->insert([
                'id' => (string) Str::uuid(),
                'type' => $type,
                'notifiable_type' => User::class,
                'notifiable_id' => $recipientId,
                'data' => json_encode(['message' => $message, 'url' => $url]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public static function ensureDefaultColumns(Project $project): void
    {
        if ($project->boardColumns()->exists()) {
            return;
        }

        foreach (self::DEFAULT_COLUMNS as $position => $column) {
            $project->boardColumns()->create([...$column, 'position' => $position]);
        }
    }
}
