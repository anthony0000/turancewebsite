@extends('admin.layouts.app')

@section('title', 'Credentials | Admin')

@section('content')
    <section class="panel hero-banner credential-hero">
        <div>
            <span class="eyebrow">Secure access management</span>
            <h1>Credentials</h1>
            <p>Maintain encrypted project access details in a dedicated workspace and prepare a branded credential handover when delivery is complete.</p>
        </div>
        <div class="hero-callout">
            <div class="callout-card"><span class="metric-label">Stored credentials</span><strong>{{ number_format($credentialCount) }}</strong><p>Encrypted access records across all projects.</p></div>
            <div class="callout-card"><span class="metric-label">Secured projects</span><strong>{{ number_format($securedProjectCount) }}</strong><p>Projects with at least one credential stored.</p></div>
        </div>
    </section>

    <section class="panel panel-padded credential-directory" style="margin-top: 24px;">
        <div class="panel-head panel-head--row credential-directory__head">
            <div>
                <span class="eyebrow">Project vaults</span>
                <h2>Choose a project</h2>
                <p>Each vault remains tied to its project record and produces its own letterhead collation PDF.</p>
            </div>
            <form method="GET" action="{{ route('admin.credentials.index') }}" class="credential-search" role="search">
                <label class="sr-only" for="credential-project-search">Search projects</label>
                <input id="credential-project-search" type="search" name="q" value="{{ $search }}" placeholder="Search project or client">
                <button class="ghost-button" type="submit">Search</button>
                @if ($search !== '')<a href="{{ route('admin.credentials.index') }}">Clear</a>@endif
            </form>
        </div>

        <div class="credential-project-grid">
            @forelse ($projects as $project)
                <article class="credential-project-card">
                    <div class="credential-project-card__top">
                        <span class="credential-project-card__mark" aria-hidden="true">{{ strtoupper(substr($project->name, 0, 1)) }}</span>
                        <span class="project-status-badge">{{ \Illuminate\Support\Str::headline($project->status ?: 'Uncategorised') }}</span>
                    </div>
                    <div>
                        <span class="credential-project-card__key">{{ $project->project_number }}</span>
                        <h3>{{ $project->name }}</h3>
                        <p>{{ $project->client_company ?: ($project->client_name ?: 'Internal project') }}</p>
                    </div>
                    <div class="credential-project-card__footer">
                        <span><strong>{{ number_format($project->credentials_count) }}</strong> {{ \Illuminate\Support\Str::plural('credential', $project->credentials_count) }}</span>
                        <a class="button" href="{{ route('admin.credentials.show', $project) }}">Open vault</a>
                    </div>
                </article>
            @empty
                <div class="project-files-empty credential-directory__empty">
                    <span class="project-files-empty__icon" aria-hidden="true">⌁</span>
                    <h3>{{ $search !== '' ? 'No matching projects found.' : 'No projects are available yet.' }}</h3>
                    <p>{{ $search !== '' ? 'Try a project number, project name, or client name.' : 'Create a project first, then return here to store its access details.' }}</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .credential-directory__head { align-items: flex-end; }
        .credential-search { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: 8px; }
        .credential-search input { width: min(280px, 100%); min-height: 40px; padding: 0 11px; border: 1px solid var(--line); border-radius: 6px; background: var(--surface-soft); color: var(--text); }
        .credential-search a { color: var(--muted); font-size: 11px; font-weight: 700; }
        .credential-project-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
        .credential-project-card { display: grid; min-height: 228px; align-content: space-between; gap: 18px; padding: 18px; border: 1px solid var(--line-soft); border-radius: 10px; background: linear-gradient(145deg, var(--surface), var(--surface-soft)); }
        .credential-project-card__top, .credential-project-card__footer { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .credential-project-card__mark { display: grid; width: 38px; height: 38px; place-items: center; border-radius: 11px; background: var(--primary-soft); color: var(--primary-strong); font-size: 13px; font-weight: 850; }
        .credential-project-card__key { color: var(--primary-strong); font-size: 9px; font-weight: 850; letter-spacing: .1em; text-transform: uppercase; }
        .credential-project-card h3 { margin: 5px 0 0; color: var(--text); font-size: 17px; }
        .credential-project-card p { margin: 6px 0 0; color: var(--muted); font-size: 11px; }
        .credential-project-card__footer { padding-top: 14px; border-top: 1px solid var(--line-soft); color: var(--muted); font-size: 11px; }
        .credential-project-card__footer strong { color: var(--text); font-size: 14px; }
        .credential-project-card__footer .button { min-height: 36px; padding-inline: 12px; font-size: 11px; }
        .credential-directory__empty { grid-column: 1 / -1; min-height: 260px; }
        @media (max-width: 1050px) { .credential-project-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 700px) { .credential-directory__head { display: block; } .credential-search { justify-content: flex-start; margin-top: 14px; } .credential-project-grid { grid-template-columns: 1fr; } }
    </style>
@endpush
