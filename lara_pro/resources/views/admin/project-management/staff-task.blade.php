@extends('admin.project-management.layout', ['title' => 'My task'])

@section('project-content')
    <div class="pm-task-view pm-task-view--staff">
        <section class="panel pm-hero">
            <div>
                <span class="eyebrow">{{ $task->project?->project_number ?: 'Assigned task' }}</span>
                <h2>{{ $task->title }}</h2>
                <p>{{ $task->project?->name ?: 'Project' }} · This task is assigned to you.</p>
            </div>
            <span class="pm-chip {{ $task->is_overdue ? 'pm-chip--danger' : ($task->completed_at ? 'pm-chip--success' : '') }}">
                {{ $task->completed_at ? 'Completed' : ($task->is_overdue ? 'Overdue' : Str::headline($task->status)) }}
            </span>
        </section>

        <div class="pm-grid">
            <section class="panel pm-panel">
                <div class="pm-panel-head">
                    <div>
                        <h3>Task details</h3>
                        <p>Read-only task information.</p>
                    </div>
                    <a class="pm-icon-link" href="{{ route('admin.project-management.dashboard') }}">Back to my tasks</a>
                </div>
                <div class="pm-callout">
                    {{ $task->description ?: 'No additional task description was provided.' }}
                </div>
            </section>

            <aside class="pm-stack">
                <section class="panel pm-panel">
                    <div class="pm-panel-head"><div><h3>Assignment</h3></div></div>
                    <div class="pm-list">
                        <div class="pm-list-item"><span>Task key</span><strong>{{ $task->task_key }}</strong></div>
                        <div class="pm-list-item"><span>Priority</span><strong>{{ Str::headline($task->priority) }}</strong></div>
                        <div class="pm-list-item"><span>Due date</span><strong>{{ optional($task->due_on)->format('M d, Y') ?: 'No due date' }}</strong></div>
                    </div>
                </section>
            </aside>
        </div>
    </div>
@endsection
