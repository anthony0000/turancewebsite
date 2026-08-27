@extends('admin.project-management.layout', ['title' => 'Overview'])

@section('project-content')
    @if (! $canManageWorkspace)
        @include('admin.project-management.partials.my-tasks')
    @else
    <div class="pm-dashboard">
    <section class="panel pm-hero">
        <div>
            <span class="eyebrow">Workspace control</span>
            <h2>Assign and track work</h2>
        </div>
        <div class="pm-actions">
            @if ($canManageWorkspace)
                <a class="button" href="{{ route('admin.project-management.projects.create') }}">New project</a>
            @endif
            <a class="ghost-button" href="{{ route('admin.project-management.reports') }}">View reports</a>
        </div>
    </section>

    <section class="pm-kpis">
        @foreach ([['Active projects', $totalProjects], ['Completed projects', $completedProjects], ['Overdue tasks', $overdueTasks], ['Open tasks', $openTasks]] as $kpi)
            <article class="panel pm-kpi">
                <span class="metric-label">{{ $kpi[0] }}</span>
                <strong>{{ number_format($kpi[1]) }}</strong>
            </article>
        @endforeach
    </section>

    <details class="panel pm-panel pm-filter-panel">
        <summary><span><strong>Filter view</strong><small>Projects, assignees, dates</small></span><span class="pm-filter-panel__toggle">Open</span></summary>
        <form method="GET" action="{{ route('admin.project-management.dashboard') }}">
        <div class="pm-panel-head"><div><h3>Filters</h3></div><a class="pm-icon-link" href="{{ route('admin.project-management.dashboard') }}">Reset</a></div>
        <div class="pm-form-grid">
            <div class="field"><label for="dashboard-project">Project</label><select id="dashboard-project" name="project_id"><option value="">All projects</option>@foreach ($projects as $project)<option value="{{ $project->id }}" @selected(($filters['project_id'] ?? '') == $project->id)>{{ $project->project_number }} · {{ $project->name }}</option>@endforeach</select></div>
            @if ($canManageWorkspace)
                <div class="field"><label for="dashboard-member">Assignee</label><select id="dashboard-member" name="assignee_id"><option value="">Everyone</option>@foreach ($assignees as $assignee)<option value="{{ $assignee->id }}" @selected(($filters['assignee_id'] ?? '') == $assignee->id)>{{ $assignee->name }}</option>@endforeach</select></div>
            @endif
            <div class="field"><label for="dashboard-priority">Priority</label><select id="dashboard-priority" name="priority"><option value="">Every priority</option>@foreach (['low', 'medium', 'high', 'urgent'] as $priority)<option value="{{ $priority }}" @selected(($filters['priority'] ?? '') === $priority)>{{ Str::headline($priority) }}</option>@endforeach</select></div>
            <div class="field"><label for="dashboard-status">Status</label><select id="dashboard-status" name="status"><option value="">Every status</option>@foreach ($statusBreakdown->keys() as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ Str::headline($status) }}</option>@endforeach</select></div>
            <div class="field"><label for="dashboard-from">Due from</label><input id="dashboard-from" type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"></div>
            <div class="field"><label for="dashboard-to">Due to</label><input id="dashboard-to" type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"></div>
        </div>
        <div class="pm-form-actions"><a class="pm-icon-link" href="{{ route('admin.project-management.dashboard') }}">Reset</a><button class="button" type="submit">Apply view</button></div>
        </form>
    <section class="pm-saved-views"><div class="pm-panel-head"><div><h3>Saved views</h3></div></div><div class="pm-actions pm-saved-views__links">@forelse ($savedFilters as $savedFilter)<a class="pm-chip" href="{{ route('admin.project-management.dashboard', $savedFilter->filters ?: []) }}">{{ $savedFilter->name }}</a><form method="POST" action="{{ route('admin.project-management.filters.destroy', $savedFilter) }}">@csrf @method('DELETE')<button class="pm-icon-link" type="submit" aria-label="Remove {{ $savedFilter->name }}">×</button></form>@empty<span class="pm-muted">No saved views yet.</span>@endforelse</div><form class="pm-inline-form" method="POST" action="{{ route('admin.project-management.filters.store') }}">@csrf<div class="field"><label for="saved-filter-name">Save as</label><input id="saved-filter-name" name="name" required placeholder="Open tasks"></div>@foreach ($filters as $key => $value)<input type="hidden" name="filters[{{ $key }}]" value="{{ $value }}">@endforeach<button class="ghost-button" type="submit">Save view</button></form></section>
    </details>

    <div class="pm-grid">
        <section class="panel pm-panel">
            <div class="pm-panel-head"><div><h3>Project progress</h3></div><a class="pm-icon-link" href="{{ route('admin.project-management.projects') }}">All projects</a></div>
            <div class="pm-list">
                @forelse ($activeProjects->take(8) as $project)
                    <div class="pm-list-item">
                        <div style="min-width:0; flex:1"><strong><a href="{{ route('admin.project-management.projects.show', $project) }}">{{ $project->project_number }} · {{ $project->name }}</a></strong><span>{{ $project->client_company ?: $project->client_name ?: 'Internal project' }} · {{ Str::headline($project->status) }}</span><div class="pm-progress" style="margin-top:8px"><span style="width:{{ $project->progress_percentage }}%"></span></div></div>
                        <strong>{{ $project->progress_percentage }}%</strong>
                    </div>
                @empty
                    <div class="pm-empty pm-empty--actionable"><strong>No active projects yet.</strong><span>Start a project to see delivery progress here.</span>@if ($canManageWorkspace)<a class="pm-icon-link" href="{{ route('admin.project-management.projects.create') }}">Create project <span aria-hidden="true">→</span></a>@endif</div>
                @endforelse
            </div>
        </section>
        <section class="panel pm-panel">
            <div class="pm-panel-head"><div><h3>Task distribution</h3></div></div>
            <div class="pm-chart-bars"><strong class="pm-muted">By status</strong>@forelse ($statusBreakdown as $status => $count)<div class="pm-chart-bar"><span>{{ Str::headline($status) }}</span><div class="pm-progress"><span style="width:{{ $tasks->count() ? round(($count / $tasks->count()) * 100) : 0 }}%"></span></div><span>{{ $count }}</span></div>@empty<div class="pm-empty pm-empty--compact"><strong>No tasks yet.</strong><span>Task distribution appears once work is added.</span></div>@endforelse</div>
            <div class="pm-chart-bars" style="margin-top:20px"><strong class="pm-muted">By priority</strong>@foreach ($priorityBreakdown as $priority => $count)<div class="pm-chart-bar"><span>{{ Str::headline($priority) }}</span><div class="pm-progress"><span style="width:{{ $tasks->count() ? round(($count / $tasks->count()) * 100) : 0 }}%; background:{{ $priority === 'urgent' ? 'var(--danger)' : 'var(--accent)' }}"></span></div><span>{{ $count }}</span></div>@endforeach</div>
        </section>
    </div>

    <section class="panel pm-panel pm-task-overview">
        <div class="pm-panel-head">
            <div>
                <h3>Task details</h3>
                <p>Recent work in the current filtered view.</p>
            </div>
            @if ($projects->isNotEmpty())
                <a class="pm-icon-link" href="{{ route('admin.project-management.backlog', $projects->first()) }}">Open backlog</a>
            @endif
        </div>
        <div class="pm-task-overview-table" role="table" aria-label="Task details">
            <div class="pm-task-overview-row pm-task-overview-row--head" role="row">
                <span role="columnheader">Task</span>
                <span role="columnheader">Project</span>
                <span role="columnheader">Assignee</span>
                <span role="columnheader">Priority</span>
                <span role="columnheader">Due</span>
                <span role="columnheader">Status</span>
            </div>
            @forelse ($taskOverview as $task)
                <div class="pm-task-overview-row" role="row">
                    <div role="cell" class="pm-task-overview-row__task">
                        <strong><a href="{{ route('admin.project-management.tasks.show', $task) }}">{{ $task->title }}</a></strong>
                        <span>{{ $task->task_key }} · {{ Str::headline($task->type) }}</span>
                    </div>
                    <span role="cell">{{ $task->project?->project_number ?: '—' }}</span>
                    <span role="cell">{{ $task->assignee?->name ?: 'Unassigned' }}</span>
                    <span role="cell" class="pm-task-overview-priority pm-task-overview-priority--{{ $task->priority }}">{{ Str::headline($task->priority) }}</span>
                    <span role="cell" class="{{ $task->is_overdue ? 'is-overdue' : '' }}">{{ optional($task->due_on)->format('M d, Y') ?: 'No date' }}</span>
                    <span role="cell"><span class="pm-chip {{ $task->completed_at ? 'pm-chip--success' : ($task->is_overdue ? 'pm-chip--danger' : '') }}">{{ $task->completed_at ? 'Completed' : Str::headline($task->status) }}</span></span>
                </div>
            @empty
                <div class="pm-empty pm-empty--compact"><strong>No task details to show.</strong><span>Tasks matching this view will appear here.</span></div>
            @endforelse
        </div>
    </section>

    <div class="pm-grid-wide">
        <section class="panel pm-panel"><div class="pm-panel-head"><div><h3>Upcoming milestones</h3><p>Commitments that are approaching next.</p></div></div><div class="pm-list">@forelse ($upcomingMilestones as $milestone)<div class="pm-list-item"><div><strong>{{ $milestone->title }}</strong><span>{{ $milestone->project->project_number }} · due {{ optional($milestone->due_on)->format('M d, Y') }}</span></div><span class="pm-chip">{{ $milestone->status }}</span></div>@empty<div class="pm-empty">No upcoming milestones.</div>@endforelse</div></section>
        <section class="panel pm-panel"><div class="pm-panel-head"><div><h3>Team workload</h3><p>Open task distribution by assignee.</p></div></div><div class="pm-list">@forelse ($workload as $person)<div class="pm-list-item"><div><strong>{{ $person['name'] }}</strong><span>{{ $person['hours'] }} estimated hours</span></div><span class="pm-chip">{{ $person['count'] }} tasks</span></div>@empty<div class="pm-empty">No assigned tasks in this view.</div>@endforelse</div></section>
    </div>

    <section class="panel pm-panel"><div class="pm-panel-head"><div><h3>Recent activity</h3><p>Important changes across the visible workspace.</p></div><a class="pm-icon-link" href="{{ route('admin.project-management.notifications') }}">Notifications</a></div><div class="pm-list">@forelse ($recentActivity as $activity)<div class="pm-list-item"><div><strong>{{ Str::headline(str_replace('.', ' ', $activity->action)) }}</strong><span>{{ $activity->actor_name ?: 'System' }} · {{ $activity->project_name ?: 'Workspace' }} · {{ CarbonCarbon::parse($activity->created_at)->diffForHumans() }}</span></div></div>@empty<div class="pm-empty">Activity will appear as the team starts moving work.</div>@endforelse</div></section>
    </div>
    @endif
@endsection
