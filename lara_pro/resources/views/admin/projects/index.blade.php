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
                @if ($canManageProjectFiles)
                    <a class="button" href="#project-file-upload">Upload Project File</a>
                @endif
                <a class="ghost-button" href="{{ route('admin.staff-contracts.index') }}">Open Staff Contracts</a>
                <a class="ghost-button" href="{{ route('admin.quotes.index') }}">View Invoices</a>
            </div>
        </div>
        <div class="hero-callout">
            <div class="callout-card">
                <span class="metric-label">Shared handoffs</span>
                @if ($canViewProjectFiles)
                    <strong>{{ number_format($sharedFileCount) }}</strong>
                    <p>Shared files available to this account.</p>
                @else
                    <strong>Limited</strong>
                    <p>File access is limited for this account.</p>
                @endif
            </div>
            <div class="callout-card">
                <span class="metric-label">Project coverage</span>
                @if ($canManageProjectFiles)
                    <strong>{{ $projectCount > 0 ? number_format(($fileCount / $projectCount), 1) : '0.0' }}</strong>
                    <p>Average files attached to each project record.</p>
                @elseif ($canViewProjectFiles)
                    <strong>{{ number_format($sharedFileCount) }}</strong>
                    <p>Shared files across projects.</p>
                @else
                    <strong>Limited</strong>
                    <p>File metrics are hidden for this account.</p>
                @endif
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
            @if ($canViewProjectFiles)
                <strong class="kpi-value">{{ number_format($fileCount) }}</strong>
                <span class="kpi-meta">{{ $canManageProjectFiles ? 'Private and shared files.' : 'Shared files only.' }}</span>
            @else
                <strong class="kpi-value">—</strong>
                <span class="kpi-meta">File access is limited for this account.</span>
            @endif
        </article>
        <article class="panel kpi-card kpi-card--traffic">
            <span class="metric-label">Shared files</span>
            @if ($canViewProjectFiles)
                <strong class="kpi-value">{{ number_format($sharedFileCount) }}</strong>
                <span class="kpi-meta">Links ready for client or team handoff.</span>
            @else
                <strong class="kpi-value">—</strong>
                <span class="kpi-meta">File access is limited for this account.</span>
            @endif
        </article>
    </section>

    <section id="project-file-upload" class="panel panel-padded project-upload-panel" style="margin-top: 24px;">
        @if ($canManageProjectFiles)
            <div class="panel-head panel-head--row">
                <div>
                    <span class="eyebrow">External project files</span>
                    <h2>Upload a file for the project team</h2>
                    <p>Attach briefs, references, approvals, or delivery files to a project. Files stay private until you create a share link for the project handoff.</p>
                </div>
                <span class="admin-pill">Private by default</span>
            </div>

            @if ($projects->isNotEmpty())
                <form class="project-index-upload-form" method="POST" action="{{ route('admin.projects.files.external.store') }}" enctype="multipart/form-data" data-project-file-upload>
                    @csrf
                    <div class="field">
                        <label for="external-project-id">Project</label>
                        <select id="external-project-id" name="project_id" required>
                            <option value="">Choose a project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" @selected((string) old('project_id') === (string) $project->id)>
                                    {{ $project->name }} · {{ $project->project_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="external-project-file">File</label>
                        <input id="external-project-file" type="file" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.ppt,.pptx,.txt,.rtf,.jpg,.jpeg,.png,.webp,.zip">
                    </div>
                    <div class="field-full">
                        <label for="external-project-description">Description <span class="field-optional">Optional</span></label>
                        <textarea id="external-project-description" name="description" rows="3" maxlength="500" placeholder="What should the project team know about this file?">{{ old('description') }}</textarea>
                    </div>
                    @error('project_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                    @error('file')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                    @error('description')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                    <div class="project-index-upload-form__actions">
                        <p class="form-help">Maximum 50 MB. PDF, Office files, images, text, and ZIP files are supported.</p>
                        <button class="button" type="submit" data-project-file-submit>Upload to Project</button>
                    </div>
                    <p class="form-help" data-project-file-status role="status" aria-live="polite"></p>
                </form>
            @elseif ($canViewProjectFiles && ! $canManageProjectFiles)
                <div class="project-upload-empty">
                    <strong>Shared project files</strong>
                    <p>This account can view shared files only. Upload and sharing controls stay with full admins.</p>
                </div>
            @else
                <div class="project-upload-empty">
                    <strong>Create a project workspace before uploading files.</strong>
                    <p>Create a project workspace from Project Management or a staff contract before uploading files.</p>
                    <a class="ghost-button" href="{{ route('admin.project-management.projects.create') }}">Create Project</a>
                </div>
            @endif
        @else
            <div class="project-upload-empty">
                <strong>Project file access is limited for this account.</strong>
                <p>You can review project records, but upload, download, preview, delete, and secure-share actions require the Project files permission.</p>
            </div>
        @endif
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

            @if (!$canViewProjectFiles)
                <div class="project-upload-empty">
                    <strong>File activity is restricted.</strong>
                    <p>A full admin can grant this account the Project files permission without granting broader admin access.</p>
                </div>
            @elseif ($fileLeaders->isNotEmpty())
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
                <p>Projects can be created from staff agreements or project management, and keep their files independent from contracts.</p>
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
                                @if ($canViewProjectFiles)
                                    <strong>{{ number_format($project->files_count) }}</strong>
                                    <span>{{ number_format($project->shared_files_count) }} shared</span>
                                @else
                                    <strong>Restricted</strong>
                                    <span>File access limited</span>
                                @endif
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
                                <span>Create a project workspace from Project Management or a staff contract, then upload external files above.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (() => {
            const form = document.querySelector('[data-project-file-upload]');

            if (!form) {
                return;
            }

            const submit = form.querySelector('[data-project-file-submit]');
            const status = form.querySelector('[data-project-file-status]');
            const defaultLabel = submit?.textContent || 'Upload to Project';

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                if (!submit) {
                    return;
                }

                submit.disabled = true;
                submit.textContent = 'Uploading…';
                if (status) {
                    status.textContent = 'Uploading file…';
                    status.classList.remove('form-error');
                }

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        const validationMessage = Object.values(payload.errors || {})
                            .flat()
                            .find((message) => typeof message === 'string');

                        throw new Error(validationMessage || payload.message || 'The file could not be uploaded.');
                    }

                    form.reset();
                    if (status) {
                        status.textContent = payload.message || 'File uploaded successfully.';
                    }
                } catch (error) {
                    if (status) {
                        status.textContent = error.message || 'The file could not be uploaded.';
                        status.classList.add('form-error');
                    }
                } finally {
                    submit.disabled = false;
                    submit.textContent = defaultLabel;
                }
            });
        })();
    </script>
@endpush
