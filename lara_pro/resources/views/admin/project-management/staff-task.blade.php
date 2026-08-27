@extends('admin.project-management.layout', ['title' => 'My task'])

@section('project-content')
    <div class="pm-task-view pm-task-view--staff">
        <section class="panel pm-hero pm-task-hero">
            <div>
                <span class="eyebrow">{{ $task->task_key }} &middot; {{ $task->project?->project_number ?: 'Assigned task' }}</span>
                <h2>{{ $task->title }}</h2>
                <p>{{ $task->project?->name ?: 'Project' }} &middot; @if ((int) $task->assignee_id === (int) \App\Support\ProjectManagementAccess::user()?->id) Assigned to you @else Assigned to {{ $task->assignee?->name ?: 'a team member' }} @endif</p>
            </div>
            <div class="pm-task-hero__actions">
                <a class="ghost-button" href="{{ route('admin.project-management.dashboard') }}">Back to my tasks</a>
                @if ($task->completed_at)
                    <span class="pm-chip pm-chip--success">Completed {{ $task->completed_at->format('M d, Y') }}</span>
                    @if ($canManageTaskStatus)
                        <form method="POST" action="{{ route('admin.project-management.tasks.reopen', $task) }}">
                            @csrf @method('PATCH')
                            <button class="ghost-button" type="submit">Mark not completed</button>
                        </form>
                    @endif
                @elseif ($canCompleteTask)
                    <form method="POST" action="{{ route('admin.project-management.tasks.complete', $task) }}">
                        @csrf @method('PATCH')
                        <button class="button" type="submit">Mark as done</button>
                    </form>
                @else
                    <span class="pm-chip">Awaiting assignee</span>
                @endif
            </div>
        </section>

        <div class="pm-grid pm-staff-task-grid">
            <section class="panel pm-panel">
                <div class="pm-panel-head"><div><span class="eyebrow">Task details</span><h3>What needs to happen</h3></div></div>
                <div class="pm-callout">{{ $task->description ?: 'No additional task description was provided.' }}</div>
            </section>
            <aside class="pm-stack">
                <section class="panel pm-panel"><div class="pm-panel-head"><div><h3>Assignment</h3><p>Your current task context.</p></div></div><div class="pm-list"><div class="pm-list-item"><span>Task key</span><strong>{{ $task->task_key }}</strong></div><div class="pm-list-item"><span>Priority</span><strong>{{ Str::headline($task->priority) }}</strong></div><div class="pm-list-item"><span>List</span><strong>{{ $task->column?->name ?: Str::headline($task->status) }}</strong></div><div class="pm-list-item"><span>Due date</span><strong class="{{ $task->is_overdue ? 'is-overdue' : '' }}">{{ optional($task->due_on)->format('M d, Y') ?: 'No due date' }}</strong></div></div></section>
            </aside>
        </div>
    </div>
@endsection
