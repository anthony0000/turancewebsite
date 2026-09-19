@extends('admin.layouts.app')

@section('title', $project->name.' | Project documents')

@section('content')
    @php
        $clientLabel = $project->client_company ?: ($project->client_name ?: 'Client not provided');
        $previewableMimes = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
    @endphp

    <section class="panel hero-banner project-detail-hero">
        <div>
            <span class="eyebrow">Project documents · {{ $project->project_number }}</span>
            <h1>{{ $project->name }}</h1>
            <p>{{ $project->description ?: 'A shared project room for working files, signed documents, and references.' }}</p>
            <div class="hero-actions">
                <a class="ghost-button" href="{{ route('admin.projects.index') }}">Back to Projects</a>
                @if ($contracts->isNotEmpty())
                    <a class="ghost-button" href="{{ route('admin.staff-contracts.show', $contracts->first()) }}">View Staff Agreement</a>
                @endif
            </div>
        </div>
        <div class="hero-callout">
            <div class="callout-card">
                <span class="metric-label">Project status</span>
                <strong>{{ \Illuminate\Support\Str::headline($project->status ?: 'Uncategorised') }}</strong>
                <p>{{ $clientLabel }}</p>
            </div>
            <div class="callout-card">
                <span class="metric-label">Document access</span>
                @if ($canViewProjectFiles)
                    <strong>{{ number_format($sharedFileCount) }} shared</strong>
                    <p>{{ number_format($files->count()) }} total project {{ \Illuminate\Support\Str::plural('file', $files->count()) }}</p>
                @else
                    <strong>Restricted</strong>
                    <p>Project file access is limited for this account.</p>
                @endif
            </div>
        </div>
    </section>

    <div class="project-detail-grid" style="margin-top: 24px;">
        <section class="panel panel-padded">
            @if ($canViewProjectFiles)
                <div class="panel-head panel-head--row">
                    <div>
                        <span class="eyebrow">Project files</span>
                        @if ($canManageProjectFiles)
                            <h2>Share the right file at the right moment</h2>
                            <p>Files remain private until you explicitly create a secure share link.</p>
                        @else
                            <h2>Shared project files</h2>
                            <p>Files shared with everyone in this workspace.</p>
                        @endif
                    </div>
                    <span class="admin-pill">{{ number_format($files->count()) }} files</span>
                </div>

                @if ($files->isNotEmpty())
                    <div class="project-file-list">
                        @foreach ($files as $file)
                            <article class="project-file-card" data-project-file-item="{{ $file->id }}">
                                <div class="project-file-card__icon" aria-hidden="true">{{ strtoupper(substr($file->fileKind(), 0, 1)) }}</div>
                                <div class="project-file-card__body">
                                    <div class="project-file-card__heading">
                                        <div>
                                            <h3 data-project-file-name>{{ $file->original_name }}</h3>
                                            <p><span data-project-file-meta>{{ $file->fileKind() }} · {{ $file->sizeLabel() }}</span> · Added {{ optional($file->created_at)->format('M d, Y') }}</p>
                                        </div>
                                        @if ($file->is_shared)
                                            <span class="file-access-badge file-access-badge--shared"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9.5 14.5 14.5 9.5"/><path d="m7.5 16.5-1.7 1.7a3 3 0 0 1-4.2-4.2l3.2-3.2A3 3 0 0 1 9 10.7"/><path d="M15 13.3a3 3 0 0 1 .2-4.1L18.4 6a3 3 0 0 1 4.2 4.2l-1.7 1.7"/></svg>Shared</span>
                                        @else
                                            <span class="file-access-badge file-access-badge--private"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>Private</span>
                                        @endif
                                    </div>

                                    @if ($file->description)
                                        <p class="project-file-card__description" data-project-file-description>{{ $file->description }}</p>
                                    @endif

                                    <div class="project-file-card__actions">
                                        @if (in_array($file->mime_type, $previewableMimes, true))
                                            <a class="ghost-button" href="{{ route('admin.projects.files.preview', $file) }}" data-file-preview data-file-name="{{ $file->original_name }}" data-download-url="{{ route('admin.projects.files.download', $file) }}">Preview</a>
                                        @endif
                                        <a class="ghost-button" href="{{ route('admin.projects.files.download', $file) }}">Download</a>
                                        @if ($canManageProjectFiles)
                                        @include('admin.projects.partials.file-update-form', ['file' => $file])
                                        <form method="POST" action="{{ route('admin.projects.files.share', $file) }}">
                                            @csrf
                                            <button class="{{ $file->is_shared ? 'ghost-button' : 'button' }}" type="submit">
                                                {{ $file->is_shared ? 'Revoke link' : 'Create share link' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.projects.files.destroy', $file) }}" onsubmit="return confirm('Remove this file from the project?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="file-delete-button" type="submit">Remove</button>
                                        </form>
                                        @endif
                                    </div>

                                    @if ($file->is_shared)
                                        <div class="project-share-link" data-share-link-row>
                                            <label for="share-link-{{ $file->id }}">Secure share link</label>
                                            <div>
                                                <input id="share-link-{{ $file->id }}" type="text" readonly value="{{ route('project-files.share', $file->share_token) }}" data-share-url>
                                                <button class="ghost-button" type="button" data-copy-share>Copy</button>
                                            </div>
                                            <small>Anyone with this link can download this file. Revoke it when sharing is complete.</small>
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="project-files-empty">
                        <span class="project-files-empty__icon" aria-hidden="true">↗</span>
                        <h3>Your first project file will appear here.</h3>
                        <p>Add a brief, reference file, signed document, or final delivery below. Nothing is shared until you choose to create a link.</p>
                    </div>
                @endif
            @else
                <div class="project-upload-empty">
                    <strong>Project file access is limited for this account.</strong>
                    <p>This project record remains available, but file upload, download, preview, delete, and secure-share actions require the Project files permission.</p>
                </div>
            @endif
        </section>

        <aside class="sticky-stack project-detail-sidebar">
            <section class="panel panel-padded">
                @if ($canManageProjectFiles)
                    <span class="eyebrow">Add to project</span>
                    <h2 class="panel-title">Upload a file</h2>
                    <p class="form-help">Private by default. Maximum 50 MB. PDF, Office files, images, text, and ZIP files are supported.</p>

                    <form id="project-file-upload" class="project-file-upload-form" method="POST" action="{{ route('admin.projects.files.store', $project) }}" enctype="multipart/form-data">
                        @csrf
                        <label for="project-file">File</label>
                        <input id="project-file" type="file" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.ppt,.pptx,.txt,.rtf,.jpg,.jpeg,.png,.webp,.zip">
                        <label for="project-file-description">Description <span>(optional)</span></label>
                        <textarea id="project-file-description" name="description" rows="4" maxlength="500" placeholder="e.g. Approved homepage references for the design team.">{{ old('description') }}</textarea>
                        @error('file')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        @error('description')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        <button class="button" type="submit">Upload private file</button>
                    </form>
                @elseif ($canViewProjectFiles)
                    <span class="eyebrow">Project files</span>
                    <h2 class="panel-title">Shared files only</h2>
                    <p class="form-help">Upload, delete, and sharing controls are available to full admins.</p>
                @else
                    <span class="eyebrow">Project files</span>
                    <h2 class="panel-title">File access is limited</h2>
                    <p class="form-help">A full admin can grant the Project files permission without granting access to unrelated admin areas.</p>
                @endif
            </section>

            <section class="panel panel-padded">
                <span class="eyebrow">Project details</span>
                <div class="meta-list" style="margin-top: 18px;">
                    <div class="meta-item">
                        <span>Client</span>
                        <strong>{{ $clientLabel }}</strong>
                        @if ($project->client_company && $project->client_name)
                            <p>{{ $project->client_name }}</p>
                        @endif
                    </div>
                    <div class="meta-item">
                        <span>Timeline</span>
                        <strong>
                            @if ($project->starts_on || $project->ends_on)
                                {{ optional($project->starts_on)->format('M d, Y') ?: 'Start pending' }}
                                → {{ optional($project->ends_on)->format('M d, Y') ?: 'End pending' }}
                            @else
                                Timeline not set
                            @endif
                        </strong>
                    </div>
                    <div class="meta-item">
                        <span>Linked agreements</span>
                        <strong>{{ number_format($contracts->count()) }}</strong>
                        <p>{{ $contracts->count() === 1 ? 'One staff contract' : 'Staff contracts' }} connected to this project.</p>
                    </div>
                </div>
            </section>
        </aside>
    </div>

    @if ($canManageProjectFiles)
        <div style="margin-top: 24px;">
            @include('admin.projects.partials.credentials')
        </div>
    @endif

    @if ($contracts->isNotEmpty())
        <section class="panel panel-padded" style="margin-top: 24px;">
            <div class="panel-head">
                <span class="eyebrow">Project agreements</span>
                <h2>Staff contracts connected to this project</h2>
                <p>Use the project file area for shared artifacts; contract records remain available in their dedicated workspace.</p>
            </div>
            <div class="table-wrap">
                <table class="quote-table">
                    <thead>
                        <tr>
                            <th>Contract</th>
                            <th>Staff member</th>
                            <th>Status</th>
                            <th>Invoice</th>
                            <th>Open</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contracts as $contract)
                            <tr>
                                <td><strong>{{ $contract->contract_number }}</strong><span>Updated {{ optional($contract->updated_at)->format('M d, Y') }}</span></td>
                                <td><strong>{{ $contract->staff_name }}</strong><span>{{ $contract->staff_role }}</span></td>
                                <td><span class="project-status-badge">{{ \Illuminate\Support\Str::headline($contract->status) }}</span></td>
                                <td><strong>{{ $contract->invoice?->quote_number ?: 'Legacy contract' }}</strong></td>
                                <td><a class="table-link" href="{{ route('admin.staff-contracts.show', $contract) }}">View contract</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
    @include('admin.projects.partials.file-preview-modal')
@endsection

@push('styles')
    <style>
        .project-credentials__head { align-items: flex-start; }
        .project-credentials__head-actions { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: 10px; }
        .project-credentials__layout { display: grid; grid-template-columns: minmax(0, 1.55fr) minmax(300px, .7fr); gap: 22px; align-items: start; }
        .credential-list { display: grid; gap: 12px; }
        .credential-card { display: grid; grid-template-columns: 42px minmax(0, 1fr); gap: 13px; padding: 15px; border: 1px solid var(--line-soft); border-radius: 9px; background: linear-gradient(135deg, var(--surface), var(--surface-soft)); }
        .credential-card__marker { display: grid; width: 42px; height: 42px; place-items: center; border-radius: 12px; background: var(--primary-soft); color: var(--primary-strong); font-size: 11px; font-weight: 800; }
        .credential-card__body { min-width: 0; }
        .credential-card__title { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
        .credential-card__title span:first-child { color: var(--muted); font-size: 9px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        .credential-card__title h3 { margin: 3px 0 0; color: var(--text); font-size: 15px; }
        .credential-card__details { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px 16px; margin: 14px 0 0; }
        .credential-card__details > div:last-child { grid-column: 1 / -1; }
        .credential-card__details dt { margin-bottom: 4px; color: var(--muted); font-size: 9px; font-weight: 800; letter-spacing: .07em; text-transform: uppercase; }
        .credential-card__details dd { min-width: 0; margin: 0; overflow-wrap: anywhere; color: var(--muted-strong); font-size: 12px; }
        .credential-card__details a { color: var(--primary-strong); }
        .credential-secret { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
        .credential-secret code { min-width: 130px; padding: 8px 10px; overflow-wrap: anywhere; border: 1px solid var(--line); border-radius: 6px; background: #f7f5f1; color: #3f454c; font-size: 11px; }
        .credential-secret .ghost-button { min-height: 32px; padding-inline: 10px; font-size: 10px; }
        .credential-card__notes { margin: 13px 0 0; padding: 10px 11px; border-left: 2px solid var(--line); background: rgba(255, 255, 255, .62); color: var(--muted); font-size: 11px; line-height: 1.55; white-space: pre-line; }
        .credential-card__actions { display: flex; align-items: flex-start; gap: 8px; margin-top: 12px; }
        .credential-card__actions form { margin: 0; }
        .credential-edit { position: relative; }
        .credential-edit > summary { display: inline-flex; min-height: 34px; align-items: center; padding: 0 11px; cursor: pointer; font-size: 11px; list-style: none; }
        .credential-edit > summary::-webkit-details-marker { display: none; }
        .credential-edit__form { display: grid; width: min(460px, calc(100vw - 70px)); gap: 9px; margin-top: 8px; padding: 14px; border: 1px solid var(--line-soft); border-radius: 9px; background: var(--surface); box-shadow: 0 10px 25px rgba(24, 29, 35, .08); }
        .credential-create { padding: 18px; border: 1px solid var(--line-soft); border-radius: 10px; background: var(--surface-soft); }
        .credential-create h3 { margin: 0; font-size: 17px; }
        .credential-create > p { margin: 7px 0 0; color: var(--muted); font-size: 11px; line-height: 1.55; }
        .credential-create__form, .credential-edit__form { margin-top: 16px; }
        .credential-create__form { display: grid; gap: 10px; }
        .credential-create__form label, .credential-edit__form label { display: grid; gap: 5px; color: var(--muted-strong); font-size: 10px; font-weight: 700; }
        .credential-create__form label span { color: var(--muted); font-weight: 500; }
        .credential-create__form input, .credential-create__form textarea, .credential-edit__form input, .credential-edit__form textarea { width: 100%; min-height: 40px; padding: 9px 10px; border: 1px solid var(--line); border-radius: 7px; background: var(--surface); color: var(--text); font: inherit; font-size: 12px; }
        .credential-create__form textarea, .credential-edit__form textarea { min-height: 76px; resize: vertical; }
        .credential-form-errors { padding: 10px 12px; border: 1px solid rgba(185, 74, 61, .22); border-radius: 7px; background: rgba(185, 74, 61, .06); color: var(--danger); font-size: 11px; }
        .credential-form-errors ul { margin: 5px 0 0; padding-left: 18px; }
        .credential-empty { min-height: 260px; }
        @media (max-width: 1000px) { .project-credentials__layout { grid-template-columns: 1fr; } }
        @media (max-width: 640px) { .project-credentials__head { display: block; } .project-credentials__head-actions { justify-content: flex-start; margin-top: 14px; } .credential-card { grid-template-columns: 1fr; } .credential-card__details { grid-template-columns: 1fr; } .credential-card__details > div:last-child { grid-column: auto; } }
    </style>
@endpush

@push('scripts')
    <script>
        document.querySelectorAll('[data-copy-share]').forEach((button) => {
            button.addEventListener('click', async () => {
                const input = button.closest('[data-share-link-row]')?.querySelector('[data-share-url]');

                if (!input) return;

                try {
                    await navigator.clipboard.writeText(input.value);
                } catch {
                    input.select();
                    document.execCommand('copy');
                }

                const originalLabel = button.textContent;
                button.textContent = 'Copied';
                window.setTimeout(() => { button.textContent = originalLabel; }, 1600);
            });
        });

        document.querySelectorAll('[data-credential-card]').forEach((card) => {
            const revealButton = card.querySelector('[data-credential-reveal]');
            const copyButton = card.querySelector('[data-credential-copy]');
            const secret = card.querySelector('[data-credential-secret]');
            const maskedValue = '••••••••••••';
            let revealedValue = null;

            revealButton?.addEventListener('click', async () => {
                if (revealedValue !== null) {
                    revealedValue = null;
                    secret.textContent = maskedValue;
                    revealButton.textContent = 'Reveal';
                    copyButton.hidden = true;
                    return;
                }

                revealButton.disabled = true;
                revealButton.textContent = 'Revealing…';

                try {
                    const response = await fetch(revealButton.dataset.url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                    });

                    if (!response.ok) throw new Error('Unable to reveal this credential.');

                    const payload = await response.json();
                    revealedValue = payload.secret;
                    secret.textContent = revealedValue;
                    revealButton.textContent = 'Hide';
                    copyButton.hidden = false;
                } catch (error) {
                    revealButton.textContent = 'Try again';
                } finally {
                    revealButton.disabled = false;
                }
            });

            copyButton?.addEventListener('click', async () => {
                if (revealedValue === null) return;

                try {
                    await navigator.clipboard.writeText(revealedValue);
                    copyButton.textContent = 'Copied';
                    window.setTimeout(() => { copyButton.textContent = 'Copy'; }, 1600);
                } catch {
                    copyButton.textContent = 'Copy failed';
                }
            });
        });
    </script>
@endpush
