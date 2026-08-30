@extends('admin.layouts.app')

@section('title', ($title ?? 'Project Management').' | Admin')

@php
    $pmNotificationUserId = \App\Support\ProjectManagementAccess::user()?->id;
    $pmUnreadNotifications = $pmNotificationUserId && \App\Support\AdminAccess::can('projects')
        ? \Illuminate\Support\Facades\DB::table('notifications')->where('notifiable_type', \App\Models\User::class)->where('notifiable_id', $pmNotificationUserId)->whereNull('read_at')->count()
        : 0;
@endphp

@push('styles')
    <style>
        .pm-shell { display: grid; gap: 18px; align-content: start; }
        .pm-subnav { position: relative; display: flex; flex-wrap: nowrap; align-items: center; gap: 7px; padding: 8px; border: 1px solid var(--line); border-radius: 16px; background: rgba(255,255,255,.78); overflow: visible; }
        .pm-subnav > a, .pm-subnav-more { flex: 0 0 auto; }
        .pm-subnav a { display: inline-flex; min-height: 34px; align-items: center; padding: 0 11px; border-radius: 9px; color: var(--muted); font-size: 12px; font-weight: 700; }
        .pm-subnav a:hover, .pm-subnav a.active { background: var(--panel-soft); color: var(--muted-strong); }
        .pm-subnav-more { position: relative; }
        .pm-subnav-more summary { display: inline-flex; min-height: 34px; align-items: center; padding: 0 11px; border-radius: 9px; color: var(--muted); cursor: pointer; font-size: 12px; font-weight: 700; list-style: none; }
        .pm-subnav-more summary::-webkit-details-marker { display: none; }
        .pm-subnav-more summary::after { content: '⌄'; margin-left: 6px; font-size: 11px; }
        .pm-subnav-more[open] summary, .pm-subnav-more summary:hover { background: var(--panel-soft); color: var(--muted-strong); }
        .pm-subnav-more__items { position: absolute; top: calc(100% + 6px); right: 0; z-index: 70; display: grid; min-width: 170px; gap: 2px; padding: 6px; border: 1px solid var(--line); border-radius: 10px; background: var(--surface, #fff); box-shadow: 0 12px 28px rgba(31,38,48,.12); }
        .pm-subnav-more__items a { width: 100%; justify-content: flex-start; white-space: nowrap; }
        .pm-notification-count { display: inline-grid; min-width: 17px; height: 17px; margin-left: 4px; place-items: center; border-radius: 999px; background: var(--primary); color: #fff; font-size: 9px; font-weight: 800; line-height: 1; }
        .pm-dashboard .pm-panel-head p { display: none; }
        .pm-dashboard .pm-panel-head { margin-bottom: 10px; }
        .pm-dashboard .pm-kpi small { display: none; }
        .pm-board-view[data-read-only="1"] .pm-hero .button,
        .pm-board-view[data-read-only="1"] .pm-task-card { cursor: default; }
        .pm-board-view[data-read-only="1"] .pm-task-card [data-keyboard-move] { display: none; }
        .pm-read-only-note { display: flex; justify-content: space-between; gap: 12px; color: var(--muted); }
        .pm-read-only-note strong { color: var(--text); }
        .pm-project-view .pm-panel-head p { display: none; }
        .pm-project-view[data-read-only="1"] form { display: none; }
        .pm-project-view[data-read-only="1"] .pm-tabs a[href*="settings"] { display: none; }
        .pm-project-view[data-read-only="1"] > section.panel.pm-panel:last-child { display: none; }
        .pm-project-view .pm-hero { align-items: center; padding: 22px 26px; }
        body.is-admin .pm-project-view .pm-hero h2 { color: var(--text); font-size: 25px !important; letter-spacing: -.035em; }
        .pm-project-view .pm-hero > div:first-child { min-width: 0; }
        .pm-project-view .pm-hero > div:first-child > p { display: block !important; max-width: 720px; margin: 8px 0 0; color: var(--muted); font-size: 12px; line-height: 1.5; }
        .pm-project-view .pm-hero .pm-project-meta { margin-top: 11px !important; }
        .pm-project-view .pm-stat-row { gap: 10px; }
        .pm-project-view .pm-stat { min-height: 76px; display: grid; align-content: center; gap: 5px; padding: 13px 15px; }
        .pm-project-view .pm-stat strong { color: var(--text); font-size: 23px; line-height: 1; }
        .pm-project-view .pm-tabs { gap: 6px; margin: 0; }
        .pm-project-view .pm-tabs a { display: inline-flex; min-height: 38px; align-items: center; padding-inline: 13px; }
        .pm-project-view .pm-tabs a.active { border-color: #1c1e22; background: #1c1e22; color: #fff; }
        .pm-project-view .pm-detail-grid { align-items: start; }
        .pm-project-view .pm-stack { align-content: start; }
        .pm-project-view .pm-stack > .pm-panel { min-height: 0; }
        .pm-project-view .pm-panel-head { align-items: center; }
        .pm-project-view .pm-empty { display: grid; min-height: 76px; align-content: center; justify-items: start; gap: 5px; padding: 16px 0; text-align: left; }
        .pm-project-view .pm-empty strong { color: var(--text); font-size: 13px; }
        .pm-project-view .pm-empty span { color: var(--muted); font-size: 11px; }
        .pm-project-view .pm-empty .pm-icon-link { justify-content: flex-start; margin-top: 2px; }
        .pm-project-view .pm-inline-form { align-items: end; }
        .pm-project-view .pm-inline-form .field,
        .pm-project-view .pm-inline-form select { min-width: 0; }
        .pm-task-view .pm-panel-head p { display: none; }
        .pm-task-view[data-read-only="1"] form:not(.pm-list-item) { display: none; }
        .pm-task-view[data-read-only="1"] form.pm-list-item { pointer-events: none; }
        .pm-task-view[data-read-only="1"] form.pm-list-item input { visibility: hidden; }
        .pm-project-list[data-read-only="1"] .pm-hero .button,
        .pm-project-list[data-read-only="1"] .pm-empty a { display: none; }
        .pm-backlog-view[data-read-only="1"] form { display: none; }
        .pm-sprints-view[data-read-only="1"] form { display: none; }
        .pm-sprints-view[data-read-only="1"] > .pm-grid > section:last-child { display: none; }
        .pm-hero { display: flex; align-items: flex-end; justify-content: space-between; gap: 22px; padding: 24px; }
        .pm-hero h2 { max-width: 760px; margin: 6px 0 0; font-size: clamp(24px, 3vw, 36px); line-height: 1.1; }
        .pm-hero p { max-width: 700px; margin: 12px 0 0; color: var(--muted); line-height: 1.6; }
        .pm-actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
        .pm-staff-task-summary { display: grid; grid-template-columns: minmax(0, 1fr) minmax(280px, .72fr); gap: 18px; align-items: stretch; }
        .pm-staff-task-summary .pm-hero { align-items: center; min-height: 100%; }
        .pm-dashboard-countdown { min-width: 0; min-height: 132px; }
        .pm-dashboard-countdown__task { position: relative; z-index: 1; overflow: hidden; color: var(--muted-strong); font-size: 12px; font-weight: 750; text-overflow: ellipsis; white-space: nowrap; }
        .pm-dashboard-countdown__task:hover { color: var(--primary-strong); }
        .pm-daily-motivation { position: relative; isolation: isolate; display: grid; grid-template-columns: 42px minmax(0, 1fr); align-items: center; gap: 16px; min-height: 132px; padding: 20px 22px; overflow: hidden; border: 1px solid rgba(184,134,11,.22); border-radius: 14px; background: radial-gradient(circle at 93% 12%, rgba(255,255,255,.9), transparent 28%), linear-gradient(120deg, #fff8e6 0%, #fffdf8 57%, #f8f0dd 100%); box-shadow: 0 13px 30px rgba(102,76,20,.07); animation: pm-motivation-rise .72s cubic-bezier(.2,.75,.25,1) both; }
        .pm-daily-motivation::before { position: absolute; inset: -80% 30% -80% -20%; z-index: -1; content: ''; background: linear-gradient(105deg, transparent 35%, rgba(255,255,255,.58) 50%, transparent 65%); transform: translateX(-55%); animation: pm-motivation-sheen 7s ease-in-out 1s infinite; }
        .pm-daily-motivation__mark { display: grid; width: 42px; height: 42px; place-items: center; border: 1px solid rgba(184,134,11,.28); border-radius: 13px; background: rgba(255,255,255,.68); color: var(--primary); box-shadow: 0 0 0 6px rgba(184,134,11,.06); animation: pm-motivation-pulse 3.8s ease-in-out infinite; }
        .pm-daily-motivation__mark svg { width: 24px; height: 24px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.6; }
        .pm-daily-motivation__content { min-width: 0; animation: pm-motivation-copy .7s .12s cubic-bezier(.2,.75,.25,1) both; }
        .pm-daily-motivation__eyebrow { display: flex; flex-wrap: wrap; gap: 8px 14px; color: var(--primary-strong); font-size: 9px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
        .pm-daily-motivation__eyebrow span + span { color: var(--muted); font-weight: 650; letter-spacing: .04em; }
        .pm-daily-motivation blockquote { max-width: 780px; margin: 8px 0 5px; color: var(--text); font-family: var(--font-display); font-size: clamp(17px, 2vw, 23px); font-weight: 700; letter-spacing: -.035em; line-height: 1.25; }
        .pm-daily-motivation cite { color: var(--muted); font-size: 11px; font-style: normal; }
        .pm-daily-motivation__orbit { position: absolute; top: -44px; right: 52px; width: 156px; height: 156px; border: 1px solid rgba(184,134,11,.14); border-radius: 50%; animation: pm-motivation-orbit 14s linear infinite; }
        .pm-daily-motivation__orbit::before, .pm-daily-motivation__orbit::after { position: absolute; inset: 20px; border: 1px solid rgba(184,134,11,.1); border-radius: 50%; content: ''; }
        .pm-daily-motivation__orbit::after { inset: 48px; background: rgba(184,134,11,.08); border: 0; }
        .pm-daily-motivation__orbit span { position: absolute; width: 6px; height: 6px; border-radius: 50%; background: var(--primary); box-shadow: 0 0 0 4px rgba(184,134,11,.1); }
        .pm-daily-motivation__orbit span:nth-child(1) { top: 13px; left: 77px; }
        .pm-daily-motivation__orbit span:nth-child(2) { right: 13px; bottom: 46px; width: 4px; height: 4px; background: #d8a93b; }
        .pm-daily-motivation__orbit span:nth-child(3) { bottom: 22px; left: 34px; width: 4px; height: 4px; background: #e1c77d; }
        @keyframes pm-motivation-rise { from { opacity: 0; transform: translateY(12px) scale(.985); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes pm-motivation-copy { from { opacity: 0; transform: translateX(-9px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes pm-motivation-pulse { 0%, 100% { box-shadow: 0 0 0 6px rgba(184,134,11,.06); transform: translateY(0); } 50% { box-shadow: 0 0 0 10px rgba(184,134,11,.025); transform: translateY(-2px); } }
        @keyframes pm-motivation-sheen { 0%, 14% { transform: translateX(-55%); } 42%, 56% { transform: translateX(80%); } 100% { transform: translateX(80%); } }
        @keyframes pm-motivation-orbit { to { transform: rotate(360deg); } }
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
        .pm-chip--warning { border-color: rgba(212,139,45,.28); background: #fff8e8; color: #a35f18; }
        .pm-backlog-view { gap: 16px; }
        .pm-backlog-hero { align-items: center; padding: 22px 26px; }
        .pm-backlog-hero h2 { color: var(--text); font-size: clamp(24px, 2.6vw, 32px); letter-spacing: -.04em; }
        .pm-backlog-hero p { max-width: 620px; margin-top: 9px; font-size: 12px; line-height: 1.5; }
        .pm-backlog-overview { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
        .pm-backlog-stat { display: flex; min-height: 76px; align-items: center; gap: 12px; padding: 13px 16px; border: 1px solid var(--line-soft); border-radius: 10px; background: rgba(255,255,255,.82); }
        .pm-backlog-stat--accent { border-top: 2px solid var(--primary); }
        .pm-backlog-stat__icon { display: grid; width: 30px; height: 30px; flex: 0 0 30px; place-items: center; border: 1px solid rgba(184,134,11,.2); border-radius: 9px; background: var(--primary-soft); color: var(--primary-strong); font-size: 16px; font-weight: 800; }
        .pm-backlog-stat > div { display: grid; min-width: 0; gap: 3px; }
        .pm-backlog-stat strong { overflow: hidden; color: var(--text); font-size: 19px; line-height: 1.1; text-overflow: ellipsis; white-space: nowrap; }
        .pm-backlog-stat span:not(.pm-backlog-stat__icon) { color: var(--muted); font-size: 10px; font-weight: 700; }
        .pm-backlog-panel { padding: 20px; }
        .pm-backlog-panel__head { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; margin-bottom: 16px; }
        .pm-backlog-panel__head .pm-panel-head { margin: 0; }
        .pm-section-kicker { display: block; margin-bottom: 5px; color: var(--primary-strong); font-size: 9px; font-weight: 850; letter-spacing: .12em; text-transform: uppercase; }
        .pm-backlog-panel__head h3 { font-size: 18px; letter-spacing: -.025em; }
        .pm-backlog-panel__head .pm-panel-head p { margin-top: 6px; }
        .pm-backlog-count { display: inline-flex; align-items: baseline; gap: 4px; padding: 7px 10px; border: 1px solid var(--line-soft); border-radius: 8px; color: var(--muted); font-size: 10px; white-space: nowrap; }
        .pm-backlog-count strong { color: var(--text); font-size: 15px; }
        .pm-backlog-table { overflow: hidden; border: 1px solid var(--line-soft); border-radius: 10px; background: #fff; }
        .pm-backlog-row { display: grid; grid-template-columns: minmax(250px, 1.45fr) minmax(105px, .55fr) minmax(150px, .8fr) minmax(300px, 1.25fr); gap: 18px; align-items: center; padding: 14px 16px; border-top: 1px solid var(--line-soft); }
        .pm-backlog-row:first-child { border-top: 0; }
        .pm-backlog-row--head { padding-top: 11px; padding-bottom: 10px; background: #fcfaf5; border-top: 0; color: var(--muted); font-size: 9px; font-weight: 850; letter-spacing: .1em; text-transform: uppercase; }
        .pm-backlog-cell { min-width: 0; }
        .pm-backlog-cell--task { display: flex; align-items: center; gap: 11px; }
        .pm-backlog-task-mark { display: grid; width: 30px; height: 30px; flex: 0 0 30px; place-items: center; border-radius: 9px; background: #f4eee2; color: var(--primary-strong); font-size: 11px; font-weight: 850; }
        .pm-backlog-cell--task > div { display: grid; min-width: 0; gap: 2px; }
        .pm-backlog-task-key { color: var(--primary-strong); font-size: 9px; font-weight: 850; letter-spacing: .07em; }
        .pm-backlog-cell--task strong { overflow: hidden; font-size: 13px; line-height: 1.3; text-overflow: ellipsis; white-space: nowrap; }
        .pm-backlog-cell--task strong a:hover { color: var(--primary-strong); }
        .pm-backlog-task-meta { color: var(--muted); font-size: 10px; }
        .pm-backlog-cell--owner { display: flex; align-items: center; gap: 8px; color: var(--muted-strong); font-size: 11px; }
        .pm-backlog-cell--owner .pm-avatar { flex: 0 0 25px; }
        .pm-backlog-assign { display: flex; align-items: center; gap: 7px; }
        .pm-backlog-assign select { min-width: 0; flex: 1 1 auto; height: 34px; padding-inline: 9px 27px; border-color: var(--line); border-radius: 7px; background-color: #fffdf8; font-size: 11px; }
        .pm-assign-button { display: inline-flex; min-height: 34px; flex: 0 0 auto; align-items: center; gap: 8px; padding: 0 11px; border: 1px solid #1c1e22; border-radius: 7px; background: #1c1e22; color: #fff; cursor: pointer; font-size: 11px; font-weight: 800; }
        .pm-assign-button:hover { background: var(--primary-strong); border-color: var(--primary-strong); }
        .pm-backlog-empty { display: flex; min-height: 142px; align-items: center; justify-content: center; gap: 13px; padding: 22px; text-align: left; }
        .pm-backlog-empty__icon { display: grid; width: 38px; height: 38px; place-items: center; border-radius: 50%; background: #ecfdf5; color: var(--success); font-weight: 850; }
        .pm-backlog-empty > div { display: grid; gap: 3px; }
        .pm-backlog-empty strong { color: var(--text); font-size: 13px; }
        .pm-backlog-empty span { color: var(--muted); font-size: 11px; }
        .pm-backlog-empty .pm-icon-link { margin-left: 13px; }
        .pm-backlog-pagination { margin-top: 14px; }
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
        @media (max-width: 900px) {
            .pm-backlog-row--head { display: none; }
            .pm-backlog-row { grid-template-columns: 1fr; gap: 12px; padding: 15px; }
            .pm-backlog-cell:not(.pm-backlog-cell--task) { display: grid; grid-template-columns: 105px minmax(0, 1fr); align-items: center; gap: 10px; }
            .pm-backlog-cell:not(.pm-backlog-cell--task)::before { content: attr(data-label); color: var(--muted); font-size: 9px; font-weight: 850; letter-spacing: .08em; text-transform: uppercase; }
            .pm-backlog-cell--action .pm-backlog-assign { min-width: 0; }
            .pm-backlog-empty { flex-direction: column; text-align: center; }
            .pm-backlog-empty .pm-icon-link { margin: 0; }
        }
        .pm-task-hero { display: grid; grid-template-columns: minmax(0, 1fr) minmax(286px, 320px) auto; align-items: center; gap: 28px; min-height: 164px; padding: 27px 30px; }
        .pm-task-hero h2 { max-width: 720px; margin-top: 10px; color: var(--text); font-size: clamp(26px, 3vw, 38px); letter-spacing: -.045em; line-height: 1.06; }
        .pm-task-hero > div:first-child { min-width: 0; }
        .pm-task-hero > div:first-child > p { max-width: 620px; margin-top: 11px; font-size: 12px; line-height: 1.5; }
        .pm-task-hero__actions { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: 9px; min-width: max-content; margin-left: 0; }
        .pm-task-hero__actions > a,
        .pm-task-hero__actions > form > button { min-height: 42px; padding-inline: 15px; }
        .pm-danger-button { border-color: rgba(194,65,85,.24); color: var(--danger); }
        .pm-danger-button:hover { border-color: rgba(194,65,85,.42); background: #fff3f4; color: #a52f43; }
        .pm-task-countdown { position: relative; display: grid; width: 100%; min-width: 0; min-height: 110px; align-content: center; gap: 6px; padding: 16px 18px 15px 20px; overflow: hidden; border: 1px solid rgba(184,134,11,.32); border-left: 4px solid var(--primary); border-radius: 14px; background: linear-gradient(135deg, #fffbed 0%, #fff8e8 100%); box-shadow: 0 10px 24px rgba(102,76,20,.09); }
        .pm-task-countdown::after { position: absolute; right: -22px; bottom: -32px; width: 94px; height: 94px; border: 1px solid rgba(184,134,11,.13); border-radius: 50%; content: ''; }
        .pm-task-countdown > span:first-child { position: relative; z-index: 1; color: var(--primary-strong); font-size: 10px; font-weight: 850; letter-spacing: .12em; text-transform: uppercase; }
        .pm-task-countdown strong { position: relative; z-index: 1; color: var(--text); font-size: clamp(25px, 2.1vw, 32px); font-variant-numeric: tabular-nums; letter-spacing: -.045em; line-height: 1; white-space: nowrap; }
        .pm-task-countdown small { position: relative; z-index: 1; color: var(--muted); font-size: 11px; line-height: 1.3; }
        .pm-task-countdown__deadline { position: relative; z-index: 1; color: var(--muted-strong); font-size: 10px; font-weight: 700; letter-spacing: .01em; }
        .pm-task-countdown[data-countdown-state="active"] { border-color: rgba(184,134,11,.38); }
        .pm-task-countdown[data-countdown-state="urgent"] { border-color: rgba(212,139,45,.42); background: #fff8e8; }
        .pm-task-countdown[data-countdown-state="urgent"] strong { color: #b56e22; }
        .pm-task-countdown[data-countdown-state="overdue"] { border-color: rgba(194,65,85,.3); background: #fff3f4; }
        .pm-task-countdown[data-countdown-state="overdue"] > span,
        .pm-task-countdown[data-countdown-state="overdue"] strong { color: var(--danger); }
        .pm-task-edit-panel .pm-panel-head h3 { margin-top: 4px; color: var(--text); font-size: 21px; letter-spacing: -.03em; }
        .pm-task-edit-panel .pm-panel-head p { max-width: 600px; }
        .pm-task-form-primary { gap: 13px 14px; }
        .pm-task-form-primary .field-full:last-child { margin-top: 2px; }
        .pm-advanced-fields { margin-top: 18px; border-top: 1px solid var(--line-soft); }
        .pm-advanced-fields summary { display: flex; min-height: 48px; align-items: center; justify-content: space-between; gap: 12px; color: var(--text); cursor: pointer; font-size: 12px; font-weight: 800; list-style: none; }
        .pm-advanced-fields summary::-webkit-details-marker { display: none; }
        .pm-advanced-fields summary::after { width: 7px; height: 7px; flex: 0 0 7px; margin-right: 3px; border-right: 1.5px solid currentColor; border-bottom: 1.5px solid currentColor; content: ''; transform: rotate(45deg); transition: transform .18s ease; }
        .pm-advanced-fields[open] summary::after { transform: rotate(225deg); }
        .pm-advanced-fields summary span { margin-left: auto; color: var(--muted); font-size: 10px; font-weight: 500; }
        .pm-advanced-fields > .pm-form-grid { padding: 4px 0 2px; }
        .pm-task-label-options { gap: 7px; margin-top: 7px; }
        .pm-task-label-options .pm-chip { min-height: 30px; gap: 6px; cursor: pointer; }
        .pm-task-view .is-overdue { color: var(--danger); }
        .pm-completion-note { display: grid; gap: 6px; border-color: rgba(184,134,11,.2); background: #fffbed; }
        .pm-completion-note strong { color: var(--text); font-size: 13px; }
        .pm-completion-note span { color: var(--muted); font-size: 11px; line-height: 1.5; }
        .pm-notifications-list .pm-list-item { align-items: center; padding: 15px 0; }
        .pm-notifications-list .pm-list-item.is-unread { padding-left: 12px; border-left: 3px solid var(--primary); background: linear-gradient(90deg, #fffbed, transparent); }
        .pm-notification-message { color: var(--text); font-size: 13px; }
        .pm-notification-meta { display: block; margin-top: 5px; color: var(--muted); font-size: 11px; }
        .pm-notification-type { display: inline-flex; margin-bottom: 5px; color: var(--primary-strong); font-size: 9px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
        form.is-saving { opacity: .72; pointer-events: none; }
        .pm-board-hero { border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; }
        .pm-board-hero h2 { color: var(--text); letter-spacing: -.035em; }
        .pm-board-toolbar { display: grid; gap: 16px; padding: 16px 20px; border-radius: 10px; background: #fff; }
        .pm-board-toolbar__topline { display: flex; align-items: center; justify-content: space-between; gap: 18px; }
        .pm-board-toolbar__topline strong { display: block; margin-top: 3px; color: var(--text); font-size: 15px; }
        .pm-board-toolbar__eyebrow, .pm-board-team__label { color: var(--muted); font-size: 10px; font-weight: 800; letter-spacing: .11em; text-transform: uppercase; }
        .pm-board-toolbar__hint { display: inline-flex; align-items: center; gap: 7px; color: var(--muted); font-size: 11px; }
        .pm-board-toolbar__drag-mark { color: var(--primary); font-size: 15px; font-weight: 900; letter-spacing: -4px; }
        .pm-board-toolbar__controls { padding-top: 14px; border-top: 1px solid var(--line-soft); }
        .pm-board-toolbar__controls .pm-inline-form { align-items: end; }
        .pm-board-toolbar__controls .field { flex: 0 1 190px; }
        .pm-board-toolbar__controls .field label { display: block; margin-bottom: 5px; color: var(--muted); font-size: 10px; font-weight: 750; letter-spacing: .05em; text-transform: uppercase; }
        .pm-board-toolbar__controls select { min-height: 36px; border-color: #e1e5e9; border-radius: 8px; background: #fbfcfc; font-size: 12px; }
        .pm-chip--check { min-height: 36px; gap: 7px; border-radius: 8px; background: #fbfcfc; cursor: pointer; }
        .pm-chip--check input { accent-color: var(--primary); }
        .pm-board-team { display: flex; align-items: center; flex-wrap: wrap; gap: 7px; }
        .pm-board-team__label { margin-right: 3px; }
        .pm-team-filter { display: inline-flex; min-height: 34px; align-items: center; gap: 7px; padding: 0 9px 0 5px; border: 1px solid #e3e6e9; border-radius: 8px; background: #fff; color: var(--muted-strong); cursor: pointer; font: inherit; font-size: 11px; font-weight: 650; transition: border-color .16s ease, background .16s ease, color .16s ease, box-shadow .16s ease; }
        .pm-team-filter:hover { border-color: rgba(184,134,11,.45); background: #fffdf5; }
        .pm-team-filter.is-active { border-color: rgba(184,134,11,.62); background: #fffbed; color: var(--primary-strong); box-shadow: inset 0 0 0 1px rgba(184,134,11,.08); }
        .pm-team-filter b { min-width: 16px; color: var(--muted); font-size: 10px; text-align: center; }
        .pm-team-filter .pm-avatar { width: 24px; height: 24px; }
        .pm-board--trello { display: flex; gap: 13px; overflow-x: auto; align-items: stretch; padding: 15px; border: 1px solid #e1e4e7; border-radius: 12px; background: #f1f2f4; scrollbar-color: #c6c9ce transparent; }
        .pm-board--trello .pm-column { display: flex; width: 306px; min-width: 306px; flex-direction: column; padding: 0; border: 0; border-radius: 9px; background: #e7e9ec; box-shadow: 0 1px 1px rgba(31,35,40,.05); }
        .pm-board--trello .pm-column-head { min-height: 48px; padding: 11px 12px 9px; }
        .pm-column-title { display: inline-flex; min-width: 0; align-items: center; gap: 8px; }
        .pm-column-title strong { overflow: hidden; color: #3d4650; font-size: 12px; text-overflow: ellipsis; white-space: nowrap; }
        .pm-column-dot { width: 8px; height: 8px; flex: 0 0 8px; border-radius: 50%; background: var(--column-color, var(--primary)); box-shadow: 0 0 0 3px color-mix(in srgb, var(--column-color, var(--primary)) 14%, transparent); }
        .pm-column-count { display: inline-grid; min-width: 22px; height: 20px; align-items: center; justify-content: center; padding: 0 5px; border-radius: 5px; background: rgba(255,255,255,.72); color: #737b84 !important; font-size: 10px !important; font-weight: 800; }
        .pm-column-add { display: inline-grid; width: 26px; height: 26px; place-items: center; border-radius: 6px; color: #68717b; font-size: 20px; line-height: 1; transition: background .16s ease, color .16s ease; }
        .pm-column-add:hover { background: rgba(255,255,255,.75); color: var(--primary-strong); }
        .pm-board--trello .pm-column-list { position: relative; display: flex; min-height: 155px; flex: 1 1 auto; flex-direction: column; gap: 9px; padding: 0 8px 9px; border-radius: 0 0 9px 9px; }
        .pm-board--trello .pm-column-list:focus-visible { outline: 2px solid var(--primary); outline-offset: -2px; }
        .pm-task-card { position: relative; display: grid; gap: 10px; padding: 12px; border: 1px solid #dfe2e5; border-radius: 8px; background: #fff; box-shadow: 0 1px 1px rgba(31,35,40,.09); cursor: grab; transition: box-shadow .16s ease, border-color .16s ease, opacity .16s ease, transform .16s ease; }
        .pm-task-card:hover { border-color: #c9cdd2; box-shadow: 0 4px 10px rgba(31,35,40,.12); transform: translateY(-1px); }
        .pm-task-card:focus-within { border-color: rgba(184,134,11,.6); box-shadow: 0 0 0 2px rgba(184,134,11,.14); }
        .pm-task-card.is-dragging { opacity: .42; transform: rotate(1deg); }
        .pm-task-card.is-saving { opacity: .62; pointer-events: none; }
        .pm-task-card__top, .pm-task-card__details, .pm-task-card__footer { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
        .pm-task-card__top { min-height: 18px; }
        .pm-task-card__top .pm-task-key { color: var(--primary-strong); }
        .pm-task-type { overflow: hidden; margin-left: auto; color: #89919a; font-size: 9px; font-weight: 750; letter-spacing: .05em; text-overflow: ellipsis; text-transform: uppercase; white-space: nowrap; }
        .pm-task-grip { color: #a3a9af; font-size: 15px; font-weight: 900; letter-spacing: -4px; line-height: .7; opacity: 0; transition: opacity .16s ease; }
        .pm-task-card:hover .pm-task-grip, .pm-task-card:focus-within .pm-task-grip { opacity: 1; }
        .pm-task-card h4 { max-width: 100%; margin: 0; color: #20262c; font-size: 13px; line-height: 1.4; }
        .pm-task-card h4 a { color: inherit; }
        .pm-task-card h4 a:hover { color: var(--primary-strong); }
        .pm-board--trello .pm-card-labels { gap: 5px; }
        .pm-board--trello .pm-card-labels span { padding: 4px 7px; border-left: 3px solid var(--label-color, var(--primary)); border-radius: 4px; background: color-mix(in srgb, var(--label-color, var(--primary)) 12%, white); color: #555e67; font-size: 9px; font-weight: 750; }
        .pm-task-due { color: #6c757e; font-size: 10px; }
        .pm-task-due.is-overdue { color: var(--danger); font-weight: 800; }
        .pm-task-card__details .pm-chip { min-height: 21px; padding: 0 7px; border-radius: 5px; font-size: 9px; }
        .pm-chip--priority-low { color: #43725a; background: #eef8f1; border-color: #d7ecdd; }
        .pm-chip--priority-medium { color: #75601c; background: #fff8dc; border-color: #f1e4a7; }
        .pm-chip--priority-high { color: #a25b24; background: #fff1e6; border-color: #f2d6bd; }
        .pm-task-card__footer { min-height: 27px; padding-top: 9px; border-top: 1px solid #eef0f1; }
        .pm-task-assignee { display: inline-flex; min-width: 0; align-items: center; gap: 6px; color: #68717b; cursor: pointer; }
        .pm-task-assignee .pm-avatar { width: 25px; height: 25px; flex: 0 0 25px; }
        .pm-task-assignee select { max-width: 112px; overflow: hidden; padding: 0 14px 0 0; border: 0; background: transparent; color: #68717b; cursor: pointer; font: inherit; font-size: 10px; text-overflow: ellipsis; white-space: nowrap; }
        .pm-task-assignee select:focus { outline: 1px solid rgba(184,134,11,.55); border-radius: 4px; }
        .pm-avatar--empty { background: #f1f2f4; color: #9aa1a8; }
        .pm-task-stats { display: inline-flex; align-items: center; gap: 7px; color: #89919a; font-size: 10px; }
        .pm-task-card__keyboard { display: none; }
        .pm-drop-placeholder { display: grid; min-height: 86px; align-content: center; justify-items: center; gap: 3px; padding: 12px; border: 1px dashed #c4c9cf; border-radius: 8px; color: #7e8790; text-align: center; }
        .pm-drop-placeholder span { color: #a4abb2; font-size: 20px; line-height: 1; }
        .pm-drop-placeholder strong { font-size: 11px; }
        .pm-drop-placeholder small { color: #9aa1a8; font-size: 9px; }
        .pm-board--trello .pm-column.is-drag-over { background: #dfe2e6; }
        .pm-board--trello .pm-column.is-drag-over .pm-column-list { background: rgba(184,134,11,.07); }
        .pm-board--trello .pm-column.is-drag-over .pm-drop-placeholder { border-color: rgba(184,134,11,.55); background: rgba(255,251,237,.7); color: var(--primary-strong); }
        .pm-board-view[data-read-only="1"] .pm-task-assignee select { pointer-events: none; }
        .pm-board-view[data-read-only="1"] .pm-task-card { cursor: default; }
        .pm-board-view[data-read-only="1"] .pm-task-card__keyboard { display: none; }
        .pm-board-view[data-read-only="1"] .pm-board-toolbar__hint { display: none; }
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
        .pm-icon-link {
            display: inline-flex;
            min-height: 24px;
            align-items: center;
            justify-content: center;
            padding: 4px 0;
            border: 0;
            background: transparent;
            color: var(--primary-strong);
            cursor: pointer;
            font: inherit;
            font-size: 12px;
            font-weight: 750;
            text-decoration: none;
        }
        .pm-icon-link:hover { color: var(--primary); text-decoration: underline; }
        .pm-chart-bars { display: grid; gap: 9px; }
        .pm-chart-bar { display: grid; grid-template-columns: 100px minmax(0,1fr) 32px; align-items: center; gap: 8px; font-size: 11px; }
        .pm-chart-bar > span:first-child { color: var(--muted); }
        .pm-chart-bar > span:last-child { text-align: right; font-weight: 800; }
        .pm-chart-bar > strong { color: var(--text); font-size: 11px; text-align: right; }
        .pm-chart-bar--danger .pm-progress span { background: var(--danger); }
        .pm-chart-bar--warning .pm-progress span { background: #d48b2d; }
        .pm-chart-bar--success .pm-progress span { background: var(--success); }
        .pm-chart-bar--muted .pm-progress span { background: #aeb5bc; }
        .pm-staff-chart-grid { align-items: stretch; }
        .pm-chart-panel { min-height: 0; }
        .pm-project-chart__bars { grid-template-columns: repeat(2, minmax(0, 1fr)); column-gap: 24px; }
        .pm-task-overview { overflow: hidden; }
        .pm-task-overview-table { overflow-x: auto; }
        .pm-task-overview-row { display: grid; min-width: 780px; grid-template-columns: minmax(220px, 1.5fr) .7fr 1fr .7fr .85fr .85fr; align-items: center; gap: 14px; padding: 11px 0; border-top: 1px solid var(--line-soft); color: var(--muted); font-size: 11px; }
        .pm-task-overview-row--head { padding-top: 0; border-top: 0; color: var(--muted); font-size: 9px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        .pm-task-overview-row__task { display: grid; min-width: 0; gap: 3px; }
        .pm-task-overview-row__task strong { overflow: hidden; color: var(--text); font-size: 12px; text-overflow: ellipsis; white-space: nowrap; }
        .pm-task-overview-row__task strong a { color: inherit; }
        .pm-task-overview-row__task strong a:hover { color: var(--primary-strong); }
        .pm-task-overview-row__task span { color: var(--muted); font-size: 10px; }
        .pm-task-overview-priority { font-weight: 750; }
        .pm-task-overview-priority--urgent { color: var(--danger); }
        .pm-task-overview-priority--high { color: #b56e22; }
        .pm-task-overview-row .is-overdue { color: var(--danger); font-weight: 750; }
        /* Keep empty delivery views compact and useful instead of stretching
           every neighboring panel to the height of a blank state. */
        .pm-dashboard,
        .pm-project-list,
        .pm-board-view,
        .pm-backlog-view,
        .pm-sprints-view,
        .pm-task-view,
        .pm-project-view { display: grid; gap: 18px; align-content: start; }
        .pm-dashboard { gap: 18px; }
        .pm-dashboard .pm-grid,
        .pm-dashboard .pm-grid-wide { align-items: start; }
        .pm-dashboard .pm-panel { min-height: 0; }
        .pm-dashboard .pm-empty {
            display: grid;
            min-height: 74px;
            align-content: center;
            justify-items: start;
            gap: 4px;
            padding: 14px 0;
            color: var(--muted);
            text-align: left;
        }
        .pm-dashboard .pm-empty strong { color: var(--text); font-size: 13px; }
        .pm-dashboard .pm-empty span { font-size: 11px; }
        .pm-dashboard .pm-empty--actionable { justify-items: start; }
        .pm-dashboard .pm-empty--actionable .pm-icon-link { margin-top: 5px; }
        .pm-dashboard .pm-empty--compact { min-height: 58px; }
        .pm-dashboard .pm-kpis { gap: 12px; border: 0; }
        .pm-dashboard .pm-kpi {
            min-height: 82px;
            padding: 14px 18px;
            border: 1px solid var(--line-soft);
            border-top: 2px solid #d9dde2;
            border-radius: 9px;
            background: #fff;
            box-shadow: none;
        }
        .pm-dashboard .pm-kpi:first-child { padding-left: 18px; border-top-color: var(--primary); }
        .pm-dashboard .pm-kpi:nth-child(3) { border-top-color: #d47b45; }
        .pm-dashboard .pm-kpi:nth-child(4) { border-top-color: #5b9b79; }
        .pm-dashboard .pm-kpi strong { margin-top: 5px; font-size: 25px; }
        .pm-dashboard .pm-filter-panel { border-radius: 9px; }
        .pm-dashboard .pm-filter-panel > summary { min-height: 64px; padding: 14px 18px; }
        .pm-dashboard .pm-filter-panel[open] > summary { border-bottom: 1px solid var(--line-soft); }
        .pm-dashboard .pm-grid { gap: 14px; }
        .pm-dashboard .pm-grid-wide { gap: 14px; }
        .pm-dashboard .pm-panel-head { margin-bottom: 9px; }
        .pm-dashboard .pm-list-item { padding: 10px 0; }
        .pm-dashboard .pm-chart-bars { min-height: 0; }
        .pm-dashboard > .pm-panel:last-child { margin-top: 0; }
        .pm-create-layout { display: grid; grid-template-columns: minmax(0, 1fr) 280px; gap: 16px; align-items: start; }
        .pm-create-main { padding: 22px; }
        .pm-create-main .pm-panel-head { margin-bottom: 20px; }
        .pm-create-main .pm-panel-head h3 { font-size: 21px; letter-spacing: -.03em; }
        .pm-create-main .pm-panel-head p { max-width: 680px; font-size: 12px; }
        .pm-create-main .pm-form-grid { align-items: start; gap: 16px; }
        .pm-create-main .field small { color: var(--muted); font-size: 11px; }
        .pm-create-main .field-full > label,
        .pm-create-main .field > label { font-size: 10px; letter-spacing: .1em; text-transform: uppercase; }
        .pm-create-main .field input,
        .pm-create-main .field select,
        .pm-create-main .field textarea,
        .pm-create-main .field-full input,
        .pm-create-main .field-full select,
        .pm-create-main .field-full textarea { min-height: 44px; border-radius: 8px; background: #fbfcfc; }
        .pm-create-main .field textarea,
        .pm-create-main .field-full textarea { padding-top: 11px; }
        .pm-create-main .pm-chip { min-height: 32px; border-radius: 7px; background: #f7f8f9; }
        .pm-create-form { display: grid; gap: 20px; }
        .pm-create-section { display: grid; gap: 12px; padding-top: 17px; border-top: 1px solid var(--line-soft); }
        .pm-create-section:first-child { padding-top: 0; border-top: 0; }
        .pm-create-section__head { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .pm-create-section__meta { color: var(--muted); font-size: 10px; font-weight: 650; }
        .pm-create-main > form > .pm-form-grid { align-items: start; gap: 12px 14px; }
        .pm-create-main .field,
        .pm-create-main .field-full { gap: 6px; }
        .pm-create-main .field,
        .pm-create-main .field-full { align-self: start; }
        .pm-create-main .field > label,
        .pm-create-main .field-full > label { color: #68717c; font-size: 10px; font-weight: 750; letter-spacing: .08em; line-height: 1.2; text-transform: uppercase; }
        .pm-create-main .field input:not([type="checkbox"]):not([type="radio"]),
        .pm-create-main .field select,
        .pm-create-main .field textarea,
        .pm-create-main .field-full input:not([type="checkbox"]):not([type="radio"]),
        .pm-create-main .field-full select,
        .pm-create-main .field-full textarea { min-height: 42px; padding: 9px 12px; border-color: #e1e5e9; border-radius: 8px; background: #fbfcfc; }
        .pm-create-main .field textarea,
        .pm-create-main .field-full textarea { min-height: 96px; }
        .pm-create-main .field input:not([type="checkbox"]):not([type="radio"]):focus,
        .pm-create-main .field select:focus,
        .pm-create-main .field textarea:focus,
        .pm-create-main .field-full input:not([type="checkbox"]):not([type="radio"]):focus,
        .pm-create-main .field-full select:focus,
        .pm-create-main .field-full textarea:focus { border-color: rgba(184,134,11,.55); box-shadow: 0 0 0 3px rgba(184,134,11,.1); }
        .pm-create-main .field small { margin-top: -1px; }
        .pm-create-main input[type="checkbox"],
        .pm-create-main input[type="radio"] { width: 16px !important; min-width: 16px !important; max-width: 16px; height: 16px !important; min-height: 16px !important; margin: 0; padding: 0; border: 0; border-radius: 4px; box-shadow: none; accent-color: var(--primary); flex: 0 0 16px; }
        .pm-member-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; }
        .pm-member-option { display: flex; min-height: 38px; align-items: center; gap: 8px; padding: 0 11px; border: 1px solid #e1e5e9; border-radius: 8px; background: #fbfcfc; color: var(--muted-strong); cursor: pointer; font-size: 11px; font-weight: 650; transition: border-color .16s ease, background .16s ease, color .16s ease; }
        .pm-member-option:hover { border-color: rgba(184,134,11,.45); background: #fffdf5; }
        .pm-member-option:has(input:checked) { border-color: rgba(184,134,11,.62); background: #fffbed; color: var(--primary-strong); box-shadow: inset 0 0 0 1px rgba(184,134,11,.12); }
        .pm-member-option span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .pm-create-main .pm-form-actions { margin-top: 0; padding-top: 16px; border-top: 1px solid var(--line-soft); }
        .pm-create-aside { display: grid; gap: 12px; position: sticky; top: 18px; }
        .pm-create-summary { padding: 20px; }
        .pm-create-summary h3 { margin: 5px 0 7px; font-size: 20px; letter-spacing: -.03em; }
        .pm-create-summary > p { margin: 0; color: var(--muted); font-size: 12px; line-height: 1.5; }
        .pm-create-summary ul { display: grid; gap: 0; margin: 20px 0 0; padding: 0; list-style: none; border-top: 1px solid var(--line-soft); }
        .pm-create-summary li { display: grid; gap: 3px; padding: 13px 0; border-bottom: 1px solid var(--line-soft); }
        .pm-create-summary li strong { font-size: 12px; }
        .pm-create-summary li span { color: var(--muted); font-size: 10px; }
        .pm-create-tip { display: flex; align-items: flex-start; gap: 9px; padding: 12px; border: 1px solid rgba(184,134,11,.18); border-radius: 9px; background: #fffbed; color: var(--muted); font-size: 11px; line-height: 1.45; }
        .pm-create-tip__mark { display: inline-grid; width: 18px; height: 18px; flex: 0 0 18px; place-items: center; border-radius: 50%; background: var(--primary); color: #fff; font-size: 11px; font-weight: 800; }
        /* Use a drawn chevron instead of the corrupted text glyph that was
           appearing as a loose "v" beside these controls. */
        .pm-subnav-more summary::after,
        body.is-admin .pm-filter-panel__toggle::after {
            content: "" !important;
            display: inline-block !important;
            width: 6px !important;
            height: 6px !important;
            margin: -3px 0 0 8px !important;
            border-right: 1.5px solid currentColor !important;
            border-bottom: 1.5px solid currentColor !important;
            transform: rotate(45deg) !important;
            transition: transform .18s ease !important;
        }
        .pm-subnav-more summary { gap: 0; }
        .pm-subnav-more[open] summary::after,
        body.is-admin .pm-filter-panel[open] .pm-filter-panel__toggle::after { transform: rotate(225deg) !important; margin-top: 3px !important; }
        body.is-admin .pm-filter-panel__toggle { display: inline-flex; min-height: 28px; align-items: center; padding: 0 9px; border: 1px solid rgba(184,134,11,.2); border-radius: 6px; background: #fffbed; }
        @media (max-width: 980px) { .pm-create-layout { grid-template-columns: 1fr; } .pm-create-aside { position: static; grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 640px) { .pm-create-aside { grid-template-columns: 1fr; } .pm-create-main { padding: 16px; } .pm-member-grid { grid-template-columns: 1fr; } }
        @media (max-width: 980px) { .pm-kpis { grid-template-columns: repeat(2, minmax(0,1fr)); } .pm-grid, .pm-detail-grid { grid-template-columns: 1fr; } .pm-project-chart__bars { grid-template-columns: 1fr; } }
        @media (max-width: 720px) {
            .pm-board-toolbar__topline { align-items: flex-start; flex-direction: column; gap: 7px; }
            .pm-board-toolbar__controls .pm-inline-form { align-items: stretch; }
            .pm-board-toolbar__controls .field { flex: 1 1 100%; }
            .pm-board-toolbar__controls .pm-chip--check,
            .pm-board-toolbar__controls .ghost-button { flex: 0 0 auto; }
            .pm-board--trello { margin-inline: -2px; padding: 10px; }
            .pm-board--trello .pm-column { width: min(86vw, 306px); min-width: min(86vw, 306px); }
        }
        @media (max-width: 1100px) { .pm-task-hero { grid-template-columns: minmax(0, 1fr) minmax(260px, .65fr); } .pm-task-hero__actions { grid-column: 1 / -1; justify-content: flex-start; } }
        @media (max-width: 980px) { .pm-staff-task-summary { grid-template-columns: 1fr; } }
        @media (max-width: 640px) { .pm-kpis, .pm-grid-wide, .pm-form-grid, .pm-backlog-overview { grid-template-columns: 1fr; } .pm-hero { align-items: flex-start; flex-direction: column; padding: 18px; } .pm-backlog-panel__head { flex-direction: column; } .pm-backlog-hero .pm-actions { width: 100%; } .pm-backlog-hero .pm-actions > * { flex: 1 1 0; justify-content: center; } .pm-daily-motivation { grid-template-columns: 34px minmax(0, 1fr); gap: 12px; min-height: 0; padding: 18px; } .pm-daily-motivation__mark { width: 34px; height: 34px; border-radius: 10px; } .pm-daily-motivation__mark svg { width: 19px; height: 19px; } .pm-daily-motivation blockquote { font-size: 16px; } .pm-daily-motivation__orbit { right: -54px; opacity: .7; } .pm-task-countdown { width: 100%; flex-basis: 100%; } .pm-task-hero__actions { justify-content: flex-start; margin-left: 0; } .pm-subnav { overflow-x: auto; flex-wrap: nowrap; } .pm-subnav a { flex: 0 0 auto; } .pm-panel { padding: 14px; } .pm-project-view .pm-stat-row { grid-template-columns: 1fr; } .pm-project-view .pm-tabs { overflow-x: auto; flex-wrap: nowrap; padding-bottom: 3px; } .pm-project-view .pm-tabs a { flex: 0 0 auto; } .pm-project-view .pm-hero .pm-actions { width: 100%; } .pm-project-view .pm-hero .pm-actions > * { flex: 1 1 0; } }
        @media (prefers-reduced-motion: reduce) { .pm-daily-motivation, .pm-daily-motivation::before, .pm-daily-motivation__mark, .pm-daily-motivation__content, .pm-daily-motivation__orbit { animation: none !important; } }
    </style>
@endpush

@section('content')
    <div class="pm-shell">
        @if (isset($dailyMotivation))
            @include('admin.project-management.partials.daily-motivation')
        @endif

        <nav class="pm-subnav" aria-label="Project management navigation">
        @if (\App\Support\AdminAccess::isFullAdmin())
        @php
            $moreNavActive = request()->routeIs('admin.project-management.search', 'admin.project-management.calendar', 'admin.project-management.team', 'admin.project-management.reports', 'admin.project-management.archived', 'admin.project-management.settings');
        @endphp
            <a class="{{ request()->routeIs('admin.project-management.dashboard') ? 'active' : '' }}" href="{{ route('admin.project-management.dashboard') }}">Overview</a>
            <a class="{{ request()->routeIs('admin.project-management.projects*') && ! request()->routeIs('admin.project-management.archived') ? 'active' : '' }}" href="{{ route('admin.project-management.projects') }}">Projects</a>
            @if (isset($project) && $project)
                <a class="{{ request()->routeIs('admin.project-management.board') ? 'active' : '' }}" href="{{ route('admin.project-management.board', $project) }}">Board</a>
                <a class="{{ request()->routeIs('admin.project-management.backlog') ? 'active' : '' }}" href="{{ route('admin.project-management.backlog', $project) }}">Backlog</a>
                <a class="{{ request()->routeIs('admin.project-management.sprints') ? 'active' : '' }}" href="{{ route('admin.project-management.sprints', $project) }}">Sprints</a>
            @endif
            <a class="{{ request()->routeIs('admin.project-management.notifications') ? 'active' : '' }}" href="{{ route('admin.project-management.notifications') }}">Notifications @if ($pmUnreadNotifications)<span class="pm-notification-count">{{ $pmUnreadNotifications > 99 ? '99+' : $pmUnreadNotifications }}</span>@endif</a>
            <details class="pm-subnav-more" @if ($moreNavActive) open @endif>
                <summary class="{{ $moreNavActive ? 'active' : '' }}">More</summary>
                <div class="pm-subnav-more__items">
                    <a class="{{ request()->routeIs('admin.project-management.search') ? 'active' : '' }}" href="{{ route('admin.project-management.search') }}">Search</a>
                    <a class="{{ request()->routeIs('admin.project-management.calendar') ? 'active' : '' }}" href="{{ route('admin.project-management.calendar') }}">Calendar</a>
                    <a class="{{ request()->routeIs('admin.project-management.team') ? 'active' : '' }}" href="{{ route('admin.project-management.team') }}">Team</a>
                    <a class="{{ request()->routeIs('admin.project-management.reports') ? 'active' : '' }}" href="{{ route('admin.project-management.reports') }}">Reports</a>
                    <a class="{{ request()->routeIs('admin.project-management.archived') ? 'active' : '' }}" href="{{ route('admin.project-management.archived') }}">Archived</a>
                    @if (\App\Support\AdminAccess::isFullAdmin() && isset($project) && $project)
                        <a class="{{ request()->routeIs('admin.project-management.settings') ? 'active' : '' }}" href="{{ route('admin.project-management.settings', $project) }}">Settings</a>
                    @endif
                </div>
            </details>
        @else
            <a class="active" href="{{ route('admin.project-management.dashboard') }}">My tasks</a>
            <a class="{{ request()->routeIs('admin.project-management.notifications') ? 'active' : '' }}" href="{{ route('admin.project-management.notifications') }}">Notifications @if ($pmUnreadNotifications)<span class="pm-notification-count">{{ $pmUnreadNotifications > 99 ? '99+' : $pmUnreadNotifications }}</span>@endif</a>
        @endif
        </nav>

        @yield('project-content')
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const closeMoreMenus = () => document.querySelectorAll('.pm-subnav-more[open]').forEach((menu) => menu.removeAttribute('open'));

            document.addEventListener('click', (event) => {
                const menu = event.target.closest('.pm-subnav-more');

                if (!menu || event.target.closest('.pm-subnav-more__items a') || !event.target.closest('.pm-subnav')) {
                    closeMoreMenus();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closeMoreMenus();
            });

            const taskOrNotificationForm = (form) => {
                const action = form.getAttribute('action') || '';

                return action.includes('/tasks') || action.includes('/notifications');
            };

            const floatingNotificationStack = () => {
                let stack = document.querySelector('[data-admin-alert-stack]');

                if (!stack) {
                    stack = document.createElement('div');
                    stack.className = 'admin-alert-stack';
                    stack.dataset.adminAlertStack = '';
                    stack.setAttribute('aria-live', 'polite');
                    stack.setAttribute('aria-atomic', 'true');
                    document.body.append(stack);
                }

                return stack;
            };

            const showFloatingNotification = (message, isError = false) => {
                const stack = floatingNotificationStack();
                const alert = document.createElement('div');
                const content = document.createElement('span');
                const close = document.createElement('button');
                let dismissTimer;

                alert.className = `alert ${isError ? 'alert-danger' : 'alert-success'}`;
                alert.setAttribute('role', isError ? 'alert' : 'status');
                content.textContent = message;
                close.className = 'admin-alert-close';
                close.type = 'button';
                close.dataset.adminAlertClose = '';
                close.setAttribute('aria-label', 'Dismiss notification');
                close.textContent = '×';
                alert.append(content, close);
                stack.append(alert);

                const dismiss = () => {
                    window.clearTimeout(dismissTimer);
                    if (!alert.isConnected) return;
                    alert.classList.add('is-dismissing');
                    window.setTimeout(() => alert.remove(), 220);
                };

                const scheduleDismissal = () => {
                    window.clearTimeout(dismissTimer);
                    dismissTimer = window.setTimeout(dismiss, 3500);
                };

                close.addEventListener('click', dismiss);
                alert.addEventListener('mouseenter', () => window.clearTimeout(dismissTimer));
                alert.addEventListener('mouseleave', scheduleDismissal);
                scheduleDismissal();
            };

            const showFeedback = (message, isError = false) => {
                showFloatingNotification(message, isError);
            };

            const responsePayload = async (response) => {
                const contentType = response.headers.get('content-type') || '';

                if (!contentType.includes('application/json')) {
                    throw new Error('The server returned an unexpected response. Please try again.');
                }

                const payload = await response.json();

                if (!response.ok) {
                    const validationMessage = payload.errors
                        ? Object.values(payload.errors).flat()[0]
                        : null;
                    throw new Error(validationMessage || payload.message || 'This action could not be completed.');
                }

                return payload;
            };

            const updateTaskState = (form) => {
                const action = form.getAttribute('action') || '';
                const isCompleting = action.endsWith('/complete');
                const nextAction = action.replace(/\/(complete|reopen)$/, isCompleting ? '/reopen' : '/complete');
                const button = form.querySelector('button[type="submit"]');
                const actions = form.closest('.pm-task-hero__actions');

                form.action = nextAction;
                form.querySelector('[name="_method"]')?.setAttribute('value', 'PATCH');

                if (button) {
                    button.textContent = isCompleting ? 'Mark not completed' : 'Mark as done';
                    button.classList.toggle('button', !isCompleting);
                    button.classList.toggle('ghost-button', isCompleting);
                }

                if (actions) {
                    const existingBadge = actions.querySelector('.pm-chip--success');

                    if (isCompleting && !existingBadge) {
                        const badge = document.createElement('span');
                        badge.className = 'pm-chip pm-chip--success';
                        badge.textContent = 'Done just now';
                        actions.insertBefore(badge, form);
                    } else if (!isCompleting) {
                        existingBadge?.remove();
                    }
                }
            };

            const updateNotification = (form) => {
                const item = form.closest('.pm-list-item');
                item?.classList.remove('is-unread');
                form.remove();
                item?.querySelector('.pm-notification-meta')?.replaceChildren(
                    ...[document.createTextNode((item.querySelector('.pm-notification-meta')?.textContent || '').replace('Unread', 'Read'))]
                );

                document.querySelectorAll('.pm-notification-count').forEach((badge) => {
                    const count = Number.parseInt(badge.textContent, 10) || 0;
                    const next = Math.max(0, count - 1);

                    if (next === 0) badge.remove();
                    else badge.textContent = next > 99 ? '99+' : String(next);
                });
            };

            const normalizedAction = (action) => action.split('?')[0].replace(/\/+$/, '');

            const shouldReset = (action) => /\/projects\/[^/]+\/tasks$|\/comments$|\/time$|\/attachments$|\/checklists$|\/checklist-items$|\/sprint$/.test(normalizedAction(action));

            const resetAfterTaskCreation = (form, action) => {
                const normalized = normalizedAction(action);

                if (!/\/projects\/[^/]+\/tasks$/.test(normalized)) return;

                form.reset();
                form.querySelector('.pm-advanced-fields')?.removeAttribute('open');
            };

            const escapeHtml = (value) => String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');

            const panelWithHeading = (root, heading) => [...root.querySelectorAll('.pm-panel')]
                .find((panel) => panel.querySelector('h3')?.textContent.trim() === heading);

            const updateTaskContent = ({form, payload, action}) => {
                const root = form.closest('.pm-task-view');
                const data = payload.data || {};

                if (!root) return;

                if (form.matches('[data-ajax-task-edit]')) {
                    const title = data.title || form.querySelector('[name="title"]')?.value || '';
                    const type = data.type || form.querySelector('[name="type"]')?.value || 'task';
                    const column = data.column?.name || form.querySelector('[name="board_column_id"] option:checked')?.textContent || '';
                    const heroTitle = root.querySelector('.pm-task-hero h2');
                    const heroEyebrow = root.querySelector('.pm-task-hero .eyebrow');
                    const heroMeta = root.querySelector('.pm-task-hero p');

                    if (heroTitle) heroTitle.textContent = title;
                    if (heroEyebrow && data.task_key) heroEyebrow.textContent = `${data.task_key} · ${String(type).replaceAll('_', ' ')}`;
                    if (heroMeta && column) heroMeta.textContent = `${heroMeta.textContent.split(' · ')[0]} · ${column}`;
                }

                if (action.endsWith('/comments')) {
                    const list = panelWithHeading(root, 'Comments')?.querySelector('.pm-list');
                    if (list) list.insertAdjacentHTML('beforeend', `<div class="pm-list-item"><div><strong>${escapeHtml(data.user?.name || 'Team member')}</strong><span>Just now</span><p style="margin:7px 0 0; white-space:pre-wrap; line-height:1.5">${escapeHtml(data.body)}</p></div></div>`);
                }

                if (action.endsWith('/time')) {
                    const panel = panelWithHeading(root, 'Time tracking');
                    const list = panel?.querySelector('.pm-list:last-child');
                    if (list) list.insertAdjacentHTML('afterbegin', `<div class="pm-list-item"><div><strong>${(Number(data.minutes || 0) / 60).toFixed(1)}h · ${escapeHtml(data.user?.name || 'Team member')}</strong><span>${escapeHtml(data.description || 'No description')}</span></div></div>`);
                    const hours = panel?.querySelector('p');
                    const current = Number.parseFloat(hours?.textContent || '0') || 0;
                    if (hours) hours.textContent = `${(current + Number(data.minutes || 0) / 60).toFixed(1)} hours logged.`;
                }

                if (action.endsWith('/attachments')) {
                    const list = panelWithHeading(root, 'Files')?.querySelector('.pm-list');
                    if (list) list.insertAdjacentHTML('afterbegin', `<div class="pm-list-item"><div><strong>${escapeHtml(data.original_name)}</strong><span>Team member · ${((Number(data.size || 0) / 1024)).toFixed(1)} KB</span></div><a class="pm-icon-link" href="${escapeHtml(data.download_url)}">Download</a></div>`);
                }

                if (action.endsWith('/timer/start') || action.endsWith('/timer/stop')) {
                    const isRunning = action.endsWith('/timer/start');
                    form.action = form.action.replace(/\/(start|stop)$/, isRunning ? '/stop' : '/start');
                    const button = form.querySelector('button[type="submit"]');
                    if (button) {
                        button.textContent = isRunning ? 'Stop active timer' : 'Start timer';
                        button.classList.toggle('button', isRunning);
                        button.classList.toggle('ghost-button', !isRunning);
                    }
                }

                if (action.endsWith('/tasks') && form.closest('.pm-subtask-create')) {
                    const list = panelWithHeading(root, 'Subtasks')?.querySelector('.pm-list');
                    if (list && data.task_key) list.insertAdjacentHTML('beforeend', `<div class="pm-list-item"><div><strong>${escapeHtml(data.task_key)} · ${escapeHtml(data.title)}</strong><span>${escapeHtml(data.assignee?.name || 'Unassigned')}</span></div><span class="pm-chip">Open</span></div>`);
                }
            };

            const submitAjax = async (form) => {
                if (form.dataset.ajaxSubmitting === '1') return;
                if (form.dataset.ajaxConfirm && !window.confirm(form.dataset.ajaxConfirm)) return;

                form.dataset.ajaxSubmitting = '1';
                form.classList.add('is-saving');
                const button = form.querySelector('button[type="submit"]');
                const originalLabel = button?.textContent;
                if (button) {
                    button.disabled = true;
                    button.textContent = 'Saving…';
                }

                try {
                    const method = (form.querySelector('[name="_method"]')?.value || form.method || 'POST').toUpperCase();
                    const response = await fetch(form.action, {
                        method: method === 'GET' ? 'POST' : method,
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || form.querySelector('[name="_token"]')?.value || '',
                        },
                        body: new FormData(form),
                    });
                    const payload = await responsePayload(response);
                    const action = form.getAttribute('action') || '';

                    if (/\/tasks\/[^/]+\/(complete|reopen)$/.test(action)) updateTaskState(form);
                    if (/\/notifications\/[^/]+\/read$/.test(action)) updateNotification(form);
                    resetAfterTaskCreation(form, action);
                    if (shouldReset(action) && !/\/projects\/[^/]+\/tasks$/.test(normalizedAction(action))) form.reset();

                    showFeedback(payload.message || 'Saved.');
                    if (form.dataset.ajaxDeleteTask === '1') {
                        window.location.assign(form.dataset.ajaxSuccessUrl || document.referrer || '/');
                        return;
                    }
                    updateTaskContent({form, payload, action});
                    document.dispatchEvent(new CustomEvent('pm:ajax-success', {detail: {form, payload, action}}));
                } catch (error) {
                    showFeedback(error.message || 'This action could not be completed.', true);
                } finally {
                    form.dataset.ajaxSubmitting = '0';
                    form.classList.remove('is-saving');
                    if (button) {
                        button.disabled = false;
                        if (button.textContent === 'Saving…') button.textContent = originalLabel;
                    }
                }
            };

            document.addEventListener('submit', (event) => {
                const form = event.target.closest('form');

                if (!form || event.defaultPrevented || !taskOrNotificationForm(form)) return;

                event.preventDefault();
                submitAjax(form);
            });

            document.addEventListener('change', (event) => {
                const checkbox = event.target;
                const form = checkbox.closest('form');

                if (!(checkbox instanceof HTMLInputElement) || checkbox.type !== 'checkbox' || !form || !taskOrNotificationForm(form) || !form.action.includes('/checklist-items/')) return;

                event.preventDefault();
                event.stopImmediatePropagation();
                submitAjax(form);
            }, true);
        })();
    </script>
@endpush
