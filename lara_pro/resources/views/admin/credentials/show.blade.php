@extends('admin.layouts.app')

@section('title', $project->name.' | Credentials')

@section('content')
    <section class="panel hero-banner credential-vault-hero">
        <div>
            <span class="eyebrow">Credential vault · {{ $project->project_number }}</span>
            <h1>{{ $project->name }}</h1>
            <p>{{ $project->client_company ?: ($project->client_name ?: 'Internal project') }} · Secure project access records and final handover documentation.</p>
            <div class="hero-actions">
                <a class="ghost-button" href="{{ route('admin.credentials.index') }}">All credential vaults</a>
                <a class="ghost-button" href="{{ route('admin.projects.show', $project) }}">Project files</a>
                <a class="ghost-button" href="{{ route('admin.project-management.board', $project) }}">Project board</a>
            </div>
        </div>
        <div class="hero-callout">
            <div class="callout-card"><span class="metric-label">Stored credentials</span><strong>{{ number_format($credentials->count()) }}</strong><p>Encrypted entries in this project vault.</p></div>
            <div class="callout-card"><span class="metric-label">Project status</span><strong>{{ \Illuminate\Support\Str::headline($project->status ?: 'Uncategorised') }}</strong><p>Handover PDF is available after the first entry.</p></div>
        </div>
    </section>

    <div style="margin-top: 24px;">
        @include('admin.credentials.partials.vault')
    </div>
@endsection

@include('admin.credentials.partials.assets')
