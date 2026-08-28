<div class="pm-dashboard pm-dashboard--staff">
    <div class="pm-staff-task-summary">
    <section class="panel pm-hero">
        <div>
            <span class="eyebrow">Assigned work</span>
            <h2>My tasks</h2>
            <p>Only tasks assigned to you are shown here.</p>
        </div>
    </section>

    @php
        $nextTaskDeadline = $nextDueTask?->due_on?->copy()->endOfDay();
    @endphp
    <section class="pm-task-countdown pm-dashboard-countdown" data-dashboard-countdown data-countdown-deadline="{{ $nextTaskDeadline?->toIso8601String() }}" aria-live="polite">
        <span>Next deadline</span>
        @if ($nextDueTask)
            <strong data-countdown-value>Calculating&hellip;</strong>
            <small data-countdown-context>Until the end of the due date</small>
            <a class="pm-dashboard-countdown__task" href="{{ route('admin.project-management.tasks.show', $nextDueTask) }}">{{ $nextDueTask->title }}</a>
        @else
            <strong>No deadline</strong>
            <small>Open tasks with due dates will appear here.</small>
        @endif
    </section>
</div>

    <section class="pm-kpis">
        @foreach ([['Assigned tasks', $myTasks], ['Due today', $todayTasks], ['Overdue', $overdueTasks], ['Completed this week', $completedThisWeek]] as $kpi)
            <article class="panel pm-kpi">
                <span class="metric-label">{{ $kpi[0] }}</span>
                <strong>{{ number_format($kpi[1]) }}</strong>
            </article>
        @endforeach
    </section>

    @php
        $staffTaskTotal = max(1, $tasks->count());
        $staffOpenTaskTotal = max(1, $tasks->whereNull('completed_at')->count());
    @endphp

    <div class="pm-grid pm-staff-chart-grid">
        <section class="panel pm-panel pm-chart-panel">
            <div class="pm-panel-head">
                <div>
                    <h3>Work status</h3>
                    <p>Where your assigned tasks stand.</p>
                </div>
            </div>
            <div class="pm-chart-bars">
                @forelse ($statusBreakdown as $status => $count)
                    <div class="pm-chart-bar">
                        <span>{{ Str::headline($status) }}</span>
                        <div class="pm-progress"><span style="width:{{ round(($count / $staffTaskTotal) * 100) }}%"></span></div>
                        <strong>{{ $count }}</strong>
                    </div>
                @empty
                    <div class="pm-empty pm-empty--compact"><strong>No status data yet.</strong><span>It will appear when tasks are assigned.</span></div>
                @endforelse
            </div>
        </section>

        <section class="panel pm-panel pm-chart-panel">
            <div class="pm-panel-head">
                <div>
                    <h3>Due-date health</h3>
                    <p>Open work by delivery window.</p>
                </div>
            </div>
            <div class="pm-chart-bars">
                @foreach ($dueBreakdown as $bucket)
                    <div class="pm-chart-bar pm-chart-bar--{{ $bucket['tone'] }}">
                        <span>{{ $bucket['label'] }}</span>
                        <div class="pm-progress"><span style="width:{{ round(($bucket['count'] / $staffOpenTaskTotal) * 100) }}%"></span></div>
                        <strong>{{ $bucket['count'] }}</strong>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <section class="panel pm-panel pm-chart-panel pm-project-chart">
        <div class="pm-panel-head">
            <div>
                <h3>Open work by project</h3>
                <p>See where your next actions are concentrated.</p>
            </div>
        </div>
        <div class="pm-chart-bars pm-project-chart__bars">
            @forelse ($projectBreakdown as $project)
                <div class="pm-chart-bar">
                    <span title="{{ $project['name'] }}">{{ $project['key'] }} · {{ Str::limit($project['name'], 24) }}</span>
                    <div class="pm-progress"><span style="width:{{ round(($project['count'] / $staffOpenTaskTotal) * 100) }}%"></span></div>
                    <strong>{{ $project['count'] }}</strong>
                </div>
            @empty
                <div class="pm-empty pm-empty--compact"><strong>No open project work.</strong><span>Completed tasks and new assignments will update this chart.</span></div>
            @endforelse
        </div>
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

@if ($nextDueTask)
    @push('scripts')
        <script>
            (() => {
                const countdown = document.querySelector('[data-dashboard-countdown]');
                const value = countdown?.querySelector('[data-countdown-value]');
                const context = countdown?.querySelector('[data-countdown-context]');
                const deadline = countdown ? new Date(countdown.dataset.countdownDeadline) : null;

                if (!countdown || !value || !context || !deadline || Number.isNaN(deadline.getTime())) return;

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
@endif
