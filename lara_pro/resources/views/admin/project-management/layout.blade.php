@extends('admin.layouts.app')

@section('title', ($title ?? 'Project Management').' | Admin')

@push('styles')
    <style>
        .pm-shell { display: grid; gap: 18px; }
        .pm-subnav { display: flex; flex-wrap: wrap; gap: 7px; padding: 8px; border: 1px solid var(--line); border-radius: 16px; background: rgba(255,255,255,.78); }
        .pm-subnav a { display: inline-flex; min-height: 34px; align-items: center; padding: 0 11px; border-radius: 9px; color: var(--muted); font-size: 12px; font-weight: 700; }
        .pm-subnav a:hover, .pm-subnav a.active { background: var(--panel-soft); color: var(--accent-soft); }
        .pm-hero { display: flex; align-items: flex-end; justify-content: space-between; gap: 22px; padding: 24px; }
        .pm-hero h2 { max-width: 760px; margin: 6px 0 0; font-size: clamp(24px, 3vw, 36px); line-height: 1.1; }
        .pm-hero p { max-width: 700px; margin: 12px 0 0; color: var(--muted); line-height: 1.6; }
        .pm-actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
        .pm-kpis { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
        .pm-kpi { padding: 17px; }
        .pm-kpi strong { display: block; margin-top: 7px; font-size: 28px; line-height: 1; }
        .pm-kpi small { display: block; margin-top: 8px; color: var(--muted); line-height: 1.4; }
        .pm-grid { display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(280px, .65fr); gap: 16px; }
        .pm-grid-wide { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .pm-panel { padding: 18px; }
        .pm-panel h3 { margin: 0; font-size: 16px; }
        .pm-panel-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 14px; }
        .pm-panel-head p { margin: 5px 0 0; color: var(--muted); font-size: 12px; line-height: 1.45; }
        .pm-list { display: grid; gap: 8px; }
        .pm-list-item { display: flex; justify-content: space-between; gap: 12px; padding: 11px 0; border-top: 1px solid var(--line-soft); }
        .pm-list-item:first-child { border-top: 0; padding-top: 0; }
        .pm-list-item strong { display: block; font-size: 13px; }
        .pm-list-item span, .pm-muted { color: var(--muted); font-size: 12px; }
        .pm-progress { height: 7px; overflow: hidden; border-radius: 999px; background: #eee8dc; }
        .pm-progress span { display: block; height: 100%; border-radius: inherit; background: var(--accent); }
        .pm-project-card { display: grid; gap: 13px; }
        .pm-project-card-top { display: flex; justify-content: space-between; gap: 12px; }
        .pm-project-card h3 { margin: 0; font-size: 16px; }
        .pm-project-card h3 a:hover { color: var(--accent-soft); }
        .pm-project-meta { display: flex; flex-wrap: wrap; gap: 7px; color: var(--muted); font-size: 12px; }
        .pm-chip { display: inline-flex; align-items: center; min-height: 24px; padding: 0 8px; border: 1px solid var(--line); border-radius: 999px; background: var(--panel-soft); color: var(--muted-strong); font-size: 11px; font-weight: 750; }
        .pm-chip--danger { border-color: rgba(185,74,61,.25); background: #fff1ef; color: #9b3328; }
        .pm-chip--success { border-color: rgba(47,128,84,.2); background: #ecfdf5; color: #166534; }
        .pm-table-wrap { overflow-x: auto; }
        .pm-table { width: 100%; min-width: 680px; border-collapse: collapse; }
        .pm-table th, .pm-table td { padding: 12px 10px; border-bottom: 1px solid var(--line-soft); text-align: left; vertical-align: middle; }
        .pm-table th { color: var(--muted); font-size: 10px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        .pm-table td { color: var(--text); font-size: 13px; }
        .pm-table td strong { display: block; }
        .pm-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .pm-form-grid .field-full { grid-column: 1 / -1; }
        .pm-form-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 16px; }
        .pm-board { display: flex; gap: 12px; overflow-x: auto; align-items: flex-start; padding-bottom: 12px; }
        .pm-column { width: 286px; min-width: 286px; padding: 10px; border: 1px solid var(--line); border-radius: 14px; background: rgba(255,255,255,.72); }
        .pm-column-head { display: flex; justify-content: space-between; gap: 8px; align-items: center; padding: 4px 3px 10px; }
        .pm-column-head strong { font-size: 13px; }
        .pm-column-head span { color: var(--muted); font-size: 11px; }
        .pm-column-list { display: grid; gap: 8px; min-height: 60px; }
        .pm-task-card { display: grid; gap: 9px; padding: 12px; border: 1px solid var(--line-soft); border-radius: 11px; background: #fff; box-shadow: 0 5px 15px rgba(70,48,15,.05); cursor: grab; }
        .pm-task-card.is-dragging { opacity: .45; }
        .pm-task-card h4 { margin: 0; font-size: 13px; line-height: 1.35; }
        .pm-task-card h4 a:hover { color: var(--accent-soft); }
        .pm-task-top, .pm-task-bottom { display: flex; justify-content: space-between; align-items: center; gap: 8px; }
        .pm-task-key { color: var(--accent-soft); font-size: 10px; font-weight: 800; letter-spacing: .06em; }
        .pm-avatar { display: inline-grid; width: 25px; height: 25px; place-items: center; border-radius: 50%; background: #efe2bc; color: #684b00; font-size: 10px; font-weight: 800; }
        .pm-card-labels { display: flex; flex-wrap: wrap; gap: 4px; }
        .pm-card-labels span { padding: 3px 6px; border-radius: 5px; color: #684b00; background: #fff4cf; font-size: 10px; }
        .pm-task-bottom { color: var(--muted); font-size: 11px; }
        .pm-task-bottom .is-overdue { color: var(--danger); font-weight: 800; }
        .pm-stat-row { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; }
        .pm-stat { padding: 10px; border: 1px solid var(--line-soft); border-radius: 10px; background: var(--panel-soft); }
        .pm-stat strong { display: block; font-size: 19px; }
        .pm-stat span { color: var(--muted); font-size: 10px; }
        .pm-empty { padding: 26px 12px; color: var(--muted); text-align: center; font-size: 13px; }
        .pm-tabs { display: flex; flex-wrap: wrap; gap: 7px; margin-bottom: 15px; }
        .pm-tabs a { padding: 8px 10px; border: 1px solid var(--line); border-radius: 8px; color: var(--muted); font-size: 12px; font-weight: 700; }
        .pm-tabs a.active, .pm-tabs a:hover { background: var(--panel-soft); color: var(--accent-soft); }
        .pm-detail-grid { display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(280px, .65fr); gap: 16px; }
        .pm-stack { display: grid; gap: 12px; }
        .pm-inline-form { display: flex; flex-wrap: wrap; gap: 8px; align-items: flex-end; }
        .pm-inline-form .field { flex: 1 1 150px; }
        .pm-callout { padding: 12px; border: 1px solid var(--line); border-radius: 10px; background: var(--panel-soft); color: var(--muted-strong); font-size: 12px; line-height: 1.5; }
        .pm-icon-link { color: var(--accent-soft); font-size: 12px; font-weight: 750; }
        .pm-icon-link:hover { text-decoration: underline; }
        .pm-chart-bars { display: grid; gap: 9px; }
        .pm-chart-bar { display: grid; grid-template-columns: 100px minmax(0,1fr) 32px; align-items: center; gap: 8px; font-size: 11px; }
        .pm-chart-bar > span:first-child { color: var(--muted); }
        .pm-chart-bar > span:last-child { text-align: right; font-weight: 800; }
        @media (max-width: 980px) { .pm-kpis { grid-template-columns: repeat(2, minmax(0,1fr)); } .pm-grid, .pm-detail-grid { grid-template-columns: 1fr; } }
        @media (max-width: 640px) { .pm-kpis, .pm-grid-wide, .pm-form-grid { grid-template-columns: 1fr; } .pm-hero { align-items: flex-start; flex-direction: column; padding: 18px; } .pm-subnav { overflow-x: auto; flex-wrap: nowrap; } .pm-subnav a { flex: 0 0 auto; } .pm-panel { padding: 14px; } }
    </style>
@endpush

@section('content')
    <div class="pm-shell">
        <nav class="pm-subnav" aria-label="Project management navigation">
            <a class="{{ request()->routeIs('admin.project-management.dashboard') ? 'active' : '' }}" href="{{ route('admin.project-management.dashboard') }}">Overview</a>
            <a class="{{ request()->routeIs('admin.project-management.dashboard') && request('assignee_id') ? 'active' : '' }}" href="{{ route('admin.project-management.dashboard', ['assignee_id' => \App\Support\ProjectManagementAccess::user()?->id]) }}">My Tasks</a>
            <a class="{{ request()->routeIs('admin.project-management.search') ? 'active' : '' }}" href="{{ route('admin.project-management.search') }}">Search</a>
            <a class="{{ request()->routeIs('admin.project-management.projects*') && ! request()->routeIs('admin.project-management.archived') ? 'active' : '' }}" href="{{ route('admin.project-management.projects') }}">Projects</a>
            @if (isset($project) && $project)
                <a class="{{ request()->routeIs('admin.project-management.board') ? 'active' : '' }}" href="{{ route('admin.project-management.board', $project) }}">Board</a>
                <a class="{{ request()->routeIs('admin.project-management.backlog') ? 'active' : '' }}" href="{{ route('admin.project-management.backlog', $project) }}">Backlog</a>
                <a class="{{ request()->routeIs('admin.project-management.sprints') ? 'active' : '' }}" href="{{ route('admin.project-management.sprints', $project) }}">Sprints</a>
            @endif
            <a class="{{ request()->routeIs('admin.project-management.calendar') ? 'active' : '' }}" href="{{ route('admin.project-management.calendar') }}">Calendar</a>
            <a class="{{ request()->routeIs('admin.project-management.team') ? 'active' : '' }}" href="{{ route('admin.project-management.team') }}">Team</a>
            <a class="{{ request()->routeIs('admin.project-management.reports') ? 'active' : '' }}" href="{{ route('admin.project-management.reports') }}">Reports</a>
            <a class="{{ request()->routeIs('admin.project-management.archived') ? 'active' : '' }}" href="{{ route('admin.project-management.archived') }}">Archived</a>
            @if (isset($project) && $project)
                <a class="{{ request()->routeIs('admin.project-management.settings') ? 'active' : '' }}" href="{{ route('admin.project-management.settings', $project) }}">Settings</a>
            @endif
            <a class="{{ request()->routeIs('admin.project-management.notifications') ? 'active' : '' }}" href="{{ route('admin.project-management.notifications') }}">Notifications</a>
        </nav>

        @yield('project-content')
    </div>
@endsection
