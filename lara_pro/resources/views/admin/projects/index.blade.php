@extends('admin.layouts.app')

@section('title', 'Projects & Files | Admin')

@section('content')
    @php
        $previewableMimes = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
    @endphp

    <div class="tt-projects-page">
        <header class="tt-projects-page-head">
            <div>
                <span class="tt-projects-heading-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 7.5h6l2 2h8v10H4z"/><path d="M4 7.5V5h6l2 2.5"/></svg></span>
                <span class="eyebrow">Project files</span>
                <h1>Project handoffs</h1>
            </div>
            <div class="tt-projects-page-actions">
                @if ($canManageProjectFiles)
                    <a class="button" href="#project-file-upload"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 16V4"/><path d="m7 9 5-5 5 5"/><path d="M5 14v5h14v-5"/></svg>Upload file</a>
                @endif
                @if ($canViewProjectFiles && $files->isNotEmpty())
                    <a class="ghost-button" href="#project-file-manager"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v14H4z"/><path d="M8 9h8M8 13h8M8 17h5"/></svg>Manage files</a>
                @endif
                @if ($canManageProjectFiles)
                    <a class="ghost-button" href="{{ route('admin.project-management.dashboard') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v14H4z"/><path d="M8 9h8M8 13h5"/></svg>Project management</a>
                @endif
            </div>
        </header>

        <section class="tt-projects-stat-strip" aria-label="Project file summary">
            <div class="tt-projects-stat"><span class="tt-projects-stat-icon tt-projects-stat-icon--gold" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 7h16v13H4z"/><path d="M8 7V4h8v3"/><path d="M8 12h8"/></svg></span><span class="metric-label">Projects</span><strong>{{ number_format($projectCount) }}</strong></div>
            <div class="tt-projects-stat"><span class="tt-projects-stat-icon tt-projects-stat-icon--green" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 12.5 9.5 17 19 7.5"/><circle cx="12" cy="12" r="9"/></svg></span><span class="metric-label">Active</span><strong>{{ number_format($activeCount) }}</strong></div>
            <div class="tt-projects-stat"><span class="tt-projects-stat-icon tt-projects-stat-icon--orange" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M6 3h9l3 3v15H6z"/><path d="M15 3v4h4M9 12h6M9 16h6"/></svg></span><span class="metric-label">Files</span><strong>{{ $canViewProjectFiles ? number_format($fileCount) : '—' }}</strong></div>
            <div class="tt-projects-stat"><span class="tt-projects-stat-icon tt-projects-stat-icon--slate" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M6 10V8a6 6 0 0 1 12 0v2"/><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M12 14v2"/></svg></span><span class="metric-label">Secure handoffs</span><strong>{{ $canViewProjectFiles ? number_format($sharedFileCount) : '—' }}</strong></div>
        </section>

        <div class="tt-projects-workspace">
            <section id="project-file-upload" class="tt-projects-card tt-projects-upload-card">
                <div class="tt-projects-card-head">
                    <div>
                        <span class="tt-projects-section-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 16V4"/><path d="m7 9 5-5 5 5"/><path d="M5 14v5h14v-5"/></svg></span>
                        <span class="eyebrow">External project files</span>
                        @if ($canManageProjectFiles)
                            <h2>Upload a file for the project team</h2>
                        @elseif ($canViewProjectFiles)
                            <h2>Shared project files</h2>
                        @else
                            <h2>Project file access</h2>
                        @endif
                    </div>
                    @if ($canManageProjectFiles)
                        <span class="tt-projects-card-note">Private until shared</span>
                    @endif
                </div>

                @if ($canManageProjectFiles && $projects->isNotEmpty())
                    <form class="project-index-upload-form tt-projects-upload-form" method="POST" action="{{ route('admin.projects.files.external.store') }}" enctype="multipart/form-data" data-project-file-upload>
                        @csrf
                        <div class="field">
                            <label for="external-project-id">Project</label>
                            <select id="external-project-id" name="project_id" required>
                                <option value="">Choose a project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}" @selected((string) old('project_id') === (string) $project->id)>
                                        {{ $project->name }} &middot; {{ $project->project_number }}
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
                            <textarea id="external-project-description" name="description" rows="3" maxlength="500" placeholder="Add a short note for the project team.">{{ old('description') }}</textarea>
                        </div>
                        @error('project_id')<p class="form-error">{{ $message }}</p>@enderror
                        @error('file')<p class="form-error">{{ $message }}</p>@enderror
                        @error('description')<p class="form-error">{{ $message }}</p>@enderror
                        <div class="project-index-upload-form__actions">
                            <span class="tt-projects-form-note">PDF, Office, image, text, and ZIP files up to 50 MB.</span>
                            <button class="button" type="submit" data-project-file-submit>Upload to project</button>
                        </div>
                        <div class="tt-project-upload-progress" data-project-file-progress hidden>
                            <div class="tt-project-upload-progress__head">
                                <span data-project-file-progress-label>Preparing upload</span>
                                <strong data-project-file-progress-value>0%</strong>
                            </div>
                            <div class="tt-project-upload-progress__track" role="progressbar" aria-label="File upload progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-project-file-progress-track>
                                <span data-project-file-progress-fill></span>
                            </div>
                            <span class="tt-project-upload-progress__detail" data-project-file-progress-detail>Getting the file ready…</span>
                        </div>
                        <p class="form-help" data-project-file-status role="status" aria-live="polite"></p>
                    </form>
                @elseif ($canViewProjectFiles && ! $canManageProjectFiles)
                    <div class="tt-projects-empty">
                        <strong>Shared files only</strong>
                        <span>Upload and sharing controls are reserved for full admins.</span>
                    </div>
                @elseif ($canManageProjectFiles)
                    <div class="tt-projects-empty tt-projects-empty--upload">
                        <span class="tt-projects-empty-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 16V4"/><path d="m7 9 5-5 5 5"/><path d="M5 14v5h14v-5"/></svg></span>
                        <div class="tt-projects-empty-copy">
                            <strong>Create a project before uploading.</strong>
                            <span>Start a workspace to share files securely.</span>
                        </div>
                        <a class="ghost-button" href="{{ route('admin.project-management.projects.create') }}">Create project</a>
                    </div>
                @else
                    <div class="tt-projects-empty">
                        <strong>Project file access is limited for this account.</strong>
                    </div>
                @endif
            </section>

            <aside class="tt-projects-side">
                <section class="tt-projects-card tt-projects-status-card">
                    <div class="tt-projects-card-head">
                    <div>
                        <span class="tt-projects-section-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 5h16v14H4z"/><path d="M8 9h8M8 13h5"/></svg></span>
                        <span class="eyebrow">Portfolio health</span>
                            <h2>Projects by status</h2>
                        </div>
                    </div>
                    <div class="project-status-chart-wrap">
                        <div class="project-status-chart" style="{{ $statusChartStyle }}" role="img" aria-label="Project status distribution">
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
                                        <span><strong>{{ $status['label'] }}</strong><small>{{ number_format($status['count']) }}</small></span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="tt-projects-muted">No project status data yet.</span>
                        @endif
                    </div>
                </section>

                <section class="tt-projects-card tt-projects-files-card">
                    <div class="tt-projects-card-head">
                    <div>
                        <span class="tt-projects-section-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 5h14v14H5z"/><path d="M8 15v-3M12 15V8M16 15v-6"/></svg></span>
                        <span class="eyebrow">Handoff activity</span>
                            <h2>Files by project</h2>
                        </div>
                    </div>
                    @if (!$canViewProjectFiles)
                        <div class="tt-projects-empty"><strong>File activity is restricted.</strong></div>
                    @elseif ($fileLeaders->isNotEmpty())
                        <div class="project-file-bars">
                            @foreach ($fileLeaders as $projectFileLeader)
                                <div class="project-file-bar">
                                    <div class="bar-header">
                                        <div><strong>{{ $projectFileLeader['name'] }}</strong><span class="bar-meta">{{ $projectFileLeader['project_number'] }}</span></div>
                                        <strong class="bar-count">{{ number_format($projectFileLeader['count']) }}</strong>
                                    </div>
                                    <div class="bar-track" role="progressbar" aria-label="Files for {{ $projectFileLeader['name'] }}" aria-valuenow="{{ $projectFileLeader['count'] }}" aria-valuemin="0" aria-valuemax="{{ max(1, $projects->max('files_count')) }}">
                                        <div class="bar-fill bar-fill--quote" style="width: {{ max(5, $projectFileLeader['width']) }}%;"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="tt-projects-empty tt-projects-empty--files">
                            <span class="tt-projects-empty-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg></span>
                            <strong>No project files yet</strong>
                            <span>Open a project to add the first file.</span>
                        </div>
                    @endif
                </section>
            </aside>
        </div>

        @if ($canViewProjectFiles)
        <section id="project-file-manager" class="tt-projects-register tt-projects-card tt-project-file-manager">
            <div class="tt-projects-card-head">
                <div>
                    <span class="tt-projects-section-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M6 3h9l3 3v15H6z"/><path d="M15 3v4h4M9 12h6M9 16h6"/></svg></span>
                    <span class="eyebrow">File management</span>
                    <h2>Uploaded project files</h2>
                </div>
                <span class="tt-projects-count">{{ number_format($files->count()) }} files</span>
            </div>

            @if ($files->isNotEmpty())
                <div class="project-file-manager-list">
                    @foreach ($files as $file)
                        <article class="project-file-manager-row">
                            <div class="project-file-card__icon" aria-hidden="true">{{ strtoupper(substr($file->fileKind(), 0, 1)) }}</div>
                            <div class="project-file-manager-row__body">
                                <div class="project-file-manager-row__heading">
                                    <div>
                                        <h3>{{ $file->original_name }}</h3>
                                        <p>
                                            @if ($file->project)
                                                <a href="{{ route('admin.projects.show', $file->project) }}">{{ $file->project->name }}</a> &middot;
                                            @else
                                                Project unavailable &middot;
                                            @endif
                                            {{ $file->fileKind() }} &middot; {{ $file->sizeLabel() }} &middot; Added {{ optional($file->created_at)->format('M d, Y') }}
                                        </p>
                                    </div>
                                    @if ($file->is_shared)
                                        <span class="file-share-badge">Shared</span>
                                    @else
                                        <span class="file-private-badge">Private</span>
                                    @endif
                                </div>

                                @if ($file->description)
                                    <p class="project-file-card__description">{{ $file->description }}</p>
                                @endif

                                <div class="project-file-card__actions">
                                    @if (in_array($file->mime_type, $previewableMimes, true))
                                        <a class="ghost-button" href="{{ route('admin.projects.files.preview', $file) }}" target="_blank" rel="noopener">Preview</a>
                                    @endif
                                    <a class="ghost-button" href="{{ route('admin.projects.files.download', $file) }}">Download</a>
                                    @if ($canManageProjectFiles)
                                        @include('admin.projects.partials.file-update-form', ['file' => $file, 'returnTo' => 'index'])
                                        <form method="POST" action="{{ route('admin.projects.files.share', $file) }}">
                                            @csrf
                                            <input type="hidden" name="return_to" value="index">
                                            <button class="{{ $file->is_shared ? 'ghost-button' : 'button' }}" type="submit">
                                                {{ $file->is_shared ? 'Revoke link' : 'Create share link' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.projects.files.destroy', $file) }}" onsubmit="return confirm('Remove this file from the project?');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="return_to" value="index">
                                            <button class="file-delete-button" type="submit">Remove</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="tt-projects-empty tt-projects-empty--files">
                    <span class="tt-projects-empty-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg></span>
                    <strong>No project files yet</strong>
                    @if ($canManageProjectFiles)
                        <span>Uploaded files will appear here with download, update, sharing, and remove controls.</span>
                    @else
                        <span>Shared files will appear here for download.</span>
                    @endif
                </div>
            @endif
        </section>
        @endif

        <section class="tt-projects-register tt-projects-card">
            <div class="tt-projects-card-head">
                <div>
                    <span class="tt-projects-section-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 5h16v14H4z"/><path d="M8 9h8M8 13h8M8 17h5"/></svg></span>
                    <span class="eyebrow">Project register</span>
                    <h2>Project directory</h2>
                </div>
                <span class="tt-projects-count">{{ number_format($projectCount) }} records</span>
            </div>
            <div class="table-wrap">
                <table class="quote-table project-table">
                    <thead><tr><th>Project</th><th>Client</th><th>Files</th><th>Contracts</th><th>Status</th><th>Open</th></tr></thead>
                    <tbody>
                        @forelse ($projects as $project)
                            <tr>
                                <td><strong>{{ $project->name }}</strong><span>{{ $project->project_number }}</span></td>
                                <td><strong>{{ $project->client_company ?: ($project->client_name ?: 'Client not provided') }}</strong>@if ($project->client_company && $project->client_name)<span>{{ $project->client_name }}</span>@endif</td>
                                <td>@if ($canViewProjectFiles)<strong>{{ number_format($project->files_count) }}</strong><span>{{ number_format($project->shared_files_count) }} shared</span>@else<strong>Restricted</strong><span>Access limited</span>@endif</td>
                                <td><strong>{{ number_format($project->staff_contracts_count) }}</strong><span>{{ number_format($project->staff_contracts_count) === '1' ? 'Staff agreement' : 'Staff agreements' }}</span></td>
                                <td><span class="project-status-badge">{{ \Illuminate\Support\Str::headline($project->status ?: 'uncategorised') }}</span></td>
                                <td><a class="table-link" href="{{ route('admin.projects.show', $project) }}">Open workspace</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><div class="tt-projects-table-empty"><strong>No project records yet.</strong><a class="table-link" href="{{ route('admin.project-management.projects.create') }}">Create a project</a></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
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
            const progress = form.querySelector('[data-project-file-progress]');
            const progressLabel = form.querySelector('[data-project-file-progress-label]');
            const progressValue = form.querySelector('[data-project-file-progress-value]');
            const progressTrack = form.querySelector('[data-project-file-progress-track]');
            const progressFill = form.querySelector('[data-project-file-progress-fill]');
            const progressDetail = form.querySelector('[data-project-file-progress-detail]');
            const defaultLabel = submit?.textContent || 'Upload to project';

            const setProgress = (value, label, detail) => {
                if (!progress) {
                    return;
                }

                const safeValue = Math.max(0, Math.min(100, Math.round(value)));
                progress.hidden = false;
                progress.classList.toggle('is-indeterminate', safeValue === 0);
                if (progressLabel) progressLabel.textContent = label;
                if (progressValue) progressValue.textContent = `${safeValue}%`;
                if (progressDetail) progressDetail.textContent = detail;
                if (progressTrack) progressTrack.setAttribute('aria-valuenow', String(safeValue));
                if (progressFill) progressFill.style.width = `${safeValue}%`;
            };

            const uploadFile = (formData, onProgress) => new Promise((resolve, reject) => {
                const request = new XMLHttpRequest();

                request.open('POST', form.action);
                request.setRequestHeader('Accept', 'application/json');
                request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                request.upload.addEventListener('progress', (event) => {
                    if (!event.lengthComputable) {
                        onProgress(0, 'Uploading file', 'Uploading securely…');
                        return;
                    }

                    const percent = (event.loaded / event.total) * 100;
                    onProgress(percent, 'Uploading file', `${Math.round(percent)}% uploaded`);
                });
                request.addEventListener('load', () => {
                    let payload = {};

                    try {
                        payload = JSON.parse(request.responseText || '{}');
                    } catch (error) {
                        payload = {};
                    }

                    if (request.status >= 200 && request.status < 300) {
                        resolve(payload);
                        return;
                    }

                    const validationMessage = Object.values(payload.errors || {}).flat().find((message) => typeof message === 'string');
                    reject(new Error(validationMessage || payload.message || 'The file could not be uploaded.'));
                });
                request.addEventListener('error', () => reject(new Error('The file could not be uploaded.')));
                request.addEventListener('abort', () => reject(new Error('The upload was cancelled.')));
                request.send(formData);
            });

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                if (!submit) {
                    return;
                }

                submit.disabled = true;
                submit.textContent = 'Uploading…';
                if (progress) progress.classList.remove('is-complete', 'is-error');
                setProgress(0, 'Preparing upload', 'Getting the file ready…');
                if (status) {
                    status.textContent = '';
                    status.classList.remove('form-error');
                }

                try {
                    const payload = await uploadFile(new FormData(form), setProgress);

                    form.reset();
                    if (progress) {
                        progress.classList.remove('is-indeterminate');
                        progress.classList.add('is-complete');
                    }
                    setProgress(100, 'Upload complete', 'File is now available in the project workspace.');
                    if (status) {
                        status.textContent = payload.message || 'File uploaded successfully.';
                    }
                    window.setTimeout(() => window.location.reload(), 900);
                } catch (error) {
                    if (progress) progress.classList.add('is-error');
                    if (progressLabel) progressLabel.textContent = 'Upload failed';
                    if (progressDetail) progressDetail.textContent = 'Check the file and try again.';
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
