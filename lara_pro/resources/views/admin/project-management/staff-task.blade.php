@extends('admin.project-management.layout', ['title' => 'My task'])

@section('project-content')
    @php($taskDeadline = $task->due_on?->copy()->endOfDay())
    <div class="pm-task-view pm-task-view--staff">
        <section class="panel pm-hero pm-task-hero">
            <div>
                <span class="eyebrow">Assigned task &middot; {{ $task->task_key }}</span>
                <h2>{{ $task->title }}</h2>
                <p>{{ $task->project?->name ?: 'Project' }} &middot; @if ((int) $task->assignee_id === (int) \App\Support\ProjectManagementAccess::user()?->id) Assigned to you @else Assigned to {{ $task->assignee?->name ?: 'a team member' }} @endif</p>
            </div>
            <div class="pm-task-countdown" data-task-countdown data-countdown-deadline="{{ $taskDeadline?->toIso8601String() }}" data-countdown-completed="{{ $task->completed_at ? '1' : '0' }}" aria-live="polite">
                <span>Due countdown</span>
                <strong data-countdown-value>{{ $task->completed_at ? 'Completed' : ($taskDeadline ? 'Calculating…' : 'No due date') }}</strong>
                <small data-countdown-context>{{ $taskDeadline ? 'Until the end of the due date' : 'Set a due date to track delivery' }}</small>
                <span class="pm-task-countdown__deadline">{{ $taskDeadline ? 'Due '.optional($task->due_on)->format('M d, Y').' · 11:59 PM' : 'No deadline set' }}</span>
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

@push('scripts')
    <script>
        (() => {
            const countdown = document.querySelector('[data-task-countdown]');

            if (!countdown || countdown.dataset.countdownCompleted === '1') {
                return;
            }

            const value = countdown.querySelector('[data-countdown-value]');
            const context = countdown.querySelector('[data-countdown-context]');
            const deadline = new Date(countdown.dataset.countdownDeadline);

            if (!value || Number.isNaN(deadline.getTime())) {
                return;
            }

            const update = () => {
                const remaining = deadline.getTime() - Date.now();
                const absolute = Math.abs(remaining);
                const days = Math.floor(absolute / 86400000);
                const hours = Math.floor((absolute % 86400000) / 3600000);
                const minutes = Math.floor((absolute % 3600000) / 60000);
                const seconds = Math.floor((absolute % 60000) / 1000);
                const formatted = [
                    days ? `${days}d` : '',
                    `${String(hours).padStart(2, '0')}h`,
                    `${String(minutes).padStart(2, '0')}m`,
                    `${String(seconds).padStart(2, '0')}s`,
                ].filter(Boolean).join(' ');

                if (remaining <= 0) {
                    countdown.dataset.countdownState = 'overdue';
                    value.textContent = `${formatted} overdue`;
                    context.textContent = 'This task needs attention';
                    return;
                }

                countdown.dataset.countdownState = remaining <= 86400000 ? 'urgent' : 'active';
                value.textContent = formatted;
                context.textContent = remaining <= 86400000 ? 'Due within the next day' : 'Until the end of the due date';
            };

            update();
            window.setInterval(update, 1000);
        })();
    </script>
@endpush
