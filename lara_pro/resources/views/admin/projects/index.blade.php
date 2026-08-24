@extends('admin.layouts.app')

@section('title', 'Projects & Files | Admin')

@section('content')
    <section class="panel hero-banner project-hero-banner">
        <div>
            <span class="eyebrow">Project workspace</span>
            <h1>Keep every project handoff in one calm, searchable place.</h1>
            <p>
                Review project health, see where files are accumulating, and open a secure file workspace for every
                client engagement.
            </p>
            <div class="hero-actions">
                <a class="button" href="{{ route('admin.staff-contracts.index') }}">Open Staff Contracts</a>
                <a class="ghost-button" href="{{ route('admin.quotes.index') }}">View Invoices</a>
            </div>
        </div>
        <div class="hero-callout">
            <div class="callout-card">
                <span class="metric-label">Shared handoffs</span>
                <strong>{{ number_format($sharedFileCount) }}</strong>
                <p>Files currently available through secure share links.</p>
            </div>
            <div class="callout-card">
                <span class="metric-label">Project coverage</span>
                <strong>{{ $projectCount > 0 ? number_format(($fileCount / $projectCount), 1) : '0.0' }}</strong>
                <p>Average files attached to each project record.</p>
            </div>
        </div>
    </section>

    <section class="kpi-grid project-kpi-grid" style="margin-top: 24px;">
        <article class="panel kpi-card kpi-card--quotes">
            <span class="metric-label">Total projects</span>
            <strong class="kpi-value">{{ number_format($projectCount) }}</strong>
            <span class="kpi-meta">Project records created from active engagements.</span>
        </article>
        <article class="panel kpi-card kpi-card--leads">
            <span class="metric-label">Active projects</span>
            <strong class="kpi-value">{{ number_format($activeCount) }}</strong>
            <span class="kpi-meta">Active or in-progress work that needs attention.</span>
        </article>
        <article class="panel kpi-card kpi-card--pipeline">
            <span class="metric-label">Project files</span>
            <strong class="kpi-value">{{ number_format($fileCount) }}</strong>
            <span class="kpi-meta">Private files stored against a project record.</span>
        </article>
        <article class="panel kpi-card kpi-card--traffic">
            <span class="metric-label">Shared files</span>
            <strong class="kpi-value">{{ number_format($sharedFileCount) }}</strong>
            <span class="kpi-meta">Links ready for client or team handoff.</span>
        </article>
    </section>

    <div class="project-analytics-grid" style="margin-top: 24px;">
        <section class="panel panel-padded project-status-card">
            <div class="panel-head panel-head--tight">
                <span class="eyebrow">Portfolio health</span>
                <h2 class="panel-title">Projects by status</h2>
                <p>See the current shape of the project portfolio before opening a record.</p>
            </div>

            <div class="project-status-chart-wrap">
                <div class="project-status-chart" style="{{ $statusChartStyle }}" role="img"
                    aria-label="Project status distribution">
                    <div class="project-status-chart__centre">
                        <strong>{{ number_format($projectCount) }}</strong>
                        <span>projects</span>
                    </div>
                </div>

                @if ($statusCounts->isNotEmpty())
                    <div class="project-status-legend">
                        @foreach ($statusCounts as $index => $status)
                            <div class="project-status-legend__item">
                                <span class="project-status-legend__dot project-status-legend__dot--{{ $index % 6 }}"></span>
                                <span>
                                    <strong>{{ $status['label'] }}</strong>
                                    <small>{{ number_format($status['count']) }}
                                        {{ \Illuminate\Support\Str::plural('project', $status['count']) }}</small>
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="data-note">Status distribution will appear after the first project record is created.</div>
                @endif
            </div>
        </section>

        <section class="panel panel-padded">
            <div class="panel-head panel-head--tight">
                <span class="eyebrow">Handoff activity</span>
                <h2 class="panel-title">Files by project</h2>
                <p>Projects with the most attached files, so handoff-heavy work stays visible.</p>
            </div>

            @if ($fileLeaders->isNotEmpty())
                <div class="project-file-bars">
                    @foreach ($fileLeaders as $projectFileLeader)
                        <div class="project-file-bar">
                            <div class="bar-header">
                                <div>
                                    <strong>{{ $projectFileLeader['name'] }}</strong>
                                    <span class="bar-meta">{{ $projectFileLeader['project_number'] }}</span>
                                </div>
                                <strong class="bar-count">{{ number_format($projectFileLeader['count']) }}</strong>
                            </div>
                            <div class="bar-track" role="progressbar" aria-label="Files for {{ $projectFileLeader['name'] }}"
                                aria-valuenow="{{ $projectFileLeader['count'] }}" aria-valuemin="0"
                                aria-valuemax="{{ max(1, $projects->max('files_count')) }}">
                                <div class="bar-fill bar-fill--quote" style="width: {{ max(5, $projectFileLeader['width']) }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="project-empty-chart">
                    <span class="project-empty-chart__icon" aria-hidden="true">＋</span>
                    <strong>No project files yet</strong>
                    <p>Open a project to add briefs, references, signed documents, and delivery files.</p>
                </div>
            @endif
        </section>
    </div>

    <section class="panel panel-padded" style="margin-top: 24px;">
        <div class="panel-head panel-head--row">
            <div>
                <span class="eyebrow">Project register</span>
                <h2>Every engagement, ready for handoff</h2>
                <p>Projects are created from invoice-linked staff agreements and keep their files independent from contracts.</p>
            </div>
            <span class="admin-pill">{{ number_format($projectCount) }} records</span>
        </div>

        <div class="table-wrap">
            <table class="quote-table project-table">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Client</th>
                        <th>Files</th>
                        <th>Contracts</th>
                        <th>Status</th>
                        <th>Open</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $project)
                        <tr>
                            <td>
                                <strong>{{ $project->name }}</strong>
                                <span>{{ $project->project_number }}</span>
                            </td>
                            <td>
                                <strong>{{ $project->client_company ?: ($project->client_name ?: 'Client not provided') }}</strong>
                                @if ($project->client_company && $project->client_name)
                                    <span>{{ $project->client_name }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ number_format($project->files_count) }}</strong>
                                <span>{{ number_format($project->shared_files_count) }} shared</span>
                            </td>
                            <td>
                                <strong>{{ number_format($project->staff_contracts_count) }}</strong>
                                <span>Staff {{ \Illuminate\Support\Str::plural('agreement', $project->staff_contracts_count) }}</span>
                            </td>
                            <td><span class="project-status-badge">{{ \Illuminate\Support\Str::headline($project->status ?: 'uncategorised') }}</span></td>
                            <td><a class="table-link" href="{{ route('admin.projects.show', $project) }}">Open workspace</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <strong>No project records yet.</strong>
                                <span>Create an invoice-linked staff contract to start a project workspace.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
