@extends('admin.layouts.app')

@section('title', $project->name.' | Project Workspace')

@section('content')
    @php
        $clientLabel = $project->client_company ?: ($project->client_name ?: 'Client not provided');
        $previewableMimes = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
    @endphp

    <section class="panel hero-banner project-detail-hero">
        <div>
            <span class="eyebrow">Project workspace · {{ $project->project_number }}</span>
            <h1>{{ $project->name }}</h1>
            <p>{{ $project->description ?: 'A shared project room for working files, signed documents, references, and final handoff materials.' }}</p>
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
                <span class="metric-label">File handoff</span>
                <strong>{{ number_format($sharedFileCount) }} shared</strong>
                <p>{{ number_format($files->count()) }} total project {{ \Illuminate\Support\Str::plural('file', $files->count()) }}</p>
            </div>
        </div>
    </section>

    <div class="project-detail-grid" style="margin-top: 24px;">
        <section class="panel panel-padded">
            <div class="panel-head panel-head--row">
                <div>
                    <span class="eyebrow">Project files</span>
                    <h2>Share the right file at the right moment</h2>
                    <p>Files remain private until you explicitly create a secure share link.</p>
                </div>
                <span class="admin-pill">{{ number_format($files->count()) }} files</span>
            </div>

            @if ($files->isNotEmpty())
                <div class="project-file-list">
                    @foreach ($files as $file)
                        <article class="project-file-card">
                            <div class="project-file-card__icon" aria-hidden="true">{{ strtoupper(substr($file->fileKind(), 0, 1)) }}</div>
                            <div class="project-file-card__body">
                                <div class="project-file-card__heading">
                                    <div>
                                        <h3>{{ $file->original_name }}</h3>
                                        <p>{{ $file->fileKind() }} · {{ $file->sizeLabel() }} · Added {{ optional($file->created_at)->format('M d, Y') }}</p>
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
                                </div>

                                @if ($file->is_shared)
                                    <div class="project-share-link" data-share-link-row>
                                        <label for="share-link-{{ $file->id }}">Secure share link</label>
                                        <div>
                                            <input id="share-link-{{ $file->id }}" type="text" readonly value="{{ route('project-files.share', $file->share_token) }}" data-share-url>
                                            <button class="ghost-button" type="button" data-copy-share>Copy</button>
                                        </div>
                                        <small>Anyone with this link can download this file. Revoke it when the handoff is complete.</small>
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
        </section>

        <aside class="sticky-stack project-detail-sidebar">
            <section class="panel panel-padded">
                <span class="eyebrow">Add to project</span>
                <h2 class="panel-title">Upload a file</h2>
                <p class="form-help">Private by default. Maximum 50 MB. PDF, Office files, images, text, and ZIP files are supported.</p>

                <form id="project-file-upload" class="project-file-upload-form" method="POST" action="{{ route('admin.projects.files.store', $project) }}" enctype="multipart/form-data">
                    @csrf
                    <label for="project-file">File</label>
                    <input id="project-file" type="file" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.ppt,.pptx,.txt,.rtf,.jpg,.jpeg,.png,.webp,.zip">
                    <label for="project-file-description">Description <span>(optional)</span></label>
                    <textarea id="project-file-description" name="description" rows="4" maxlength="500" placeholder="e.g. Approved homepage references for the design handoff.">{{ old('description') }}</textarea>
                    @error('file')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                    @error('description')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                    <button class="button" type="submit">Upload private file</button>
                </form>
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
@endsection

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
    </script>
@endpush
