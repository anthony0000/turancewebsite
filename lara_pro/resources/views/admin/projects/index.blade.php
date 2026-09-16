@extends('admin.layouts.app')

@section('title', 'File management | Admin')

@section('content')
    @php
        $previewableMimes = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
        $storageLabel = static function (int $bytes): string {
            if ($bytes < 1024) return number_format($bytes).' B';
            if ($bytes < 1048576) return number_format($bytes / 1024, 1).' KB';
            if ($bytes < 1073741824) return number_format($bytes / 1048576, 1).' MB';
            return number_format($bytes / 1073741824, 1).' GB';
        };
        $fileTone = static fn ($file): string => match ($file->fileKind()) {
            'PDF' => 'pdf', 'Image' => 'image', 'Document' => 'document',
            'Spreadsheet' => 'spreadsheet', default => 'file',
        };
    @endphp

    <div class="fm-page">
        <form class="fm-search" method="GET" action="{{ route('admin.projects.index') }}" role="search">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="m16 16 4 4"/></svg>
            <input type="search" name="q" value="{{ $filters['search'] }}" placeholder="Search documents, folders, projects or descriptions" aria-label="Search file library">
            @if ($filters['scope'] !== 'all')<input type="hidden" name="scope" value="{{ $filters['scope'] }}">@endif
            <kbd>/</kbd>
        </form>

        <header class="fm-heading">
            <div>
                <span class="eyebrow">{{ $canManageProjectFiles ? 'Company workspace' : 'Team access' }}</span>
                <h1>{{ $canManageProjectFiles ? 'File management' : 'Shared project files' }}</h1>
                <p>{{ $canManageProjectFiles ? 'Organise company documents and every project file in one secure library.' : 'Find the project documents that have been shared with you.' }}</p>
            </div>
            <div class="fm-heading__actions">
                @if ($canManageProjectFiles)
                    <button class="button" type="button" data-upload-open><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 16V4"/><path d="m7 9 5-5 5 5"/><path d="M5 14v5h14v-5"/></svg>Add document</button>
                    <a class="ghost-button" href="{{ route('admin.project-management.dashboard') }}">Project management</a>
                @endif
            </div>
        </header>

        <nav class="fm-tabs" aria-label="Document library views">
            <a class="{{ $filters['scope'] === 'all' && $filters['sharing'] === 'all' ? 'active' : '' }}" href="{{ route('admin.projects.index') }}">All files <span>{{ number_format($fileCount) }}</span></a>
            @if ($canManageProjectFiles)<a class="{{ $filters['scope'] === 'company' ? 'active' : '' }}" href="{{ route('admin.projects.index', ['scope' => 'company']) }}">Company documents <span>{{ number_format($companyFileCount) }}</span></a>@endif
            <a class="{{ $filters['scope'] === 'project' && $filters['sharing'] === 'all' ? 'active' : '' }}" href="{{ route('admin.projects.index', ['scope' => 'project']) }}">Project files <span>{{ number_format($projectFileCount) }}</span></a>
            <a class="{{ $filters['sharing'] === 'shared' ? 'active' : '' }}" href="{{ route('admin.projects.index', ['sharing' => 'shared']) }}">Shared <span>{{ number_format($sharedFileCount) }}</span></a>
        </nav>

        @if ($canManageProjectFiles)
            <section class="fm-summary" aria-label="Document library summary">
                <article><span class="fm-summary__icon fm-summary__icon--gold"><svg viewBox="0 0 24 24"><path d="M4 7h6l2 2h8v10H4z"/><path d="M4 7V5h6l2 2"/></svg></span><div><small>Total files</small><strong>{{ number_format($fileCount) }}</strong></div></article>
                <article><span class="fm-summary__icon fm-summary__icon--blue"><svg viewBox="0 0 24 24"><path d="M6 3h9l3 3v15H6z"/><path d="M15 3v4h4"/></svg></span><div><small>Company documents</small><strong>{{ number_format($companyFileCount) }}</strong></div></article>
                <article><span class="fm-summary__icon fm-summary__icon--green"><svg viewBox="0 0 24 24"><path d="M4 7h16v13H4z"/><path d="M8 7V4h8v3"/></svg></span><div><small>Project collections</small><strong>{{ number_format($projectCount) }}</strong></div></article>
                <article><span class="fm-summary__icon fm-summary__icon--slate"><svg viewBox="0 0 24 24"><path d="M6 10V8a6 6 0 0 1 12 0v2"/><rect x="4" y="10" width="16" height="10" rx="2"/></svg></span><div><small>Storage used</small><strong>{{ $storageLabel($storageUsed) }}</strong></div></article>
            </section>

            <section class="fm-section">
                <div class="fm-section__head"><div><span class="eyebrow">Collections</span><h2>Browse by location</h2></div><a href="#document-library">View all files</a></div>
                <div class="fm-folders">
                    <a class="fm-folder fm-folder--company" href="{{ route('admin.projects.index', ['scope' => 'company']) }}"><span class="fm-folder__icon"><svg viewBox="0 0 24 24"><path d="M3 8h18v12H3z"/><path d="M7 8V4h10v4M8 12h8M8 16h5"/></svg></span><span><strong>Company documents</strong><small>Policies, finance, HR and operations</small></span><b>{{ number_format($companyFileCount) }}</b></a>
                    @foreach ($projects->take(5) as $project)
                        <a class="fm-folder" href="{{ route('admin.projects.index', ['scope' => 'project', 'project' => $project->id]) }}"><span class="fm-folder__icon"><svg viewBox="0 0 24 24"><path d="M3 7h7l2 2h9v11H3z"/><path d="M3 7V5h7l2 2"/></svg></span><span><strong>{{ $project->name }}</strong><small>{{ $project->project_number }}</small></span><b>{{ number_format($project->files_count) }}</b></a>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($recentFiles->isNotEmpty())
            <section class="fm-section">
                <div class="fm-section__head"><div><span class="eyebrow">Quick access</span><h2>Recent files</h2></div></div>
                <div class="fm-recents">
                    @foreach ($recentFiles as $file)
                        <article class="fm-recent"><span class="fm-file-icon fm-file-icon--{{ $fileTone($file) }}">{{ strtoupper(substr($file->fileKind(), 0, 1)) }}</span><div><strong title="{{ $file->original_name }}">{{ $file->original_name }}</strong><small>{{ $file->locationLabel() }}</small></div><a href="{{ route('admin.projects.files.download', $file) }}" aria-label="Download {{ $file->original_name }}"><svg viewBox="0 0 24 24"><path d="M12 4v11"/><path d="m7 11 5 5 5-5"/><path d="M5 20h14"/></svg></a></article>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($canViewProjectFiles)
            <section id="document-library" class="fm-library">
                <div class="fm-section__head"><div><span class="eyebrow">Library</span><h2>{{ $canManageProjectFiles ? 'All documents' : 'Shared project files' }}</h2></div><span class="fm-result-count">{{ number_format($files->total()) }} result{{ $files->total() === 1 ? '' : 's' }}</span></div>
                <form class="fm-filters" method="GET" action="{{ route('admin.projects.index') }}">
                    <div class="fm-filters__top">
                        <div class="field fm-filter-search"><label for="library-search">Search documents</label><div class="fm-filter-search__control"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="m16 16 4 4"/></svg><input id="library-search" type="search" name="q" value="{{ $filters['search'] }}" placeholder="Search by file name or description"></div></div>
                        <div class="fm-filters__actions">
                            @if (collect($filters)->except(['sort'])->filter(fn ($value) => ! in_array($value, ['', 'all', 0], true))->isNotEmpty())<a class="fm-clear" href="{{ route('admin.projects.index') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>Reset</a>@endif
                            <button class="button fm-filter-submit" type="submit"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>Apply filters</button>
                        </div>
                    </div>
                    <div class="fm-filters__controls">
                        @if ($canManageProjectFiles)<div class="field"><label for="scope-filter">Location</label><select id="scope-filter" name="scope"><option value="all">Everywhere</option><option value="company" @selected($filters['scope'] === 'company')>Company documents</option><option value="project" @selected($filters['scope'] === 'project')>Project files</option></select></div>@endif
                        <div class="field"><label for="project-filter">Project</label><select id="project-filter" name="project"><option value="">All projects</option>@foreach($projects as $project)<option value="{{ $project->id }}" @selected($filters['projectFilter'] === $project->id)>{{ $project->name }}</option>@endforeach</select></div>
                        @if ($canManageProjectFiles)<div class="field"><label for="folder-filter">Company folder</label><select id="folder-filter" name="folder"><option value="">All folders</option>@foreach($availableFolders as $folder)<option value="{{ $folder }}" @selected($filters['folderFilter'] === $folder)>{{ $folder }}</option>@endforeach</select></div>@endif
                        <div class="field"><label for="type-filter">File type</label><select id="type-filter" name="type"><option value="all">All types</option><option value="pdf" @selected($filters['type'] === 'pdf')>PDF</option><option value="document" @selected($filters['type'] === 'document')>Documents</option><option value="spreadsheet" @selected($filters['type'] === 'spreadsheet')>Spreadsheets</option><option value="image" @selected($filters['type'] === 'image')>Images</option><option value="other" @selected($filters['type'] === 'other')>Other</option></select></div>
                        <div class="field"><label for="sharing-filter">Access</label><select id="sharing-filter" name="sharing"><option value="all">Any access</option><option value="private" @selected($filters['sharing'] === 'private')>Private</option><option value="shared" @selected($filters['sharing'] === 'shared')>Shared</option></select></div>
                        <div class="field"><label for="sort-filter">Sort by</label><select id="sort-filter" name="sort"><option value="newest">Newest first</option><option value="oldest" @selected($filters['sort'] === 'oldest')>Oldest first</option><option value="name" @selected($filters['sort'] === 'name')>File name</option><option value="size" @selected($filters['sort'] === 'size')>Largest first</option></select></div>
                    </div>
                </form>

                @if ($files->isNotEmpty())
                    <div class="fm-file-list"><div class="fm-file-row fm-file-row--head"><span>Name</span><span>Location</span><span>Owner</span><span>Updated</span><span>Access</span><span>Actions</span></div>
                        @foreach ($files as $file)
                            <article class="fm-file-row" data-project-file-item="{{ $file->id }}">
                                <div class="fm-file-name"><span class="fm-file-icon fm-file-icon--{{ $fileTone($file) }}">{{ strtoupper(substr($file->fileKind(), 0, 1)) }}</span><span><strong data-project-file-name>{{ $file->original_name }}</strong><small data-project-file-meta>{{ $file->fileKind() }} · {{ $file->sizeLabel() }}@if($file->folder) · {{ $file->folder }}@endif</small>@if($file->description)<em data-project-file-description>{{ $file->description }}</em>@endif</span></div>
                                <div class="fm-file-cell"><small>Location</small>@if ($file->project)<a href="{{ route('admin.projects.show', $file->project) }}">{{ $file->project->name }}</a><span>{{ $file->project->project_number }}</span>@else<strong>Company documents</strong><span>{{ $file->folder ?: 'General' }}</span>@endif</div>
                                <div class="fm-file-cell"><small>Owner</small><strong>{{ $file->uploader?->name ?: 'Administrator' }}</strong></div>
                                <div class="fm-file-cell"><small>Updated</small><strong>{{ optional($file->updated_at)->format('M d, Y') }}</strong><span>{{ optional($file->updated_at)->diffForHumans() }}</span></div>
                                <div class="fm-file-cell"><small>Access</small><span class="{{ $file->is_shared ? 'file-share-badge' : 'file-private-badge' }}">{{ $file->is_shared ? 'Shared' : 'Private' }}</span></div>
                                <div class="fm-file-actions">
                                    @if (in_array($file->mime_type, $previewableMimes, true))
                                        <a class="fm-icon-action" href="{{ route('admin.projects.files.preview', $file) }}" target="_blank" rel="noopener" title="Preview" aria-label="Preview {{ $file->original_name }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg></a>
                                    @endif
                                    <a class="fm-icon-action" href="{{ route('admin.projects.files.download', $file) }}" title="Download" aria-label="Download {{ $file->original_name }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4v11"/><path d="m7 11 5 5 5-5"/><path d="M5 20h14"/></svg></a>
                                    @if ($canManageProjectFiles)
                                        @include('admin.projects.partials.file-update-form', ['file' => $file, 'returnTo' => 'index', 'compact' => true])
                                        <details class="fm-more-menu">
                                            <summary title="More actions" aria-label="More actions for {{ $file->original_name }}"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="12" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/></svg></summary>
                                            <div class="fm-more-menu__panel">
                                                <form method="POST" action="{{ route('admin.projects.files.share', $file) }}">@csrf<input type="hidden" name="return_to" value="index"><button type="submit"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8.5 13.5 15.5 6.5"/><path d="M13 5h4v4"/><path d="M17 13v5H6V7h5"/></svg>{{ $file->is_shared ? 'Revoke link' : 'Create share link' }}</button></form>
                                                <form method="POST" action="{{ route('admin.projects.files.destroy', $file) }}" onsubmit="return confirm('Remove this file from the document library?');">@csrf @method('DELETE')<input type="hidden" name="return_to" value="index"><button class="is-danger" type="submit"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"/></svg>Remove document</button></form>
                                            </div>
                                        </details>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                    @if ($files->hasPages())<div class="fm-pagination">{{ $files->onEachSide(1)->links() }}</div>@endif
                @else
                    <div class="fm-empty"><span class="fm-folder__icon"><svg viewBox="0 0 24 24"><path d="M3 7h7l2 2h9v11H3z"/><path d="M8 13h8"/></svg></span><strong>No documents match this view</strong><p>{{ $filters['search'] ? 'Try a broader search or clear the active filters.' : 'Upload the first document to begin building the library.' }}</p>@if($canManageProjectFiles)<button class="button" type="button" data-upload-open>Add document</button>@endif</div>
                @endif
            </section>
        @else
            <section class="fm-empty"><strong>File access is limited for this account.</strong><p>Ask a project administrator to add you to a project team and share the documents you need.</p></section>
        @endif

        @if ($canManageProjectFiles)
            <div id="upload-document" class="fm-upload-modal" data-upload-modal hidden>
                <button class="fm-upload-modal__backdrop" type="button" data-upload-close aria-label="Close upload dialog"></button>
                <section class="fm-upload-dialog" role="dialog" aria-modal="true" aria-labelledby="upload-dialog-title">
                    <header class="fm-upload-dialog__head">
                        <div><span class="eyebrow">Add to the library</span><h2 id="upload-dialog-title">Add document</h2><p>Files remain private until you create a share link.</p></div>
                        <button class="fm-upload-dialog__close" type="button" data-upload-close aria-label="Close"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg></button>
                    </header>
                    <form method="POST" action="{{ route('admin.projects.files.external.store') }}" enctype="multipart/form-data" data-project-file-upload>
                        @csrf
                        <div class="fm-upload-primary">
                            <div class="field"><label for="document-scope">Save to</label><select id="document-scope" name="document_scope" data-document-scope required><option value="company" @selected(old('document_scope') === 'company' || $projects->isEmpty())>Company documents</option><option value="project" @selected(old('document_scope', $projects->isEmpty() ? 'company' : 'project') === 'project')>Project files</option></select></div>
                            <div class="field" data-project-select><label for="external-project-id">Project</label><select id="external-project-id" name="project_id"><option value="">Choose a project</option>@foreach ($projects as $project)<option value="{{ $project->id }}" @selected((string) old('project_id') === (string) $project->id)>{{ $project->name }} · {{ $project->project_number }}</option>@endforeach</select></div>
                        </div>
                        <div class="field fm-file-picker"><label for="external-project-file">Choose document</label><input id="external-project-file" type="file" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.ppt,.pptx,.txt,.rtf,.jpg,.jpeg,.png,.webp,.zip"><small>PDF, Office, image, text or ZIP · maximum 50 MB</small></div>
                        <details class="fm-upload-optional">
                            <summary><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>Add folder and description <span>Optional</span></summary>
                            <div class="fm-upload-optional__fields">
                                <div class="field"><label for="document-folder">Folder</label><input id="document-folder" type="text" name="folder" maxlength="100" value="{{ old('folder') }}" placeholder="e.g. Legal or Deliverables"></div>
                                <div class="field"><label for="external-project-description">Description</label><textarea id="external-project-description" name="description" rows="3" maxlength="500" placeholder="Add a short note about this document">{{ old('description') }}</textarea></div>
                            </div>
                        </details>
                        @error('document_scope')<p class="form-error">{{ $message }}</p>@enderror @error('project_id')<p class="form-error">{{ $message }}</p>@enderror @error('folder')<p class="form-error">{{ $message }}</p>@enderror @error('file')<p class="form-error">{{ $message }}</p>@enderror
                        <div class="tt-project-upload-progress" data-project-file-progress hidden><div class="tt-project-upload-progress__head"><span data-project-file-progress-label>Preparing upload</span><strong data-project-file-progress-value>0%</strong></div><div class="tt-project-upload-progress__track" role="progressbar" aria-label="File upload progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-project-file-progress-track><span data-project-file-progress-fill></span></div><span class="tt-project-upload-progress__detail" data-project-file-progress-detail>Getting the file ready…</span></div>
                        <p class="form-help" data-project-file-status role="status" aria-live="polite"></p>
                        <footer class="fm-upload-dialog__actions"><button class="ghost-button" type="button" data-upload-close>Cancel</button><button class="button" type="submit" data-project-file-submit>Upload document</button></footer>
                    </form>
                </section>
            </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
    .fm-page{--fm-border:#e8e4dc;--fm-muted:#77736d;--fm-ink:#1d1d1b;display:grid;gap:28px;max-width:1500px;margin:0 auto;padding-bottom:40px}.fm-page svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round}.fm-search{display:flex;align-items:center;gap:12px;min-height:48px;padding:0 15px;border:1px solid #ebe8e2;border-radius:12px;background:#f7f6f3;color:#777}.fm-search input{flex:1;border:0!important;background:transparent!important;box-shadow:none!important;padding:0!important}.fm-search kbd{display:grid;place-items:center;min-width:24px;height:24px;border:1px solid #dedad2;border-radius:6px;background:#fff;color:#777;font:600 12px/1 inherit}.fm-heading{display:flex;align-items:flex-end;justify-content:space-between;gap:20px}.fm-heading h1{margin:5px 0 7px;font-size:clamp(30px,4vw,46px);letter-spacing:-.04em}.fm-heading p{margin:0;color:var(--fm-muted);font-size:15px}.fm-heading__actions{display:flex;flex-wrap:wrap;gap:10px}.fm-heading__actions .button,.fm-heading__actions .ghost-button{min-height:44px}.fm-heading__actions svg{width:17px}.fm-tabs{display:flex;gap:26px;border-bottom:1px solid var(--fm-border);overflow-x:auto}.fm-tabs a{position:relative;display:flex;align-items:center;gap:7px;min-height:44px;color:#6f6b65;font-size:13px;font-weight:700;text-decoration:none;white-space:nowrap}.fm-tabs a.active{color:#9b7008}.fm-tabs a.active:after{position:absolute;right:0;bottom:-1px;left:0;height:2px;background:#b8860b;content:""}.fm-tabs span{padding:2px 7px;border-radius:99px;background:#f0ede7;font-size:11px}.fm-summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.fm-summary article{display:flex;align-items:center;gap:13px;padding:18px;border:1px solid var(--fm-border);border-radius:14px;background:#fff}.fm-summary__icon{display:grid;width:42px;height:42px;place-items:center;border-radius:12px;color:#916806;background:#fff4d8}.fm-summary__icon--blue{color:#3564aa;background:#eaf1ff}.fm-summary__icon--green{color:#28724b;background:#e8f5ed}.fm-summary__icon--slate{color:#535b66;background:#edf0f3}.fm-summary small,.fm-summary strong{display:block}.fm-summary small{color:var(--fm-muted);font-size:11px;font-weight:700}.fm-summary strong{margin-top:3px;font-size:20px}.fm-section{display:grid;gap:14px}.fm-section__head{display:flex;align-items:flex-end;justify-content:space-between;gap:15px}.fm-section__head h2{margin:4px 0 0;font-size:20px}.fm-section__head>a{color:#926a0a;font-size:12px;font-weight:800;text-decoration:none}.fm-folders{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}.fm-folder{display:grid;grid-template-columns:auto minmax(0,1fr) auto;align-items:center;gap:13px;min-height:82px;padding:14px 16px;border:1px solid var(--fm-border);border-radius:12px;background:#fff;color:var(--fm-ink);text-decoration:none;transition:.18s ease}.fm-folder:hover{border-color:#d2b66f;box-shadow:0 9px 24px rgba(55,43,18,.07);transform:translateY(-1px)}.fm-folder--company{background:#fffaf0}.fm-folder__icon{display:grid;width:42px;height:42px;place-items:center;border-radius:10px;background:#f5c14c;color:#6f4c00}.fm-folder__icon svg{width:23px;height:23px}.fm-folder strong,.fm-folder small{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.fm-folder strong{font-size:13px}.fm-folder small{margin-top:4px;color:var(--fm-muted);font-size:10px}.fm-folder b{color:#8a8379;font-size:12px}.fm-recents{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}.fm-recent{display:grid;grid-template-columns:auto minmax(0,1fr) auto;align-items:center;gap:12px;padding:13px;border:1px solid var(--fm-border);border-radius:12px;background:#f8f7f4}.fm-recent strong,.fm-recent small{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.fm-recent strong{font-size:12px}.fm-recent small{margin-top:4px;color:var(--fm-muted);font-size:10px}.fm-recent>a{display:grid;width:34px;height:34px;place-items:center;border-radius:9px;color:#746d61}.fm-file-icon{display:grid;flex:0 0 auto;width:42px;height:48px;place-items:center;border-radius:8px;background:#e7effc;color:#3568ae;font-size:12px;font-weight:900}.fm-file-icon--pdf{background:#ffeae6;color:#b84b3b}.fm-file-icon--spreadsheet{background:#e4f3e9;color:#25804d}.fm-file-icon--image{background:#fff0cc;color:#9a6900}.fm-file-icon--file{background:#ececf0;color:#555c68}.fm-upload-card{display:grid;grid-template-columns:minmax(250px,.65fr) minmax(0,1.35fr);gap:28px;padding:24px;border:1px solid #e4d6b5;border-radius:16px;background:linear-gradient(135deg,#fffaf0,#fff)}.fm-upload-card__intro{padding:5px 10px}.fm-upload-card__mark{display:grid;width:45px;height:45px;place-items:center;margin-bottom:18px;border-radius:12px;background:#b8860b;color:#fff}.fm-upload-card h2{margin:5px 0 8px;font-size:24px}.fm-upload-card p{margin:0;color:var(--fm-muted);font-size:13px;line-height:1.7}.fm-upload-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px}.fm-upload-card .field-full{grid-column:1/-1}.fm-upload-card label,.fm-filters label{display:block;margin-bottom:6px;color:#5b554c;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.08em}.fm-upload-card input,.fm-upload-card select,.fm-upload-card textarea,.fm-filters input,.fm-filters select{width:100%;border:1px solid #ddd8cd!important;border-radius:9px!important;background:#fff!important}.fm-upload-actions{display:flex;align-items:center;justify-content:space-between;gap:15px;margin-top:15px}.fm-upload-actions span{color:var(--fm-muted);font-size:10px}.fm-library{display:grid;gap:17px;padding:22px;border:1px solid var(--fm-border);border-radius:16px;background:#fff}.fm-result-count{padding:5px 9px;border-radius:99px;background:#f3f0ea;color:#69635a;font-size:11px;font-weight:800}.fm-filters{display:grid;grid-template-columns:minmax(180px,1.4fr) repeat(4,minmax(115px,.7fr)) auto auto;align-items:end;gap:9px;padding:13px;border-radius:12px;background:#f7f6f3}.fm-filters .ghost-button{min-height:42px}.fm-clear{align-self:center;color:#8e6709;font-size:11px;font-weight:800}.fm-file-list{display:grid}.fm-file-row{display:grid;grid-template-columns:minmax(240px,2.1fr) minmax(150px,1.1fr) minmax(115px,.8fr) minmax(120px,.8fr) minmax(80px,.55fr) minmax(210px,1.3fr);align-items:center;gap:14px;padding:15px 8px;border-bottom:1px solid #efede8}.fm-file-row--head{padding:8px;color:#817b72;font-size:10px;font-weight:800;letter-spacing:.07em;text-transform:uppercase}.fm-file-name{display:flex;align-items:center;gap:12px;min-width:0}.fm-file-name>span:last-child{min-width:0}.fm-file-name strong,.fm-file-name small,.fm-file-name em{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.fm-file-name strong{font-size:12px}.fm-file-name small{margin-top:4px;color:var(--fm-muted);font-size:10px}.fm-file-name em{max-width:310px;margin-top:3px;color:#918a80;font-size:10px;font-style:normal}.fm-file-cell{min-width:0}.fm-file-cell>small{display:none}.fm-file-cell strong,.fm-file-cell span,.fm-file-cell a{display:block;overflow:hidden;color:#383530;font-size:11px;font-weight:700;text-overflow:ellipsis;white-space:nowrap}.fm-file-cell span{margin-top:3px;color:#8a847a;font-size:9px;font-weight:500}.fm-file-cell a{color:#8b6509;text-decoration:none}.fm-file-actions{display:flex;flex-wrap:wrap;align-items:center;gap:7px}.fm-file-actions>a,.fm-file-actions button,.fm-file-actions summary{padding:0;border:0;background:none;color:#8b6509;font:800 10px/1.4 inherit;cursor:pointer;text-decoration:none}.fm-file-actions .is-danger{color:#b34a3c}.fm-file-actions details{position:relative}.fm-file-actions .project-file-edit__form{right:0;left:auto;z-index:12;width:min(340px,80vw)}.fm-empty{display:grid;justify-items:center;gap:9px;padding:45px 20px;border:1px dashed #d9d2c5;border-radius:14px;background:#faf9f6;text-align:center}.fm-empty strong{font-size:16px}.fm-empty p{max-width:460px;margin:0;color:var(--fm-muted);font-size:12px}.fm-pagination{padding-top:12px}.fm-pagination nav>div:first-child{display:none}.fm-pagination nav>div:last-child{display:flex;align-items:center;justify-content:space-between;gap:12px}.fm-pagination nav p{margin:0;color:var(--fm-muted);font-size:11px}.fm-pagination nav a,.fm-pagination nav span{font-size:11px}.fm-pagination nav>div:last-child>div:last-child{display:flex;border-radius:8px;overflow:hidden;box-shadow:0 0 0 1px #ddd8ce}.fm-pagination nav>div:last-child>div:last-child>*{display:grid;min-width:34px;height:34px;place-items:center;padding:0 8px;border-right:1px solid #ddd8ce;background:#fff;color:#766e62;text-decoration:none}.fm-pagination nav>div:last-child>div:last-child>span[aria-current=page] span{background:#b8860b;color:#fff}.fm-pagination svg{width:15px}
    .fm-tabs{overflow-y:hidden;scrollbar-width:none}.fm-tabs::-webkit-scrollbar{display:none}
    .fm-file-row{grid-template-columns:minmax(240px,2.2fr) minmax(150px,1.1fr) minmax(115px,.8fr) minmax(120px,.8fr) minmax(80px,.55fr) minmax(156px,.75fr)}.fm-file-actions{position:relative;justify-content:flex-end;flex-wrap:nowrap;gap:6px}.fm-icon-action,.fm-file-actions>.project-file-edit>summary,.fm-more-menu>summary{display:grid!important;width:34px;height:34px;min-height:34px!important;place-items:center;padding:0!important;border:1px solid #e2ddd4!important;border-radius:9px!important;background:#fff!important;color:#71695d!important;list-style:none;transition:.15s ease}.fm-icon-action:hover,.fm-file-actions>.project-file-edit>summary:hover,.fm-file-actions>.project-file-edit[open]>summary,.fm-more-menu>summary:hover,.fm-more-menu[open]>summary{border-color:#c9a44f!important;background:#fff9eb!important;color:#8b6509!important}.fm-icon-action svg,.fm-file-actions>.project-file-edit>summary svg,.fm-more-menu>summary svg{width:16px;height:16px}.fm-more-menu{position:relative}.fm-more-menu>summary::-webkit-details-marker{display:none}.fm-more-menu__panel{position:absolute;z-index:30;top:calc(100% + 7px);right:0;display:grid;width:190px;padding:6px;border:1px solid #e1ddd5;border-radius:11px;background:#fff;box-shadow:0 14px 34px rgba(31,27,20,.14)}.fm-more-menu__panel form{margin:0}.fm-more-menu__panel button{display:flex!important;width:100%;min-height:36px;align-items:center;gap:9px;padding:0 10px!important;border-radius:7px!important;color:#514b43!important;text-align:left}.fm-more-menu__panel button:hover{background:#f7f4ed!important;color:#8b6509!important}.fm-more-menu__panel button.is-danger{color:#b34a3c!important}.fm-more-menu__panel button.is-danger:hover{background:#fff0ed!important}.fm-more-menu__panel svg{width:15px;height:15px}.fm-file-actions>.project-file-edit{position:relative}.fm-file-actions>.project-file-edit .project-file-edit__form{position:absolute;z-index:31;top:calc(100% + 7px);right:0;left:auto;width:min(340px,80vw);margin-top:0;background:#fff}
    .fm-filters{grid-template-columns:1fr;align-items:stretch;gap:14px;padding:16px}.fm-filters__top{display:flex;align-items:flex-end;gap:14px}.fm-filter-search{flex:1;min-width:220px}.fm-filter-search__control{position:relative}.fm-filter-search__control svg{position:absolute;top:50%;left:13px;width:17px;height:17px;color:#817a70;transform:translateY(-50%);pointer-events:none}.fm-filter-search__control input{min-height:44px;padding-left:40px!important}.fm-filters__actions{display:flex;flex:0 0 auto;align-items:center;gap:8px}.fm-filter-submit{min-height:44px;padding:0 16px;border-radius:9px;box-shadow:none}.fm-filter-submit svg{width:16px;height:16px}.fm-clear{display:inline-flex;min-height:42px;align-items:center;gap:6px;padding:0 10px;border-radius:8px;color:#756c60;font-size:11px;font-weight:800;text-decoration:none}.fm-clear:hover{background:#ebe7df;color:#8e6709}.fm-clear svg{width:14px;height:14px}.fm-filters__controls{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:10px;padding-top:13px;border-top:1px solid #e8e4dc}.fm-filters__controls select{min-height:42px;padding-inline:11px}.fm-filters .field{min-width:0}
    body.fm-modal-open{overflow:hidden}.fm-upload-modal[hidden]{display:none}.fm-upload-modal{position:fixed;z-index:1000;inset:0;display:grid;place-items:center;padding:20px}.fm-upload-modal__backdrop{position:absolute;inset:0;width:100%;height:100%;border:0;background:rgba(22,20,17,.56);backdrop-filter:blur(3px);cursor:default}.fm-upload-dialog{position:relative;z-index:1;width:min(620px,100%);max-height:calc(100vh - 40px);overflow-y:auto;border:1px solid #e4dfd5;border-radius:18px;background:#fff;box-shadow:0 28px 80px rgba(20,18,14,.24)}.fm-upload-dialog__head{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;padding:22px 24px 18px;border-bottom:1px solid #ece8e0}.fm-upload-dialog__head h2{margin:4px 0 5px;font-size:24px}.fm-upload-dialog__head p{margin:0;color:var(--fm-muted);font-size:12px}.fm-upload-dialog__close{display:grid;flex:0 0 auto;width:34px;height:34px;place-items:center;padding:0;border:1px solid #e3ded5;border-radius:9px;background:#fff;color:#70695f;cursor:pointer}.fm-upload-dialog__close:hover{background:#f6f3ed;color:#2c2924}.fm-upload-dialog__close svg{width:16px;height:16px}.fm-upload-dialog form{display:grid;gap:16px;padding:20px 24px 24px}.fm-upload-dialog label{display:block;margin-bottom:6px;color:#5b554c;font-size:10px;font-weight:800;letter-spacing:.08em;text-transform:uppercase}.fm-upload-dialog input,.fm-upload-dialog select,.fm-upload-dialog textarea{width:100%;border:1px solid #ddd8cd;border-radius:9px;background:#fff;color:var(--fm-ink)}.fm-upload-dialog select,.fm-upload-dialog input{min-height:43px}.fm-upload-dialog select,.fm-upload-dialog input[type=text]{padding:0 11px}.fm-upload-dialog textarea{min-height:84px;padding:10px;resize:vertical}.fm-upload-primary{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.fm-file-picker{padding:14px;border:1px dashed #d6c79e;border-radius:11px;background:#fffcf5}.fm-file-picker input[type=file]{min-height:44px;padding:7px;background:#fff}.fm-file-picker small{display:block;margin-top:7px;color:#8a8175;font-size:10px}.fm-upload-optional{border:1px solid #e7e2da;border-radius:10px;background:#faf9f6}.fm-upload-optional>summary{display:flex;min-height:44px;align-items:center;gap:8px;padding:0 12px;color:#5f584e;font-size:11px;font-weight:800;list-style:none;cursor:pointer}.fm-upload-optional>summary::-webkit-details-marker{display:none}.fm-upload-optional>summary svg{width:15px;height:15px}.fm-upload-optional>summary span{margin-left:auto;color:#999087;font-size:9px;font-weight:700;letter-spacing:.07em;text-transform:uppercase}.fm-upload-optional[open]>summary{border-bottom:1px solid #e7e2da}.fm-upload-optional__fields{display:grid;gap:12px;padding:13px}.fm-upload-dialog__actions{display:flex;align-items:center;justify-content:flex-end;gap:9px;padding-top:3px}.fm-upload-dialog__actions .button,.fm-upload-dialog__actions .ghost-button{min-height:42px;border-radius:9px}.fm-upload-dialog .tt-project-upload-progress{margin-top:0}.fm-upload-dialog .form-help{margin:0}.fm-upload-dialog .form-help:empty{display:none}
    @media(max-width:1180px){.fm-summary{grid-template-columns:repeat(2,1fr)}.fm-folders,.fm-recents{grid-template-columns:repeat(2,1fr)}.fm-filters{grid-template-columns:repeat(3,1fr)}.fm-file-row{grid-template-columns:minmax(240px,1.7fr) minmax(140px,1fr) 100px minmax(190px,1.2fr)}.fm-file-row--head span:nth-child(3),.fm-file-row--head span:nth-child(4),.fm-file-row .fm-file-cell:nth-child(3),.fm-file-row .fm-file-cell:nth-child(4){display:none}}
    @media(max-width:760px){.fm-page{gap:22px}.fm-heading{align-items:flex-start;flex-direction:column}.fm-summary,.fm-folders,.fm-recents,.fm-upload-card,.fm-upload-fields{grid-template-columns:1fr}.fm-upload-card .field-full{grid-column:auto}.fm-filters{grid-template-columns:1fr 1fr}.fm-filter-search{grid-column:1/-1}.fm-file-row--head{display:none}.fm-file-row{grid-template-columns:1fr;gap:12px;padding:18px 4px}.fm-file-cell{padding-left:54px}.fm-file-cell>small{display:block;margin-bottom:3px;color:#999087;font-size:8px;font-weight:800;letter-spacing:.08em;text-transform:uppercase}.fm-file-row .fm-file-cell:nth-child(3),.fm-file-row .fm-file-cell:nth-child(4){display:block}.fm-file-actions{justify-content:flex-start;padding-left:54px}.fm-file-actions>.project-file-edit .project-file-edit__form{right:auto;left:-54px}.fm-more-menu__panel{right:auto;left:0}.fm-upload-actions{align-items:flex-start;flex-direction:column}.fm-pagination nav>div:last-child{align-items:flex-start;flex-direction:column}}
    @media(max-width:1180px){.fm-filters{grid-template-columns:1fr}.fm-filters__controls{grid-template-columns:repeat(3,minmax(0,1fr))}}
    @media(max-width:760px){.fm-filters{grid-template-columns:1fr}.fm-filters__top{align-items:stretch;flex-direction:column}.fm-filter-search{min-width:0}.fm-filters__actions{justify-content:flex-end}.fm-filters__controls{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:520px){.fm-filters__controls{grid-template-columns:1fr}.fm-filters__actions{align-items:stretch;flex-direction:column-reverse}.fm-filter-submit,.fm-clear{width:100%;justify-content:center}}
    @media(max-width:620px){.fm-upload-modal{padding:10px}.fm-upload-dialog{max-height:calc(100vh - 20px);border-radius:14px}.fm-upload-dialog__head{padding:18px}.fm-upload-dialog form{padding:17px 18px 19px}.fm-upload-primary{grid-template-columns:1fr}.fm-upload-dialog__actions{align-items:stretch;flex-direction:column-reverse}.fm-upload-dialog__actions .button,.fm-upload-dialog__actions .ghost-button{width:100%}}
</style>
@endpush

@push('scripts')
<script>
    (() => {
        const search = document.querySelector('.fm-search input');
        document.addEventListener('keydown', (event) => { if (event.key === '/' && !/input|textarea|select/i.test(document.activeElement?.tagName || '')) { event.preventDefault(); search?.focus(); } });
        document.addEventListener('click', (event) => {
            document.querySelectorAll('.fm-file-actions details[open]').forEach((menu) => {
                if (!menu.contains(event.target)) menu.removeAttribute('open');
            });
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') document.querySelectorAll('.fm-file-actions details[open]').forEach((menu) => menu.removeAttribute('open'));
        });
        const modal = document.querySelector('[data-upload-modal]');
        let modalTrigger = null;
        const openModal = (trigger) => {
            if (!modal) return;
            modalTrigger = trigger;
            modal.hidden = false;
            document.body.classList.add('fm-modal-open');
            window.requestAnimationFrame(() => modal.querySelector('[data-document-scope]')?.focus());
        };
        const closeModal = () => {
            if (!modal) return;
            modal.hidden = true;
            document.body.classList.remove('fm-modal-open');
            modalTrigger?.focus();
        };
        document.querySelectorAll('[data-upload-open]').forEach((trigger) => trigger.addEventListener('click', () => openModal(trigger)));
        modal?.querySelectorAll('[data-upload-close]').forEach((trigger) => trigger.addEventListener('click', closeModal));
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal && !modal.hidden) closeModal();
        });
        const form = document.querySelector('[data-project-file-upload]');
        if (!form) return;
        const scope = form.querySelector('[data-document-scope]');
        const projectField = form.querySelector('[data-project-select]');
        const projectSelect = projectField?.querySelector('select');
        const syncScope = () => { const isProject = scope?.value === 'project'; if (projectField) projectField.hidden = !isProject; if (projectSelect) projectSelect.required = isProject; };
        scope?.addEventListener('change', syncScope); syncScope();
        const submit = form.querySelector('[data-project-file-submit]');
        const status = form.querySelector('[data-project-file-status]');
        const progress = form.querySelector('[data-project-file-progress]');
        const progressLabel = form.querySelector('[data-project-file-progress-label]');
        const progressValue = form.querySelector('[data-project-file-progress-value]');
        const progressTrack = form.querySelector('[data-project-file-progress-track]');
        const progressFill = form.querySelector('[data-project-file-progress-fill]');
        const progressDetail = form.querySelector('[data-project-file-progress-detail]');
        const defaultLabel = submit?.textContent || 'Upload document';
        const setProgress = (value, label, detail) => { if (!progress) return; const safeValue = Math.max(0, Math.min(100, Math.round(value))); progress.hidden = false; progress.classList.toggle('is-indeterminate', safeValue === 0); if (progressLabel) progressLabel.textContent = label; if (progressValue) progressValue.textContent = `${safeValue}%`; if (progressDetail) progressDetail.textContent = detail; if (progressTrack) progressTrack.setAttribute('aria-valuenow', String(safeValue)); if (progressFill) progressFill.style.width = `${safeValue}%`; };
        const upload = (formData) => new Promise((resolve, reject) => { const request = new XMLHttpRequest(); request.open('POST', form.action); request.setRequestHeader('Accept', 'application/json'); request.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); request.upload.addEventListener('progress', (event) => setProgress(event.lengthComputable ? (event.loaded / event.total) * 100 : 0, 'Uploading document', event.lengthComputable ? `${Math.round((event.loaded / event.total) * 100)}% uploaded` : 'Uploading securely…')); request.addEventListener('load', () => { let payload = {}; try { payload = JSON.parse(request.responseText || '{}'); } catch (error) {} if (request.status >= 200 && request.status < 300) return resolve(payload); reject(new Error(Object.values(payload.errors || {}).flat()[0] || payload.message || 'The document could not be uploaded.')); }); request.addEventListener('error', () => reject(new Error('The document could not be uploaded.'))); request.send(formData); });
        form.addEventListener('submit', async (event) => { event.preventDefault(); if (!submit) return; submit.disabled = true; submit.textContent = 'Uploading…'; progress?.classList.remove('is-complete', 'is-error'); setProgress(0, 'Preparing upload', 'Getting the file ready…'); if (status) { status.textContent = ''; status.classList.remove('form-error'); } try { const payload = await upload(new FormData(form)); progress?.classList.add('is-complete'); setProgress(100, 'Upload complete', 'Document is now available in the library.'); if (status) status.textContent = payload.message || 'Document uploaded successfully.'; window.setTimeout(() => window.location.reload(), 700); } catch (error) { progress?.classList.add('is-error'); if (progressLabel) progressLabel.textContent = 'Upload failed'; if (progressDetail) progressDetail.textContent = 'Check the document and try again.'; if (status) { status.textContent = error.message; status.classList.add('form-error'); } } finally { submit.disabled = false; submit.textContent = defaultLabel; } });
    })();
</script>
@endpush
