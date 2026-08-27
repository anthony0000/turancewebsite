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
