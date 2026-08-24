@extends('admin.project-management.layout', ['title' => 'Overview'])

@section('project-content')
    <section class="panel pm-hero">
        <div>
            <span class="eyebrow">Delivery control room</span>
            <h2>See the work that needs attention before it becomes noise.</h2>
            <p>Projects, deadlines, workload, and recent movement in one focused workspace. Every metric below is calculated from the project records your account can access.</p>
        </div>
        <div class="pm-actions">
            <a class="button" href="{{ route('admin.project-management.projects.create') }}">New project</a>
            <a class="ghost-button" href="{{ route('admin.project-management.reports') }}">View reports</a>
        </div>
    </section>

    <section class="pm-kpis">
        @foreach ([['Active projects', $totalProjects, 'Planning, active, and on-hold work'], ['Completed projects', $completedProjects, 'Delivered project records'], ['Overdue tasks', $overdueTasks, 'Open tasks past their due date'], ['My tasks', $myTasks, $todayTasks.' due today · '.$completedThisWeek.' completed this week']] as $kpi)
            <article class="panel pm-kpi">
                <span class="metric-label">{{ $kpi[0] }}</span>
                <strong>{{ number_format($kpi[1]) }}</strong>
                <small>{{ $kpi[2] }}</small>
            </article>
        @endforeach
    </section>

    <form class="panel pm-panel" method="GET" action="{{ route('admin.project-management.dashboard') }}">
        <div class="pm-panel-head"><div><h3>Filter delivery signals</h3><p>Use the same operational view across the dashboard.</p></div><a class="pm-icon-link" href="{{ route('admin.project-management.dashboard') }}">Reset filters</a></div>
        <div class="pm-form-grid">
            <div class="field"><label for="dashboard-project">Project</label><select id="dashboard-project" name="project_id"><option value="">All projects</option>@foreach ($projects as $project)<option value="{{ $project->id }}" @selected(($filters['project_id'] ?? '') == $project->id)>{{ $project->project_number }} · {{ $project->name }}</option>@endforeach</select></div>
            <div class="field"><label for="dashboard-member">Team member</label><select id="dashboard-member" name="assignee_id"><option value="">Everyone</option>@foreach ($members as $member)<option value="{{ $member->id }}" @selected(($filters['assignee_id'] ?? '') == $member->id)>{{ $member->name }}</option>@endforeach</select></div>
            <div class="field"><label for="dashboard-priority">Priority</label><select id="dashboard-priority" name="priority"><option value="">Every priority</option>@foreach (['low', 'medium', 'high', 'urgent'] as $priority)<option value="{{ $priority }}" @selected(($filters['priority'] ?? '') === $priority)>{{ Str::headline($priority) }}</option>@endforeach</select></div>
            <div class="field"><label for="dashboard-status">Status</label><select id="dashboard-status" name="status"><option value="">Every status</option>@foreach ($statusBreakdown->keys() as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ Str::headline($status) }}</option>@endforeach</select></div>
            <div class="field"><label for="dashboard-from">Due from</label><input id="dashboard-from" type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"></div>
            <div class="field"><label for="dashboard-to">Due to</label><input id="dashboard-to" type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"></div>
        </div>
        <div class="pm-form-actions"><button class="button" type="submit">Apply view</button></div>
    </form>
    <section class="panel pm-panel"><div class="pm-panel-head"><div><h3>Saved views</h3><p>Keep the filters your team uses every week.</p></div></div><div class="pm-actions" style="margin-bottom:12px">@forelse ($savedFilters as $savedFilter)<a class="pm-chip" href="{{ route('admin.project-management.dashboard', $savedFilter->filters ?: []) }}">{{ $savedFilter->name }}</a><form method="POST" action="{{ route('admin.project-management.filters.destroy', $savedFilter) }}">@csrf @method('DELETE')<button class="pm-icon-link" type="submit" aria-label="Remove {{ $savedFilter->name }}">×</button></form>@empty<span class="pm-muted">No saved views yet.</span>@endforelse</div><form class="pm-inline-form" method="POST" action="{{ route('admin.project-management.filters.store') }}">@csrf<div class="field"><label for="saved-filter-name">Save current filters as</label><input id="saved-filter-name" name="name" required placeholder="My open tasks"></div>@foreach ($filters as $key => $value)<input type="hidden" name="filters[{{ $key }}]" value="{{ $value }}">@endforeach<button class="ghost-button" type="submit">Save view</button></form></section>

    <div class="pm-grid">
        <section class="panel pm-panel">
            <div class="pm-panel-head"><div><h3>Project progress</h3><p>Task completion, with manual overrides respected where configured.</p></div><a class="pm-icon-link" href="{{ route('admin.project-management.projects') }}">All projects</a></div>
            <div class="pm-list">
                @forelse ($activeProjects->take(8) as $project)
                    <div class="pm-list-item">
                        <div style="min-width:0; flex:1"><strong><a href="{{ route('admin.project-management.projects.show', $project) }}">{{ $project->project_number }} · {{ $project->name }}</a></strong><span>{{ $project->client_company ?: $project->client_name ?: 'Internal project' }} · {{ Str::headline($project->status) }}</span><div class="pm-progress" style="margin-top:8px"><span style="width:{{ $project->progress_percentage }}%"></span></div></div>
                        <strong>{{ $project->progress_percentage }}%</strong>
                    </div>
                @empty
                    <div class="pm-empty">No active projects match this view.</div>
                @endforelse
            </div>
        </section>
        <section class="panel pm-panel">
            <div class="pm-panel-head"><div><h3>Task distribution</h3><p>Status and priority signals for the filtered task set.</p></div></div>
            <div class="pm-chart-bars"><strong class="pm-muted">By status</strong>@forelse ($statusBreakdown as $status => $count)<div class="pm-chart-bar"><span>{{ Str::headline($status) }}</span><div class="pm-progress"><span style="width:{{ $tasks->count() ? round(($count / $tasks->count()) * 100) : 0 }}%"></span></div><span>{{ $count }}</span></div>@empty<div class="pm-empty">No tasks in this view.</div>@endforelse</div>
            <div class="pm-chart-bars" style="margin-top:20px"><strong class="pm-muted">By priority</strong>@foreach ($priorityBreakdown as $priority => $count)<div class="pm-chart-bar"><span>{{ Str::headline($priority) }}</span><div class="pm-progress"><span style="width:{{ $tasks->count() ? round(($count / $tasks->count()) * 100) : 0 }}%; background:{{ $priority === 'urgent' ? 'var(--danger)' : 'var(--accent)' }}"></span></div><span>{{ $count }}</span></div>@endforeach</div>
        </section>
    </div>

    <div class="pm-grid-wide">
        <section class="panel pm-panel"><div class="pm-panel-head"><div><h3>Upcoming milestones</h3><p>Commitments that are approaching next.</p></div></div><div class="pm-list">@forelse ($upcomingMilestones as $milestone)<div class="pm-list-item"><div><strong>{{ $milestone->title }}</strong><span>{{ $milestone->project->project_number }} · due {{ optional($milestone->due_on)->format('M d, Y') }}</span></div><span class="pm-chip">{{ $milestone->status }}</span></div>@empty<div class="pm-empty">No upcoming milestones.</div>@endforelse</div></section>
        <section class="panel pm-panel"><div class="pm-panel-head"><div><h3>Team workload</h3><p>Open task distribution by assignee.</p></div></div><div class="pm-list">@forelse ($workload as $person)<div class="pm-list-item"><div><strong>{{ $person['name'] }}</strong><span>{{ $person['hours'] }} estimated hours</span></div><span class="pm-chip">{{ $person['count'] }} tasks</span></div>@empty<div class="pm-empty">No assigned tasks in this view.</div>@endforelse</div></section>
    </div>

    <section class="panel pm-panel"><div class="pm-panel-head"><div><h3>Recent activity</h3><p>Important changes across the visible workspace.</p></div><a class="pm-icon-link" href="{{ route('admin.project-management.notifications') }}">Notifications</a></div><div class="pm-list">@forelse ($recentActivity as $activity)<div class="pm-list-item"><div><strong>{{ Str::headline(str_replace('.', ' ', $activity->action)) }}</strong><span>{{ $activity->actor_name ?: 'System' }} · {{ $activity->project_name ?: 'Workspace' }} · {{ CarbonCarbon::parse($activity->created_at)->diffForHumans() }}</span></div></div>@empty<div class="pm-empty">Activity will appear as the team starts moving work.</div>@endforelse</div></section>
@endsection
