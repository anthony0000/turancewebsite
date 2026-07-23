@extends('admin.layouts.app')

@section('title', ($builderProposal ? 'Edit Proposal' : 'Proposal Studio').' | Admin')

@section('content')
    @php
        use Illuminate\Support\Js;
        use Illuminate\Support\Str;

        $isEditing = filled($builderProposal);
        $builderId = $builderProposal?->id ? 'proposal-'.$builderProposal->id : 'proposal-new';
        $templateOptions = $templates->map(fn ($template) => [
            'id' => $template->id,
            'slug' => $template->slug,
            'name' => $template->name,
            'description' => $template->description,
            'category' => $template->category,
            'theme_key' => $template->theme_key,
            'palette' => $template->palette,
            'settings' => $template->settings,
        ])->values();
    @endphp

    <style>
        .proposal-studio-hero {
            background: transparent;
        }

        .proposal-template-strip,
        .proposal-builder-shell,
        .proposal-dashboard-grid {
            display: grid;
            gap: 18px;
        }

        .proposal-template-strip {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .proposal-template-card {
            position: relative;
            display: grid;
            gap: 14px;
            min-height: 236px;
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.88);
            cursor: pointer;
            transition: border-color 0.2s ease, transform 0.2s ease, background 0.2s ease;
        }

        .proposal-template-card:hover,
        .proposal-template-card.is-active {
            border-color: rgba(20, 61, 50, 0.42);
            background: #ffffff;
            transform: translateY(-2px);
        }

        .proposal-template-card input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .proposal-template-card strong {
            display: block;
            color: var(--text);
            font-size: 15px;
            line-height: 1.18;
        }

        .proposal-template-card span {
            display: block;
            margin-top: 5px;
            color: var(--muted);
            font-size: 11px;
            line-height: 1.45;
        }

        .template-mini-preview {
            display: grid;
            grid-template-columns: 0.85fr 1fr;
            gap: 8px;
            min-height: 126px;
        }

        .template-mini-cover,
        .template-mini-pages {
            border: 1px solid rgba(20, 20, 20, 0.1);
            overflow: hidden;
        }

        .template-mini-cover {
            position: relative;
            padding: 12px;
            background: #143d32;
            color: #ffffff;
        }

        .template-mini-cover::after {
            content: "";
            position: absolute;
            right: 12px;
            top: 20px;
            width: 28%;
            height: 62%;
            background: linear-gradient(160deg, #111, #777);
        }

        .template-mini-cover b {
            position: absolute;
            left: 12px;
            bottom: 14px;
            max-width: 70%;
            font-size: 13px;
            line-height: 0.95;
            text-transform: uppercase;
        }

        .template-mini-pages {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5px;
            padding: 7px;
            background: #f6f7f8;
        }

        .template-mini-page {
            min-height: 48px;
            padding: 6px;
            background: #ffffff;
        }

        .template-mini-line {
            display: block;
            height: 3px;
            margin-bottom: 4px;
            background: rgba(20, 20, 20, 0.16);
        }

        .template-mini-line.short {
            width: 58%;
        }

        .template-mini-accent {
            display: block;
            width: 40%;
            height: 7px;
            margin-bottom: 6px;
            background: #8ccf5f;
        }

        .template-mini-preview[data-theme="gold"] .template-mini-cover,
        .template-mini-preview[data-theme="white"] .template-mini-cover,
        .template-mini-preview[data-theme="agency"] .template-mini-cover {
            background: #e8b51f;
            color: #111111;
        }

        .template-mini-preview[data-theme="gold"] .template-mini-accent {
            background: #e8b51f;
        }

        .template-mini-preview[data-theme="dark"] .template-mini-cover {
            background: #15171c;
        }

        .template-mini-preview[data-theme="agency"] .template-mini-accent {
            background: #4ab3c7;
        }

        .proposal-dashboard-grid {
            grid-template-columns: minmax(0, 0.78fr) minmax(0, 1.22fr);
            align-items: start;
        }

        .proposal-builder-shell {
            grid-template-columns: 290px minmax(0, 1fr) 340px;
            align-items: start;
        }

        .proposal-builder-panel {
            min-width: 0;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.92);
        }

        .proposal-builder-panel .panel-head {
            margin: 0;
            padding: 18px;
            border-bottom: 1px solid var(--line);
        }

        .proposal-builder-panel-body {
            padding: 18px;
        }

        .proposal-section-list {
            display: grid;
            gap: 8px;
        }

        .proposal-section-item {
            display: grid;
            grid-template-columns: 28px minmax(0, 1fr) auto;
            gap: 8px;
            align-items: center;
            padding: 9px;
            border: 1px solid rgba(184, 134, 11, 0.16);
            border-radius: 8px;
            background: rgba(184, 134, 11, 0.04);
            cursor: grab;
        }

        .proposal-section-item.is-active {
            border-color: rgba(20, 61, 50, 0.38);
            background: rgba(20, 61, 50, 0.07);
        }

        .proposal-section-item.is-hidden {
            opacity: 0.55;
        }

        .proposal-section-index {
            display: grid;
            place-items: center;
            width: 28px;
            height: 28px;
            background: #143d32;
            color: #ffffff;
            font-size: 10px;
            font-weight: 800;
        }

        .proposal-section-item strong {
            display: block;
            overflow: hidden;
            color: var(--text);
            font-size: 13px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .proposal-section-item span {
            display: block;
            color: var(--muted);
            font-size: 10px;
            text-transform: uppercase;
        }

        .proposal-icon-row {
            display: flex;
            gap: 4px;
        }

        .proposal-icon-button {
            display: inline-grid;
            place-items: center;
            width: 28px;
            height: 28px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #ffffff;
            color: var(--text);
            cursor: pointer;
            font-size: 11px;
            font-weight: 800;
        }

        .proposal-form-grid,
        .proposal-micro-grid {
            display: grid;
            gap: 12px;
        }

        .proposal-form-grid {
            grid-template-columns: 1fr 1fr;
        }

        .proposal-field {
            display: grid;
            gap: 7px;
        }

        .proposal-field.full {
            grid-column: 1 / -1;
        }

        .proposal-field label {
            color: var(--muted);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.09em;
            text-transform: uppercase;
        }

        .proposal-field input,
        .proposal-field select,
        .proposal-field textarea {
            width: 100%;
            min-height: 40px;
            padding: 9px 11px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.92);
            color: var(--text);
        }

        .proposal-field textarea {
            min-height: 92px;
            resize: vertical;
        }

        .proposal-color-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .proposal-color-row input[type="color"] {
            height: 42px;
            padding: 4px;
        }

        .proposal-builder-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.88);
        }

        .proposal-builder-toolbar-group {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .proposal-preview-stage {
            min-height: 760px;
            padding: 24px;
            overflow: auto;
            background:
                linear-gradient(45deg, rgba(20, 20, 20, 0.035) 25%, transparent 25%),
                linear-gradient(-45deg, rgba(20, 20, 20, 0.035) 25%, transparent 25%),
                #e9ecef;
            background-size: 26px 26px;
        }

        .proposal-live-document {
            width: 820px;
            margin: 0 auto;
            transform-origin: top center;
            transition: transform 0.2s ease;
        }

        .proposal-live-document.zoom-90 {
            transform: scale(0.9);
        }

        .proposal-live-document.zoom-75 {
            transform: scale(0.75);
        }

        .live-page {
            position: relative;
            min-height: 1060px;
            margin-bottom: 26px;
            padding: 60px 62px 98px;
            background: #ffffff;
            border: 1px solid rgba(20, 20, 20, 0.08);
            box-shadow: 0 18px 44px rgba(16, 24, 40, 0.16);
            color: #171717;
            font-family: var(--proposal-font, Aptos), "Segoe UI", Arial, sans-serif;
        }

        .live-page[contenteditable] {
            outline: none;
        }

        .live-cover {
            display: grid;
            grid-template-columns: 1.1fr 0.72fr;
            padding: 0;
            background: var(--proposal-primary, #143d32);
            color: #ffffff;
        }

        .live-cover-main {
            position: relative;
            min-height: 1060px;
            padding: 70px 62px;
        }

        .live-cover-year {
            display: block;
            margin-top: 58px;
            color: var(--proposal-accent, #8ccf5f);
            font-size: 30px;
            font-weight: 900;
        }

        .live-cover h1 {
            position: absolute;
            left: 62px;
            right: 62px;
            bottom: 238px;
            margin: 0;
            max-width: 500px;
            font-size: 48px;
            line-height: 1.04;
            text-transform: uppercase;
            word-break: normal;
        }

        .live-cover h1 span {
            display: block;
            color: var(--proposal-accent, #8ccf5f);
            font-weight: 500;
        }

        .live-cover-side {
            padding: 74px 52px 74px 0;
        }

        .live-cover-image {
            height: 500px;
            background:
                linear-gradient(140deg, rgba(255,255,255,0.16), transparent 45%),
                repeating-linear-gradient(90deg, #111 0 18px, #2d2d2d 18px 21px, #050505 21px 34px);
        }

        .live-cover-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .live-meta {
            position: absolute;
            left: 62px;
            right: 54px;
            bottom: 116px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            width: auto;
            font-size: 10px;
            line-height: 1.55;
        }

        .live-meta-item {
            min-width: 0;
        }

        .live-meta-item span {
            display: block;
            margin-bottom: 4px;
            color: rgba(255, 255, 255, 0.66);
            font-size: 9px;
            font-weight: 900;
            letter-spacing: 0.09em;
            text-transform: uppercase;
        }

        .live-meta-item strong,
        .live-meta-item small {
            display: block;
            overflow-wrap: break-word;
        }

        .live-meta-item small {
            margin-top: 2px;
            color: rgba(255, 255, 255, 0.62);
        }

        .live-page-kicker,
        .live-eyebrow,
        .live-footer,
        .live-stat span,
        .live-toc-number {
            letter-spacing: 0.09em;
            text-transform: uppercase;
        }

        .live-page-kicker {
            display: block;
            margin-bottom: 38px;
            padding-bottom: 14px;
            border-bottom: 1px solid rgba(20, 20, 20, 0.1);
            color: #667085;
            font-size: 10px;
            font-weight: 900;
        }

        .live-eyebrow {
            display: block;
            margin-bottom: 10px;
            color: var(--proposal-accent, #e8b51f);
            font-size: 11px;
            font-weight: 900;
        }

        .live-title {
            margin: 0 0 18px;
            max-width: 650px;
            color: var(--proposal-primary, #111111);
            font-size: 40px;
            line-height: 1.08;
            font-weight: 900;
        }

        .live-body {
            max-width: 700px;
            color: #5f6368;
            font-size: 15px;
            line-height: 1.7;
        }

        .live-section-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 210px;
            gap: 36px;
        }

        .live-aside {
            padding: 20px;
            border: 1px solid rgba(20, 20, 20, 0.1);
            background: var(--proposal-secondary, #f3f4f0);
        }

        .live-stat-grid,
        .live-team-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 32px;
        }

        .live-stat,
        .live-card,
        .live-timeline-item {
            border: 1px solid rgba(20, 20, 20, 0.1);
            background: var(--proposal-secondary, #f3f4f0);
        }

        .live-stat {
            padding: 16px;
        }

        .live-stat strong {
            display: block;
            margin-top: 5px;
            color: var(--proposal-primary, #111111);
            font-size: 24px;
        }

        .live-toc {
            columns: 2;
            margin: 22px 0 0;
            padding: 0;
            list-style: none;
        }

        .live-toc li {
            display: grid;
            grid-template-columns: 44px 1fr;
            gap: 10px;
            break-inside: avoid;
            padding: 10px 0;
            border-bottom: 1px solid rgba(20, 20, 20, 0.08);
        }

        .live-toc-number {
            color: var(--proposal-accent, #e8b51f);
            font-weight: 900;
        }

        .live-table {
            width: 100%;
            margin-top: 22px;
            border-collapse: collapse;
            table-layout: fixed;
            border-top: 3px solid var(--proposal-accent, #e8b51f);
        }

        .live-table th,
        .live-table td {
            padding: 11px 9px;
            border-bottom: 1px solid rgba(20, 20, 20, 0.08);
            text-align: left;
            vertical-align: top;
        }

        .live-table th {
            background: var(--proposal-secondary, #f3f4f0);
            color: #667085;
            font-size: 10px;
            text-transform: uppercase;
        }

        .live-service-cell strong,
        .live-service-description {
            display: block;
        }

        .live-service-description {
            margin-top: 5px;
            color: #667085;
            font-size: 12px;
            line-height: 1.45;
        }

        .live-col-package {
            width: 15%;
        }

        .live-col-service {
            width: 52%;
        }

        .live-col-qty {
            width: 10%;
            text-align: center;
        }

        .live-col-total {
            width: 23%;
        }

        .live-table .amount {
            color: var(--proposal-primary, #111111);
            font-weight: 900;
            text-align: right;
            white-space: nowrap;
        }

        .live-timeline {
            display: grid;
            gap: 10px;
            margin-top: 22px;
        }

        .live-timeline-item {
            padding: 16px 18px 16px 58px;
            position: relative;
        }

        .live-timeline-item b {
            position: absolute;
            left: 18px;
            top: 20px;
            display: grid;
            place-items: center;
            width: 28px;
            height: 28px;
            background: var(--proposal-accent, #e8b51f);
            color: #111111;
        }

        .live-card {
            padding: 16px;
        }

        .live-card-avatar {
            display: grid;
            place-items: center;
            width: 54px;
            height: 54px;
            margin-bottom: 14px;
            border-radius: 50%;
            background: var(--proposal-primary, #111111);
            color: #ffffff;
            font-weight: 900;
        }

        .live-footer {
            position: absolute;
            left: 62px;
            right: 62px;
            bottom: 34px;
            display: flex;
            justify-content: space-between;
            padding-top: 12px;
            border-top: 1px solid rgba(20, 20, 20, 0.1);
            color: #667085;
            font-size: 10px;
            font-weight: 800;
        }

        .proposal-rows {
            display: grid;
            gap: 10px;
        }

        .proposal-data-row {
            display: grid;
            grid-template-columns: minmax(150px, 1fr) minmax(180px, 1.2fr) 74px 90px 78px 64px 32px;
            gap: 8px;
            align-items: start;
        }

        .proposal-timeline-row {
            grid-template-columns: minmax(130px, 0.8fr) minmax(190px, 1.2fr) 90px 110px 110px 84px 32px;
        }

        .proposal-team-row {
            grid-template-columns: minmax(120px, 0.8fr) minmax(120px, 0.8fr) minmax(190px, 1.2fr) minmax(150px, 1fr) 32px;
        }

        .proposal-data-row input,
        .proposal-data-row textarea,
        .proposal-data-row select {
            width: 100%;
            min-height: 36px;
            padding: 8px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #ffffff;
            font-size: 12px;
        }

        .proposal-data-row textarea {
            min-height: 36px;
            resize: vertical;
        }

        .proposal-empty-state {
            padding: 20px;
            border: 1px dashed var(--line);
            border-radius: 8px;
            color: var(--muted);
            text-align: center;
        }

        @media (max-width: 1420px) {
            .proposal-template-strip {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .proposal-builder-shell,
            .proposal-dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .proposal-template-strip,
            .proposal-form-grid,
            .proposal-color-row {
                grid-template-columns: 1fr;
            }

            .proposal-data-row,
            .proposal-timeline-row,
            .proposal-team-row {
                grid-template-columns: 1fr;
            }

            .proposal-preview-stage {
                padding: 12px;
            }

            .proposal-live-document {
                width: 720px;
            }
        }
    </style>

    <section class="dashboard-command proposal-studio-hero">
        <div class="dashboard-command-main">
            <span class="eyebrow">Proposal Studio</span>
            <h2>{{ $isEditing ? 'Refine the saved proposal.' : 'Create a client-ready proposal.' }}</h2>
            <p>Choose a template, edit the live document, and export when ready.</p>
            <div class="dashboard-command-actions">
                <a class="button" href="#proposal-builder">{{ $isEditing ? 'Continue' : 'Start' }}</a>
                @if ($builderProposal)
                    <a class="ghost-button" href="{{ route('admin.proposals.show', $builderProposal) }}">Open Preview</a>
                @endif
                <a class="ghost-button" href="#proposal-archive">Archive</a>
            </div>
        </div>

        <div class="dashboard-status-grid">
            <div class="status-card">
                <span class="metric-label">Saved Proposals</span>
                <strong>{{ number_format($proposals->count()) }}</strong>
                <p>Ready to edit or export.</p>
            </div>
            <div class="status-card">
                <span class="metric-label">Pipeline Value</span>
                <strong>${{ number_format($totalValue, 0) }}</strong>
                <p>Stored proposal total.</p>
            </div>
        </div>
    </section>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="panel panel-padded">
        <div class="panel-head">
            <span class="eyebrow">Templates</span>
            <h2>Choose a direction</h2>
            <p>Pick the visual structure before editing the live document.</p>
        </div>

        <div class="proposal-template-strip" data-template-list>
            @foreach ($templates as $template)
                <label class="proposal-template-card" data-template-card data-template-id="{{ $template->id }}">
                    <input type="radio" name="proposal_template_selector" value="{{ $template->id }}"
                        @checked((int) $builderState['proposal']['proposal_template_id'] === $template->id)>
                    <div class="template-mini-preview" data-theme="{{ $template->theme_key }}">
                        <div class="template-mini-cover">
                            <b>{{ Str::headline($template->theme_key) }} Proposal</b>
                        </div>
                        <div class="template-mini-pages">
                            <div class="template-mini-page">
                                <span class="template-mini-accent"></span>
                                <span class="template-mini-line"></span>
                                <span class="template-mini-line short"></span>
                            </div>
                            <div class="template-mini-page">
                                <span class="template-mini-line"></span>
                                <span class="template-mini-line"></span>
                                <span class="template-mini-accent"></span>
                            </div>
                            <div class="template-mini-page">
                                <span class="template-mini-line short"></span>
                                <span class="template-mini-line"></span>
                            </div>
                            <div class="template-mini-page">
                                <span class="template-mini-accent"></span>
                                <span class="template-mini-line short"></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <strong>{{ $template->name }}</strong>
                        <span>{{ $template->description }}</span>
                    </div>
                </label>
            @endforeach
        </div>
    </section>

    <form id="proposal-builder" method="POST"
        action="{{ $isEditing ? route('admin.proposals.update', $builderProposal) : route('admin.proposals.store') }}"
        enctype="multipart/form-data" data-proposal-builder data-builder-id="{{ $builderId }}">
        @csrf
        @if ($isEditing)
            @method('PUT')
        @endif

        <input type="hidden" name="proposal_template_id" data-field="proposal_template_id" value="{{ $builderState['proposal']['proposal_template_id'] }}">
        <input type="hidden" name="sections_payload" data-payload="sections">
        <input type="hidden" name="pricing_payload" data-payload="pricing">
        <input type="hidden" name="timeline_payload" data-payload="timeline">
        <input type="hidden" name="team_payload" data-payload="team">

        <div class="proposal-builder-shell">
            <aside class="proposal-builder-panel">
                <div class="panel-head">
                    <span class="eyebrow">Sections</span>
                    <h2 class="panel-title">Page stack</h2>
                </div>
                <div class="proposal-builder-panel-body">
                    <div class="proposal-section-list" data-section-list></div>

                    <div class="proposal-field" style="margin-top: 14px;">
                        <label for="section-library">Add Section</label>
                        <select id="section-library" data-section-library></select>
                    </div>
                    <button type="button" class="button" style="width: 100%; margin-top: 10px;" data-add-section>Add Section</button>
                </div>
            </aside>

            <section class="proposal-builder-panel">
                <div class="proposal-builder-toolbar">
                    <div class="proposal-builder-toolbar-group">
                        <button type="button" class="ghost-button" data-preview-zoom="100">100%</button>
                        <button type="button" class="ghost-button" data-preview-zoom="90">90%</button>
                        <button type="button" class="ghost-button" data-preview-zoom="75">75%</button>
                    </div>
                    <div class="proposal-builder-toolbar-group">
                        <span class="admin-pill" data-autosave-status>Ready</span>
                        <button type="button" class="ghost-button" data-ai-generate>Generate Draft</button>
                        <button type="button" class="button" data-ai-improve>Improve This Section</button>
                    </div>
                </div>

                <div class="proposal-preview-stage">
                    <div class="proposal-live-document" data-live-preview></div>
                </div>
            </section>

            <aside class="proposal-builder-panel">
                <div class="panel-head">
                    <span class="eyebrow">Setup and Style</span>
                    <h2 class="panel-title">Proposal details</h2>
                </div>
                <div class="proposal-builder-panel-body">
                    <div class="proposal-form-grid">
                        <div class="proposal-field full">
                            <label for="title">Proposal Title</label>
                            <input id="title" name="title" data-field="title" value="{{ $builderState['proposal']['title'] }}" required>
                        </div>
                        <div class="proposal-field">
                            <label for="client_name">Client Name</label>
                            <input id="client_name" name="client_name" data-field="client_name" value="{{ $builderState['proposal']['client_name'] }}">
                        </div>
                        <div class="proposal-field">
                            <label for="client_company">Client Company</label>
                            <input id="client_company" name="client_company" data-field="client_company" value="{{ $builderState['proposal']['client_company'] }}">
                        </div>
                        <div class="proposal-field">
                            <label for="prepared_by">Prepared By</label>
                            <input id="prepared_by" name="prepared_by" data-field="prepared_by" value="{{ $builderState['proposal']['prepared_by'] }}">
                        </div>
                        <div class="proposal-field">
                            <label for="company_name">Company Name</label>
                            <input id="company_name" name="company_name" data-field="company_name" value="{{ $builderState['proposal']['company_name'] }}" required>
                        </div>
                        <div class="proposal-field full">
                            <label for="company_slogan">Company Slogan</label>
                            <input id="company_slogan" name="company_slogan" data-field="company_slogan" value="{{ $builderState['proposal']['company_slogan'] }}">
                        </div>
                        <div class="proposal-field">
                            <label for="proposal_date">Proposal Date</label>
                            <input id="proposal_date" type="date" name="proposal_date" data-field="proposal_date" value="{{ $builderState['proposal']['proposal_date'] }}">
                        </div>
                        <div class="proposal-field">
                            <label for="reference_number">Reference</label>
                            <input id="reference_number" name="reference_number" data-field="reference_number" value="{{ $builderState['proposal']['reference_number'] }}">
                        </div>
                        <div class="proposal-field">
                            <label for="status">Status</label>
                            <select id="status" name="status" data-field="status">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" @selected($builderState['proposal']['status'] === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="proposal-field">
                            <label for="currency">Currency</label>
                            <input id="currency" name="currency" data-field="currency" value="{{ $builderState['proposal']['currency'] }}">
                        </div>
                        <div class="proposal-field">
                            <label for="contact_email">Email</label>
                            <input id="contact_email" type="email" name="contact_email" data-field="contact_email" value="{{ $builderState['proposal']['contact_email'] }}">
                        </div>
                        <div class="proposal-field">
                            <label for="phone_number">Phone</label>
                            <input id="phone_number" name="phone_number" data-field="phone_number" value="{{ $builderState['proposal']['phone_number'] }}">
                        </div>
                        <div class="proposal-field full">
                            <label for="website">Website</label>
                            <input id="website" name="website" data-field="website" value="{{ $builderState['proposal']['website'] }}">
                        </div>
                        <div class="proposal-field full">
                            <label for="business_address">Business Address</label>
                            <textarea id="business_address" name="business_address" data-field="business_address">{{ $builderState['proposal']['business_address'] }}</textarea>
                        </div>
                    </div>

                    <div class="proposal-micro-grid" style="margin-top: 18px;">
                        <div class="proposal-color-row">
                            <div class="proposal-field">
                                <label for="primary_color">Primary</label>
                                <input id="primary_color" type="color" name="primary_color" data-setting="primary_color" value="{{ $builderState['settings']['primary_color'] }}">
                            </div>
                            <div class="proposal-field">
                                <label for="secondary_color">Secondary</label>
                                <input id="secondary_color" type="color" name="secondary_color" data-setting="secondary_color" value="{{ $builderState['settings']['secondary_color'] }}">
                            </div>
                            <div class="proposal-field">
                                <label for="accent_color">Accent</label>
                                <input id="accent_color" type="color" name="accent_color" data-setting="accent_color" value="{{ $builderState['settings']['accent_color'] }}">
                            </div>
                        </div>

                        <div class="proposal-form-grid">
                            <div class="proposal-field">
                                <label for="font_family">Font</label>
                                <select id="font_family" name="font_family" data-setting="font_family">
                                    @foreach (['Aptos', 'Aptos Condensed', 'Segoe UI', 'Inter', 'Georgia'] as $font)
                                        <option value="{{ $font }}" @selected($builderState['settings']['font_family'] === $font)>{{ $font }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="proposal-field">
                                <label for="watermark">Watermark</label>
                                <input id="watermark" name="watermark" data-setting="watermark" value="{{ $builderState['settings']['watermark'] }}">
                            </div>
                            <div class="proposal-field">
                                <label for="header_style">Header</label>
                                <select id="header_style" name="header_style" data-setting="header_style">
                                    @foreach (['Minimal bar', 'Editorial split', 'Clean masthead', 'Dark executive', 'Agency grid'] as $style)
                                        <option value="{{ $style }}" @selected($builderState['settings']['header_style'] === $style)>{{ $style }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="proposal-field">
                                <label for="footer_style">Footer</label>
                                <select id="footer_style" name="footer_style" data-setting="footer_style">
                                    @foreach (['Reference footer', 'Gold folio', 'Thin line', 'Luxury footer', 'Project folio'] as $style)
                                        <option value="{{ $style }}" @selected($builderState['settings']['footer_style'] === $style)>{{ $style }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <label class="proposal-field" style="display:flex;grid-template-columns:auto 1fr;align-items:center;gap:10px;">
                            <input type="checkbox" name="page_numbering" value="1" data-setting="page_numbering" @checked($builderState['settings']['page_numbering']) style="width:auto;min-height:auto;">
                            <span style="font-size:12px;color:var(--text);font-weight:700;">Page numbering</span>
                        </label>
                    </div>

                    <div class="proposal-form-grid" style="margin-top: 18px;">
                        <div class="proposal-field">
                            <label for="logo">Logo</label>
                            <input id="logo" type="file" name="logo" accept="image/*" data-asset="logo">
                        </div>
                        <div class="proposal-field">
                            <label for="cover_image">Cover Image</label>
                            <input id="cover_image" type="file" name="cover_image" accept="image/*" data-asset="cover_image">
                        </div>
                        <div class="proposal-field full">
                            <label for="background_image">Background Image</label>
                            <input id="background_image" type="file" name="background_image" accept="image/*" data-asset="background_image">
                        </div>
                    </div>

                    <div class="proposal-micro-grid" style="margin-top: 18px;">
                        <div class="panel-head" style="padding:0;border:0;margin-bottom:0;">
                            <span class="eyebrow">Selected Section</span>
                            <h2 class="panel-title" data-selected-section-label>Section</h2>
                        </div>
                        <div class="proposal-field">
                            <label for="section_title">Title</label>
                            <input id="section_title" data-section-field="title">
                        </div>
                        <div class="proposal-field">
                            <label for="section_eyebrow">Eyebrow</label>
                            <input id="section_eyebrow" data-section-field="eyebrow">
                        </div>
                        <div class="proposal-field">
                            <label for="section_layout">Layout</label>
                            <select id="section_layout" data-section-field="layout_style">
                                <option value="editorial">Editorial</option>
                                <option value="split">Split</option>
                                <option value="table">Table</option>
                                <option value="cards">Cards</option>
                            </select>
                        </div>
                        <div class="proposal-field">
                            <label for="section_body">Body</label>
                            <textarea id="section_body" data-section-field="body"></textarea>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        <div class="proposal-dashboard-grid" style="margin-top: 24px;">
            <section class="panel panel-padded">
                <div class="panel-head">
                    <span class="eyebrow">Pricing Table Builder</span>
                    <h2>Packages and service investment</h2>
                </div>
                <div class="proposal-rows" data-pricing-rows></div>
                <button type="button" class="ghost-button" style="margin-top: 12px;" data-add-pricing>Add Pricing Item</button>
            </section>

            <section class="panel panel-padded">
                <div class="panel-head">
                    <span class="eyebrow">Timeline and Team</span>
                    <h2>Milestones and delivery ownership</h2>
                </div>
                <div class="proposal-rows" data-timeline-rows></div>
                <button type="button" class="ghost-button" style="margin-top: 12px;" data-add-timeline>Add Milestone</button>

                <div class="proposal-rows" data-team-rows style="margin-top: 20px;"></div>
                <button type="button" class="ghost-button" style="margin-top: 12px;" data-add-team>Add Team Member</button>
            </section>
        </div>

        <section class="panel panel-padded" style="margin-top: 24px;">
            <div class="section-heading" style="margin:0;">
                <div>
                    <span class="eyebrow">Export</span>
                    <h2>{{ $isEditing ? 'Export proposal' : 'Save to unlock exports' }}</h2>
                    <p>PDF, Word, print, and share link options.</p>
                </div>
                <div class="hero-actions" style="margin-top:0;">
                    <button type="submit" class="button">{{ $isEditing ? 'Save Proposal' : 'Generate Proposal' }}</button>
                    @if ($builderProposal)
                        <a class="ghost-button" href="{{ route('admin.proposals.pdf', $builderProposal) }}">PDF</a>
                        <a class="ghost-button" href="{{ route('admin.proposals.word', $builderProposal) }}">Word</a>
                        <a class="ghost-button" href="{{ route('admin.proposals.print', $builderProposal) }}">Print</a>
                        <a class="ghost-button" href="{{ route('proposals.share', $builderProposal->public_token) }}" target="_blank" rel="noopener">Share Link</a>
                    @endif
                </div>
            </div>
        </section>
    </form>

    <section class="panel panel-padded" id="proposal-archive" style="margin-top: 24px;">
        <div class="panel-head">
            <span class="eyebrow">Archive</span>
            <h2>Saved proposals</h2>
            <p>Edit, duplicate, export, or delete from one menu.</p>
        </div>

        <div class="table-wrap">
            <table class="quote-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Client</th>
                        <th>Template</th>
                        <th>Status</th>
                        <th>Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($proposals as $proposal)
                        <tr>
                            <td>
                                <strong>{{ $proposal->proposal_number }}</strong>
                                <span>{{ $proposal->reference_number }}</span>
                            </td>
                            <td>
                                <strong>{{ $proposal->client_company ?: $proposal->client_name ?: 'Client' }}</strong>
                                <span>{{ $proposal->title }}</span>
                            </td>
                            <td>
                                <strong>{{ $proposal->template?->name ?? 'Template' }}</strong>
                                <span>{{ Str::headline($proposal->theme_key) }}</span>
                            </td>
                            <td>
                                <strong>{{ ucfirst($proposal->status) }}</strong>
                                <span>Edited {{ optional($proposal->updated_at)->format('M d, Y') }}</span>
                            </td>
                            <td>
                                <strong>{{ $proposal->currency }} {{ number_format((float) $proposal->grand_total, 2) }}</strong>
                                <span>{{ optional($proposal->proposal_date)->format('M d, Y') }}</span>
                            </td>
                            <td>
                                <details class="action-menu">
                                    <summary>Actions</summary>
                                    <div class="action-menu-panel">
                                        <a href="{{ route('admin.proposals.show', $proposal) }}">Preview</a>
                                        <a href="{{ route('admin.proposals.edit', $proposal) }}">Edit</a>
                                        <a href="{{ route('admin.proposals.pdf', $proposal) }}">Download PDF</a>
                                        <form method="POST" action="{{ route('admin.proposals.duplicate', $proposal) }}">
                                            @csrf
                                            <button type="submit">Duplicate</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.proposals.destroy', $proposal) }}" onsubmit="return confirm('Delete this proposal?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit">Delete</button>
                                        </form>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <strong>No proposals created yet.</strong>
                                <span>Generate the first proposal from the builder above.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <script>
        (() => {
            const form = document.querySelector('[data-proposal-builder]');

            if (!form) {
                return;
            }

            const templates = {{ Js::from($templateOptions) }};
            const defaults = {{ Js::from(config('proposals.sections', [])) }};
            const state = {{ Js::from($builderState) }};
            const csrfToken = @json(csrf_token());
            const aiUrl = @json(route('admin.proposals.ai.improve'));
            const selectedTemplateInput = form.querySelector('[data-field="proposal_template_id"]');
            const livePreview = form.querySelector('[data-live-preview]');
            const sectionList = form.querySelector('[data-section-list]');
            const sectionLibrary = form.querySelector('[data-section-library]');
            const autosaveStatus = form.querySelector('[data-autosave-status]');
            const selectedSectionLabel = form.querySelector('[data-selected-section-label]');
            const storageKey = `proposal-builder:${form.dataset.builderId}`;
            let selectedSectionId = null;
            let dragSectionId = null;
            let autosaveTimer = null;

            const uid = () => `section-${Date.now()}-${Math.random().toString(16).slice(2)}`;
            const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            })[char]);
            const money = (value) => `${state.proposal.currency || 'USD'} ${Number(value || 0).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            })}`;

            state.sections = (state.sections || []).map((section) => ({
                id: section.id || uid(),
                type: section.type || 'custom',
                title: section.title || 'Proposal Section',
                eyebrow: section.eyebrow || '',
                body: section.body || '',
                payload: section.payload || {},
                layout_style: section.layout_style || 'editorial',
                is_visible: section.is_visible !== false,
            }));
            state.pricing = state.pricing || [];
            state.timeline = state.timeline || [];
            state.team = state.team || [];
            state.asset_urls = state.asset_urls || {};
            selectedSectionId = state.sections[0]?.id || null;

            defaults.forEach((section) => {
                const option = document.createElement('option');
                option.value = section.type;
                option.textContent = section.title;
                sectionLibrary.append(option);
            });

            const selectedSection = () => state.sections.find((section) => section.id === selectedSectionId) || state.sections[0];

            const collectSectionPayloads = () => {
                form.querySelector('[data-payload="sections"]').value = JSON.stringify(state.sections.map((section, index) => ({
                    type: section.type,
                    title: section.title,
                    eyebrow: section.eyebrow,
                    body: section.body,
                    payload: section.payload || {},
                    layout_style: section.layout_style || 'editorial',
                    is_visible: section.is_visible !== false,
                    sort_order: index,
                })));
                form.querySelector('[data-payload="pricing"]').value = JSON.stringify(state.pricing);
                form.querySelector('[data-payload="timeline"]').value = JSON.stringify(state.timeline);
                form.querySelector('[data-payload="team"]').value = JSON.stringify(state.team);
            };

            const scheduleAutosave = () => {
                window.clearTimeout(autosaveTimer);
                autosaveStatus.textContent = 'Editing';
                autosaveTimer = window.setTimeout(() => {
                    collectSectionPayloads();
                    localStorage.setItem(storageKey, JSON.stringify({
                        proposal: state.proposal,
                        settings: state.settings,
                        sections: state.sections,
                        pricing: state.pricing,
                        timeline: state.timeline,
                        team: state.team,
                        saved_at: new Date().toISOString(),
                    }));
                    autosaveStatus.textContent = 'Autosaved locally';
                }, 500);
            };

            const syncFormState = () => {
                form.querySelectorAll('[data-field]').forEach((field) => {
                    if (field.name === 'proposal_template_id') {
                        return;
                    }
                    state.proposal[field.dataset.field] = field.value;
                });

                form.querySelectorAll('[data-setting]').forEach((field) => {
                    state.settings[field.dataset.setting] = field.type === 'checkbox' ? field.checked : field.value;
                });
            };

            const applyTemplate = (templateId, shouldApplyPalette = true) => {
                const template = templates.find((item) => Number(item.id) === Number(templateId));

                if (!template) {
                    return;
                }

                selectedTemplateInput.value = template.id;
                state.proposal.proposal_template_id = template.id;
                state.theme_key = template.theme_key;

                if (shouldApplyPalette) {
                    const palette = template.palette || {};
                    const templateSettings = template.settings || {};
                    const settingMap = {
                        primary_color: palette.primary || '#111111',
                        secondary_color: palette.secondary || '#f3f4f0',
                        accent_color: palette.accent || '#e8b51f',
                        font_family: templateSettings.font_family || state.settings.font_family,
                        header_style: templateSettings.header_style || state.settings.header_style,
                        footer_style: templateSettings.footer_style || state.settings.footer_style,
                    };

                    Object.entries(settingMap).forEach(([key, value]) => {
                        state.settings[key] = value;
                        const field = form.querySelector(`[data-setting="${key}"]`);
                        if (field) {
                            field.value = value;
                        }
                    });
                }

                document.querySelectorAll('[data-template-card]').forEach((card) => {
                    const active = Number(card.dataset.templateId) === Number(template.id);
                    card.classList.toggle('is-active', active);
                    card.querySelector('input').checked = active;
                });
            };

            const renderSectionList = () => {
                sectionList.innerHTML = '';

                state.sections.forEach((section, index) => {
                    const item = document.createElement('div');
                    item.className = `proposal-section-item ${section.id === selectedSectionId ? 'is-active' : ''} ${section.is_visible === false ? 'is-hidden' : ''}`;
                    item.draggable = true;
                    item.dataset.sectionId = section.id;
                    item.innerHTML = `
                        <span class="proposal-section-index">${String(index + 1).padStart(2, '0')}</span>
                        <button type="button" style="all:unset;min-width:0;cursor:pointer;" data-select-section="${section.id}">
                            <strong>${escapeHtml(section.title)}</strong>
                            <span>${escapeHtml(section.type.replaceAll('_', ' '))}</span>
                        </button>
                        <span class="proposal-icon-row">
                            <button type="button" class="proposal-icon-button" title="Hide or show" data-toggle-section="${section.id}">${section.is_visible === false ? 'S' : 'H'}</button>
                            <button type="button" class="proposal-icon-button" title="Duplicate" data-duplicate-section="${section.id}">D</button>
                            <button type="button" class="proposal-icon-button" title="Remove" data-remove-section="${section.id}">X</button>
                        </span>
                    `;
                    sectionList.append(item);
                });
            };

            const renderSelectedSectionControls = () => {
                const section = selectedSection();

                if (!section) {
                    return;
                }

                selectedSectionLabel.textContent = section.title;
                form.querySelectorAll('[data-section-field]').forEach((field) => {
                    field.value = section[field.dataset.sectionField] ?? '';
                });
            };

            const renderPricingRows = () => {
                const wrap = form.querySelector('[data-pricing-rows]');
                wrap.innerHTML = '';

                state.pricing.forEach((item, index) => {
                    const row = document.createElement('div');
                    row.className = 'proposal-data-row';
                    row.innerHTML = `
                        <input value="${escapeHtml(item.service_name)}" placeholder="Service" data-pricing-field="service_name" data-index="${index}">
                        <textarea placeholder="Description" data-pricing-field="description" data-index="${index}">${escapeHtml(item.description)}</textarea>
                        <input type="number" step="0.01" value="${escapeHtml(item.quantity)}" data-pricing-field="quantity" data-index="${index}">
                        <input type="number" step="0.01" value="${escapeHtml(item.unit_price)}" data-pricing-field="unit_price" data-index="${index}">
                        <input type="number" step="0.01" value="${escapeHtml(item.discount || 0)}" data-pricing-field="discount" data-index="${index}">
                        <input type="number" step="0.01" value="${escapeHtml(item.tax_rate || 0)}" data-pricing-field="tax_rate" data-index="${index}">
                        <button type="button" class="proposal-icon-button" data-remove-pricing="${index}">X</button>
                    `;
                    wrap.append(row);
                });
            };

            const renderTimelineRows = () => {
                const wrap = form.querySelector('[data-timeline-rows]');
                wrap.innerHTML = '';

                state.timeline.forEach((item, index) => {
                    const row = document.createElement('div');
                    row.className = 'proposal-data-row proposal-timeline-row';
                    row.innerHTML = `
                        <input value="${escapeHtml(item.phase_title)}" placeholder="Phase" data-timeline-field="phase_title" data-index="${index}">
                        <textarea placeholder="Description" data-timeline-field="description" data-index="${index}">${escapeHtml(item.description)}</textarea>
                        <input value="${escapeHtml(item.duration || '')}" placeholder="Duration" data-timeline-field="duration" data-index="${index}">
                        <input type="date" value="${escapeHtml(item.start_date || '')}" data-timeline-field="start_date" data-index="${index}">
                        <input type="date" value="${escapeHtml(item.end_date || '')}" data-timeline-field="end_date" data-index="${index}">
                        <input value="${escapeHtml(item.status || 'Planned')}" data-timeline-field="status" data-index="${index}">
                        <button type="button" class="proposal-icon-button" data-remove-timeline="${index}">X</button>
                    `;
                    wrap.append(row);
                });
            };

            const renderTeamRows = () => {
                const wrap = form.querySelector('[data-team-rows]');
                wrap.innerHTML = '';

                state.team.forEach((item, index) => {
                    const row = document.createElement('div');
                    row.className = 'proposal-data-row proposal-team-row';
                    row.innerHTML = `
                        <input value="${escapeHtml(item.name)}" placeholder="Name" data-team-field="name" data-index="${index}">
                        <input value="${escapeHtml(item.role || '')}" placeholder="Role" data-team-field="role" data-index="${index}">
                        <textarea placeholder="Bio" data-team-field="bio" data-index="${index}">${escapeHtml(item.bio || '')}</textarea>
                        <input value="${escapeHtml(item.email || '')}" placeholder="Email" data-team-field="email" data-index="${index}">
                        <button type="button" class="proposal-icon-button" data-remove-team="${index}">X</button>
                    `;
                    wrap.append(row);
                });
            };

            const pricingTotals = () => state.pricing.reduce((totals, item) => {
                const base = Number(item.quantity || 0) * Number(item.unit_price || 0);
                const discount = Math.min(Number(item.discount || 0), base);
                const tax = (base - discount) * (Number(item.tax_rate || 0) / 100);
                totals.subtotal += base;
                totals.discount += discount;
                totals.tax += tax;
                totals.grand += base - discount + tax;
                return totals;
            }, { subtotal: 0, discount: 0, tax: 0, grand: 0 });

            const renderPreview = () => {
                syncFormState();
                livePreview.style.setProperty('--proposal-primary', state.settings.primary_color);
                livePreview.style.setProperty('--proposal-secondary', state.settings.secondary_color);
                livePreview.style.setProperty('--proposal-accent', state.settings.accent_color);
                livePreview.style.setProperty('--proposal-font', state.settings.font_family);

                const visible = state.sections.filter((section) => section.is_visible !== false);
                const total = pricingTotals();
                const contact = [state.proposal.contact_email, state.proposal.phone_number, state.proposal.website].filter(Boolean).join(' / ');
                const coverImage = state.asset_urls.cover_image ? `<img src="${escapeHtml(state.asset_urls.cover_image)}" alt="">` : '';

                livePreview.innerHTML = visible.map((section, index) => {
                    if (section.type === 'cover') {
                        return `
                            <section class="live-page live-cover">
                                <div class="live-cover-main">
                                    <span class="live-cover-year">${escapeHtml((state.proposal.proposal_date || new Date().toISOString()).slice(0, 4))}</span>
                                    <h1><span>${escapeHtml((state.proposal.title || 'Business Proposal').split(' ').slice(0, -1).join(' ') || 'Business')}</span>${escapeHtml((state.proposal.title || 'Proposal').split(' ').slice(-1)[0])}</h1>
                                    <div class="live-meta">
                                        <div class="live-meta-item">
                                            <span>Prepared for</span>
                                            <strong>${escapeHtml(state.proposal.client_company || state.proposal.client_name || 'Client')}</strong>
                                            <small>${escapeHtml(state.proposal.client_name || '')}</small>
                                        </div>
                                        <div class="live-meta-item">
                                            <span>Prepared by</span>
                                            <strong>${escapeHtml(state.proposal.prepared_by || state.proposal.company_name)}</strong>
                                            <small>${escapeHtml(state.proposal.company_name || '')}</small>
                                        </div>
                                        <div class="live-meta-item">
                                            <span>Reference</span>
                                            <strong>${escapeHtml(state.proposal.reference_number || '')}</strong>
                                            <small>${escapeHtml(state.proposal.proposal_date || '')}</small>
                                        </div>
                                    </div>
                                    <div class="live-footer">${escapeHtml(contact)}<span>${escapeHtml(state.proposal.company_name)}</span></div>
                                </div>
                                <div class="live-cover-side"><div class="live-cover-image">${coverImage}</div></div>
                            </section>
                        `;
                    }

                    if (section.type === 'table_of_contents') {
                        return `
                            <section class="live-page">
                                <span class="live-page-kicker">${escapeHtml(state.proposal.company_name)} / ${String(index + 1).padStart(2, '0')}</span>
                                <span class="live-eyebrow" contenteditable data-edit-section="${section.id}" data-edit-field="eyebrow">${escapeHtml(section.eyebrow)}</span>
                                <h2 class="live-title" contenteditable data-edit-section="${section.id}" data-edit-field="title">${escapeHtml(section.title)}</h2>
                                <ol class="live-toc">
                                    ${visible.map((toc, tocIndex) => `<li><span class="live-toc-number">${String(tocIndex + 1).padStart(2, '0')}</span><strong>${escapeHtml(toc.title)}</strong></li>`).join('')}
                                </ol>
                                <footer class="live-footer">${escapeHtml(contact)}<span>${escapeHtml(state.proposal.company_name)}</span></footer>
                            </section>
                        `;
                    }

                    if (section.type === 'pricing') {
                        return `
                            <section class="live-page">
                                <span class="live-page-kicker">${escapeHtml(state.proposal.company_name)} / ${String(index + 1).padStart(2, '0')}</span>
                                <span class="live-eyebrow" contenteditable data-edit-section="${section.id}" data-edit-field="eyebrow">${escapeHtml(section.eyebrow)}</span>
                                <h2 class="live-title" contenteditable data-edit-section="${section.id}" data-edit-field="title">${escapeHtml(section.title)}</h2>
                                <p class="live-body" contenteditable data-edit-section="${section.id}" data-edit-field="body">${escapeHtml(section.body)}</p>
                                <table class="live-table">
                                    <thead><tr><th class="live-col-package">Package</th><th class="live-col-service">Service</th><th class="live-col-qty">Qty</th><th class="live-col-total amount">Total</th></tr></thead>
                                    <tbody>${state.pricing.map((item) => {
                                        const base = Number(item.quantity || 0) * Number(item.unit_price || 0);
                                        const discount = Math.min(Number(item.discount || 0), base);
                                        const tax = (base - discount) * (Number(item.tax_rate || 0) / 100);
                                        return `<tr><td class="live-col-package">${escapeHtml(item.package || 'Custom')}</td><td class="live-col-service live-service-cell"><strong>${escapeHtml(item.service_name)}</strong><span class="live-service-description">${escapeHtml(item.description || '')}</span></td><td class="live-col-qty">${escapeHtml(item.quantity || 1)}</td><td class="live-col-total amount">${money(base - discount + tax).replace(' ', '&nbsp;')}</td></tr>`;
                                    }).join('')}</tbody>
                                </table>
                                <table class="live-table" style="max-width:360px;margin-left:auto;"><tbody>
                                    <tr><td>Subtotal</td><td class="amount">${money(total.subtotal)}</td></tr>
                                    <tr><td>Discount</td><td class="amount">${money(total.discount)}</td></tr>
                                    <tr><td>Tax / VAT</td><td class="amount">${money(total.tax)}</td></tr>
                                    <tr><td><strong>Grand Total</strong></td><td class="amount">${money(total.grand)}</td></tr>
                                </tbody></table>
                                <footer class="live-footer">${escapeHtml(contact)}<span>${escapeHtml(state.proposal.company_name)}</span></footer>
                            </section>
                        `;
                    }

                    if (section.type === 'timeline' || section.type === 'work_process') {
                        return `
                            <section class="live-page">
                                <span class="live-page-kicker">${escapeHtml(state.proposal.company_name)} / ${String(index + 1).padStart(2, '0')}</span>
                                <span class="live-eyebrow" contenteditable data-edit-section="${section.id}" data-edit-field="eyebrow">${escapeHtml(section.eyebrow)}</span>
                                <h2 class="live-title" contenteditable data-edit-section="${section.id}" data-edit-field="title">${escapeHtml(section.title)}</h2>
                                <p class="live-body" contenteditable data-edit-section="${section.id}" data-edit-field="body">${escapeHtml(section.body)}</p>
                                <div class="live-timeline">
                                    ${state.timeline.map((item, timelineIndex) => `<article class="live-timeline-item"><b>${timelineIndex + 1}</b><h3>${escapeHtml(item.phase_title)}</h3><p>${escapeHtml(item.description)}</p><small>${escapeHtml(item.duration || '')} / ${escapeHtml(item.status || 'Planned')}</small></article>`).join('')}
                                </div>
                                <footer class="live-footer">${escapeHtml(contact)}<span>${escapeHtml(state.proposal.company_name)}</span></footer>
                            </section>
                        `;
                    }

                    if (section.type === 'team') {
                        return `
                            <section class="live-page">
                                <span class="live-page-kicker">${escapeHtml(state.proposal.company_name)} / ${String(index + 1).padStart(2, '0')}</span>
                                <span class="live-eyebrow" contenteditable data-edit-section="${section.id}" data-edit-field="eyebrow">${escapeHtml(section.eyebrow)}</span>
                                <h2 class="live-title" contenteditable data-edit-section="${section.id}" data-edit-field="title">${escapeHtml(section.title)}</h2>
                                <p class="live-body" contenteditable data-edit-section="${section.id}" data-edit-field="body">${escapeHtml(section.body)}</p>
                                <div class="live-team-grid">
                                    ${state.team.slice(0, 3).map((item) => `<article class="live-card"><span class="live-card-avatar">${escapeHtml((item.name || 'T').slice(0, 1).toUpperCase())}</span><h3>${escapeHtml(item.name)}</h3><strong>${escapeHtml(item.role || '')}</strong><p>${escapeHtml(item.bio || '')}</p></article>`).join('')}
                                </div>
                                <footer class="live-footer">${escapeHtml(contact)}<span>${escapeHtml(state.proposal.company_name)}</span></footer>
                            </section>
                        `;
                    }

                    return `
                        <section class="live-page" data-preview-section="${section.id}">
                            <span class="live-page-kicker">${escapeHtml(state.proposal.company_name)} / ${String(index + 1).padStart(2, '0')}</span>
                            <div class="live-section-grid">
                                <main>
                                    <span class="live-eyebrow" contenteditable data-edit-section="${section.id}" data-edit-field="eyebrow">${escapeHtml(section.eyebrow)}</span>
                                    <h2 class="live-title" contenteditable data-edit-section="${section.id}" data-edit-field="title">${escapeHtml(section.title)}</h2>
                                    <p class="live-body" contenteditable data-edit-section="${section.id}" data-edit-field="body">${escapeHtml(section.body)}</p>
                                </main>
                                <aside class="live-aside"><strong>${String(index + 1).padStart(2, '0')}</strong><p>${escapeHtml(state.proposal.client_company || state.proposal.client_name || 'Client-ready proposal')}</p></aside>
                            </div>
                            <div class="live-stat-grid">
                                <div class="live-stat"><span>Focus</span><strong>01</strong><p>Clear direction.</p></div>
                                <div class="live-stat"><span>Value</span><strong>02</strong><p>Business impact.</p></div>
                                <div class="live-stat"><span>Delivery</span><strong>03</strong><p>Confident handoff.</p></div>
                            </div>
                            <footer class="live-footer">${escapeHtml(contact)}<span>${escapeHtml(state.proposal.company_name)}</span></footer>
                        </section>
                    `;
                }).join('');
            };

            const renderAll = () => {
                syncFormState();
                renderSectionList();
                renderSelectedSectionControls();
                renderPricingRows();
                renderTimelineRows();
                renderTeamRows();
                renderPreview();
                collectSectionPayloads();
                scheduleAutosave();
            };

            document.querySelectorAll('[data-template-card]').forEach((card) => {
                card.addEventListener('click', () => {
                    applyTemplate(card.dataset.templateId, true);
                    renderAll();
                });
            });

            sectionList.addEventListener('dragstart', (event) => {
                dragSectionId = event.target.closest('[data-section-id]')?.dataset.sectionId;
            });

            sectionList.addEventListener('dragover', (event) => {
                event.preventDefault();
            });

            sectionList.addEventListener('drop', (event) => {
                event.preventDefault();
                const targetId = event.target.closest('[data-section-id]')?.dataset.sectionId;

                if (!dragSectionId || !targetId || dragSectionId === targetId) {
                    return;
                }

                const from = state.sections.findIndex((section) => section.id === dragSectionId);
                const to = state.sections.findIndex((section) => section.id === targetId);
                const [section] = state.sections.splice(from, 1);
                state.sections.splice(to, 0, section);
                renderAll();
            });

            sectionList.addEventListener('click', (event) => {
                const select = event.target.closest('[data-select-section]');
                const toggle = event.target.closest('[data-toggle-section]');
                const duplicate = event.target.closest('[data-duplicate-section]');
                const remove = event.target.closest('[data-remove-section]');

                if (select) {
                    selectedSectionId = select.dataset.selectSection;
                }

                if (toggle) {
                    const section = state.sections.find((item) => item.id === toggle.dataset.toggleSection);
                    section.is_visible = section.is_visible === false;
                    selectedSectionId = section.id;
                }

                if (duplicate) {
                    const section = state.sections.find((item) => item.id === duplicate.dataset.duplicateSection);
                    const copy = { ...section, id: uid(), title: `${section.title} Copy` };
                    state.sections.splice(state.sections.indexOf(section) + 1, 0, copy);
                    selectedSectionId = copy.id;
                }

                if (remove && state.sections.length > 1) {
                    state.sections = state.sections.filter((item) => item.id !== remove.dataset.removeSection);
                    selectedSectionId = state.sections[0].id;
                }

                renderAll();
            });

            form.querySelector('[data-add-section]').addEventListener('click', () => {
                const base = defaults.find((section) => section.type === sectionLibrary.value) || defaults[0];
                const section = { ...base, id: uid(), is_visible: true, layout_style: 'editorial', payload: {} };
                state.sections.push(section);
                selectedSectionId = section.id;
                renderAll();
            });

            form.querySelectorAll('[data-field], [data-setting]').forEach((field) => {
                field.addEventListener('input', renderAll);
                field.addEventListener('change', renderAll);
            });

            form.querySelectorAll('[data-section-field]').forEach((field) => {
                field.addEventListener('input', () => {
                    const section = selectedSection();
                    section[field.dataset.sectionField] = field.value;
                    renderAll();
                });
            });

            livePreview.addEventListener('input', (event) => {
                const editable = event.target.closest('[data-edit-section]');
                if (!editable) {
                    return;
                }

                const section = state.sections.find((item) => item.id === editable.dataset.editSection);
                section[editable.dataset.editField] = editable.textContent.trim();
                selectedSectionId = section.id;
                renderSectionList();
                renderSelectedSectionControls();
                collectSectionPayloads();
                scheduleAutosave();
            });

            livePreview.addEventListener('focusin', (event) => {
                const editable = event.target.closest('[data-edit-section]');
                if (editable) {
                    selectedSectionId = editable.dataset.editSection;
                    renderSectionList();
                    renderSelectedSectionControls();
                }
            });

            form.addEventListener('input', (event) => {
                const pricingField = event.target.closest('[data-pricing-field]');
                const timelineField = event.target.closest('[data-timeline-field]');
                const teamField = event.target.closest('[data-team-field]');

                if (pricingField) {
                    state.pricing[Number(pricingField.dataset.index)][pricingField.dataset.pricingField] = pricingField.value;
                    renderPreview();
                    collectSectionPayloads();
                    scheduleAutosave();
                }

                if (timelineField) {
                    state.timeline[Number(timelineField.dataset.index)][timelineField.dataset.timelineField] = timelineField.value;
                    renderPreview();
                    collectSectionPayloads();
                    scheduleAutosave();
                }

                if (teamField) {
                    state.team[Number(teamField.dataset.index)][teamField.dataset.teamField] = teamField.value;
                    renderPreview();
                    collectSectionPayloads();
                    scheduleAutosave();
                }
            });

            form.addEventListener('click', (event) => {
                const removePricing = event.target.closest('[data-remove-pricing]');
                const removeTimeline = event.target.closest('[data-remove-timeline]');
                const removeTeam = event.target.closest('[data-remove-team]');

                if (removePricing) {
                    state.pricing.splice(Number(removePricing.dataset.removePricing), 1);
                    renderAll();
                }

                if (removeTimeline) {
                    state.timeline.splice(Number(removeTimeline.dataset.removeTimeline), 1);
                    renderAll();
                }

                if (removeTeam) {
                    state.team.splice(Number(removeTeam.dataset.removeTeam), 1);
                    renderAll();
                }
            });

            form.querySelector('[data-add-pricing]').addEventListener('click', () => {
                state.pricing.push({ package: 'Custom', service_name: 'New service', description: '', quantity: 1, unit_price: 0, discount: 0, tax_rate: 0 });
                renderAll();
            });

            form.querySelector('[data-add-timeline]').addEventListener('click', () => {
                state.timeline.push({ phase_title: 'New phase', description: '', duration: '', start_date: '', end_date: '', deliverables: '', status: 'Planned' });
                renderAll();
            });

            form.querySelector('[data-add-team]').addEventListener('click', () => {
                state.team.push({ name: 'Team Member', role: 'Project Role', bio: '', email: '', social_link: '' });
                renderAll();
            });

            form.querySelectorAll('[data-asset]').forEach((input) => {
                input.addEventListener('change', () => {
                    const file = input.files?.[0];
                    if (!file) {
                        return;
                    }

                    const reader = new FileReader();
                    reader.addEventListener('load', () => {
                        state.asset_urls[input.dataset.asset] = reader.result;
                        renderPreview();
                    });
                    reader.readAsDataURL(file);
                });
            });

            form.querySelectorAll('[data-preview-zoom]').forEach((button) => {
                button.addEventListener('click', () => {
                    livePreview.classList.remove('zoom-90', 'zoom-75');
                    if (button.dataset.previewZoom !== '100') {
                        livePreview.classList.add(`zoom-${button.dataset.previewZoom}`);
                    }
                });
            });

            const runAi = async (mode) => {
                const section = selectedSection();
                if (!section) {
                    return;
                }

                autosaveStatus.textContent = 'Writing';

                const response = await fetch(aiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        mode,
                        type: section.type,
                        title: section.title,
                        body: section.body,
                        proposal_title: state.proposal.title,
                        client_company: state.proposal.client_company,
                        company_name: state.proposal.company_name,
                    }),
                });

                if (!response.ok) {
                    autosaveStatus.textContent = 'AI unavailable';
                    return;
                }

                const data = await response.json();
                section.body = data.content || section.body;
                renderAll();
                autosaveStatus.textContent = 'Section improved';
            };

            form.querySelector('[data-ai-generate]').addEventListener('click', () => runAi('generate'));
            form.querySelector('[data-ai-improve]').addEventListener('click', () => runAi('improve'));

            form.addEventListener('submit', () => {
                syncFormState();
                collectSectionPayloads();
            });

            applyTemplate(state.proposal.proposal_template_id, false);
            renderAll();
        })();
    </script>
@endsection
