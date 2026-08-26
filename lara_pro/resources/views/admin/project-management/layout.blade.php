@extends('admin.layouts.app')

@section('title', ($title ?? 'Project Management').' | Admin')

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
        .pm-dashboard .pm-panel-head p { display: none; }
        .pm-dashboard .pm-panel-head { margin-bottom: 10px; }
        .pm-dashboard .pm-kpi small { display: none; }
        .pm-board-view .pm-hero h2 { font-size: 0; }
        .pm-board-view .pm-hero h2::after { content: 'Move work forward.'; display: block; font-size: clamp(24px, 3vw, 36px); }
        .pm-board-view .pm-hero p { display: none; }
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
        @media (max-width: 980px) { .pm-kpis { grid-template-columns: repeat(2, minmax(0,1fr)); } .pm-grid, .pm-detail-grid { grid-template-columns: 1fr; } }
        @media (max-width: 640px) { .pm-kpis, .pm-grid-wide, .pm-form-grid { grid-template-columns: 1fr; } .pm-hero { align-items: flex-start; flex-direction: column; padding: 18px; } .pm-subnav { overflow-x: auto; flex-wrap: nowrap; } .pm-subnav a { flex: 0 0 auto; } .pm-panel { padding: 14px; } .pm-project-view .pm-stat-row { grid-template-columns: 1fr; } .pm-project-view .pm-tabs { overflow-x: auto; flex-wrap: nowrap; padding-bottom: 3px; } .pm-project-view .pm-tabs a { flex: 0 0 auto; } .pm-project-view .pm-hero .pm-actions { width: 100%; } .pm-project-view .pm-hero .pm-actions > * { flex: 1 1 0; } }
    </style>
@endpush

@section('content')
    <div class="pm-shell">
        <nav class="pm-subnav" aria-label="Project management navigation">
        @if (\App\Support\AdminAccess::isFullAdmin())
        @php
            $moreNavActive = request()->routeIs('admin.project-management.search', 'admin.project-management.calendar', 'admin.project-management.team', 'admin.project-management.reports', 'admin.project-management.archived', 'admin.project-management.notifications', 'admin.project-management.settings');
        @endphp
            <a class="{{ request()->routeIs('admin.project-management.dashboard') && ! request('assignee_id') ? 'active' : '' }}" href="{{ route('admin.project-management.dashboard') }}">Overview</a>
            <a class="{{ request()->routeIs('admin.project-management.dashboard') && request('assignee_id') ? 'active' : '' }}" href="{{ route('admin.project-management.dashboard', ['assignee_id' => \App\Support\ProjectManagementAccess::user()?->id]) }}">My tasks</a>
            <a class="{{ request()->routeIs('admin.project-management.projects*') && ! request()->routeIs('admin.project-management.archived') ? 'active' : '' }}" href="{{ route('admin.project-management.projects') }}">Projects</a>
            @if (isset($project) && $project)
                <a class="{{ request()->routeIs('admin.project-management.board') ? 'active' : '' }}" href="{{ route('admin.project-management.board', $project) }}">Board</a>
                <a class="{{ request()->routeIs('admin.project-management.backlog') ? 'active' : '' }}" href="{{ route('admin.project-management.backlog', $project) }}">Backlog</a>
                <a class="{{ request()->routeIs('admin.project-management.sprints') ? 'active' : '' }}" href="{{ route('admin.project-management.sprints', $project) }}">Sprints</a>
            @endif
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
                    <a class="{{ request()->routeIs('admin.project-management.notifications') ? 'active' : '' }}" href="{{ route('admin.project-management.notifications') }}">Notifications</a>
                </div>
            </details>
        @else
            <a class="active" href="{{ route('admin.project-management.dashboard') }}">My tasks</a>
        @endif
        </nav>

        @yield('project-content')
    </div>
@endsection
