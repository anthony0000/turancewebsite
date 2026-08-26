<div class="pm-dashboard pm-dashboard--staff">
    <section class="panel pm-hero">
        <div>
            <span class="eyebrow">Assigned work</span>
            <h2>My tasks</h2>
            <p>Only tasks assigned to you are shown here.</p>
        </div>
    </section>

    <section class="pm-kpis">
        @foreach ([['Assigned tasks', $myTasks], ['Due today', $todayTasks], ['Overdue', $overdueTasks], ['Completed this week', $completedThisWeek]] as $kpi)
            <article class="panel pm-kpi">
                <span class="metric-label">{{ $kpi[0] }}</span>
                <strong>{{ number_format($kpi[1]) }}</strong>
            </article>
        @endforeach
    </section>

    <section class="panel pm-panel">
        <div class="pm-panel-head">
            <div>
                <h3>Assigned tasks</h3>
                <p>Open a task to review its details.</p>
            </div>
        </div>
        <div class="pm-list">
            @forelse ($tasks as $task)
                <div class="pm-list-item">
                    <div>
                        <strong><a href="{{ route('admin.project-management.tasks.show', $task) }}">{{ $task->task_key }} · {{ $task->title }}</a></strong>
                        <span>{{ $task->project?->name ?: 'Project' }} · {{ Str::headline($task->status) }}</span>
                    </div>
                    <span class="pm-chip {{ $task->is_overdue ? 'pm-chip--danger' : ($task->completed_at ? 'pm-chip--success' : '') }}">
                        {{ $task->completed_at ? 'Completed' : ($task->is_overdue ? 'Overdue' : 'Open') }}
                    </span>
                </div>
            @empty
                <div class="pm-empty">
                    <strong>No tasks assigned to you.</strong>
                    <span>New assignments will appear here.</span>
                </div>
            @endforelse
        </div>
    </section>
</div>
