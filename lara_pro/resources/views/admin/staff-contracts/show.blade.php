@extends('admin.layouts.app')

@section('title', $contract->contract_number.' | Staff Contract')

@section('content')
    <style>
        .staff-contract-preview-shell {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 24px;
            align-items: start;
        }

        .staff-contract-document-stage {
            padding: 24px;
            overflow-x: auto;
            background: #e9ecef;
        }

        .staff-contract-document-stage .staff-contract-document {
            max-width: 840px;
            margin: 0 auto;
        }

        .contract-status-large {
            display: inline-flex;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(184, 134, 11, 0.1);
            color: var(--accent-soft);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .contract-lock-notice {
            display: inline-flex;
            align-items: center;
            padding: 10px 14px;
            border: 1px solid rgba(47, 128, 84, 0.24);
            border-radius: 999px;
            background: rgba(47, 128, 84, 0.1);
            color: var(--success);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .signed-document-preview {
            overflow: hidden;
            margin-top: 18px;
            border: 1px solid rgba(25, 31, 39, 0.12);
            border-radius: 14px;
            background: #f5f6f7;
        }

        .signed-document-preview iframe {
            display: block;
            width: 100%;
            min-height: 760px;
            border: 0;
            background: #fff;
        }

        .signed-document-preview img {
            display: block;
            width: 100%;
            max-height: 760px;
            object-fit: contain;
            background: #fff;
        }

        @media (max-width: 1120px) {
            .staff-contract-preview-shell {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <style>
        @include('admin.staff-contracts.partials.document-styles')
    </style>

    <section class="panel hero-banner">
        <div>
            <span class="eyebrow">Staff contract preview</span>
            <h1>{{ $contract->staff_name }} is assigned to {{ $contract->project->name }}.</h1>
            <p>
                Review the source invoice, project reference, agreed price, terms, and signing section below. Export
                the same saved document as a PDF when it is ready to circulate.
            </p>
            <div class="hero-actions">
                <a class="button" href="{{ route('admin.staff-contracts.pdf', $contract) }}">Download Contract PDF</a>
                @if ($contract->hasSignedDocument())
                    <span class="contract-lock-notice">Locked after signed upload</span>
                @else
                    <a class="ghost-button" href="{{ route('admin.staff-contracts.edit', $contract) }}">Edit Contract</a>
                @endif
                <a class="ghost-button" href="{{ route('admin.staff-contracts.index') }}">Back to Register</a>
            </div>
        </div>
        <div class="hero-callout">
            <div class="callout-card">
                <span class="metric-label">Contract status</span>
                <strong>{{ str_replace('_', ' ', ucfirst($contract->status)) }}</strong>
                <p>{{ $contract->contract_number }}</p>
            </div>
            <div class="callout-card">
                <span class="metric-label">Agreed fee</span>
                <strong>{{ $contract->currency }} {{ number_format((float) $contract->agreed_fee, 2) }}</strong>
                <p>{{ $contract->payment_terms }}</p>
            </div>
        </div>
    </section>

    <div class="staff-contract-preview-shell" style="margin-top: 24px;">
        <section class="panel staff-contract-document-stage">
            @include('admin.staff-contracts.partials.document', [
                'contract' => $contract,
                'brand' => $brand,
            ])

            @if ($contract->hasSignedDocument())
                <section class="panel panel-padded" style="margin-top: 24px;">
                    <span class="eyebrow">Uploaded signed version</span>
                    <h2 class="panel-title">{{ $contract->signed_document_original_name }}</h2>
                    <p class="form-help" style="margin-top: 10px;">
                        This is the signed document attached to the contract. The contract is locked and can no longer be edited.
                    </p>
                    @if (str_starts_with((string) $contract->signed_document_mime, 'image/'))
                        <div class="signed-document-preview">
                            <img src="{{ route('admin.staff-contracts.signed-document.preview', $contract) }}" alt="Uploaded signed contract">
                        </div>
                    @elseif ($contract->signed_document_mime === 'application/pdf')
                        <div class="signed-document-preview">
                            <iframe src="{{ route('admin.staff-contracts.signed-document.preview', $contract) }}" title="Uploaded signed contract"></iframe>
                        </div>
                    @else
                        <div class="signed-document-preview" style="padding: 24px;">
                            <strong>Preview is unavailable for this file type.</strong>
                            <p class="form-help" style="margin-top: 8px;">Download the uploaded Word document to view the signed version.</p>
                        </div>
                    @endif
                    <a class="button" href="{{ route('admin.staff-contracts.signed-document', $contract) }}" style="margin-top: 14px;">Download signed version</a>
                </section>
            @endif
        </section>

        <aside class="sticky-stack">
            <section class="panel panel-padded">
                <span class="eyebrow">Project relationship</span>
                <h3 class="panel-title">{{ $contract->project->name }}</h3>
                <div class="meta-list" style="margin-top: 18px;">
                    <div class="meta-item">
                        <span>Project number</span>
                        <strong>{{ $contract->project->project_number }}</strong>
                    </div>
                    <div class="meta-item">
                        <span>Source invoice</span>
                        <strong>{{ $contract->invoice?->quote_number ?: 'Legacy contract' }}</strong>
                    </div>
                    <div class="meta-item">
                        <span>Client</span>
                        <strong>{{ $contract->project->client_company ?: ($contract->project->client_name ?: 'Not provided') }}</strong>
                    </div>
                    <div class="meta-item">
                        <span>Staff member</span>
                        <strong>{{ $contract->staff_name }}</strong>
                        <p>{{ $contract->staff_role }}</p>
                    </div>
                </div>
            </section>

            <section class="panel panel-padded">
                <span class="eyebrow">Signing progress</span>
                <h3 class="panel-title">{{ ucfirst(str_replace('_', ' ', $contract->status)) }}</h3>
                <ul class="stack-list" style="margin-top: 18px;">
                    <li>
                        <strong>{{ $contract->company_signatory_name ?: 'Company signatory pending' }}</strong>
                        <span>{{ $contract->company_signed_date ? 'Signed '.$contract->company_signed_date->format('M d, Y') : 'Company signature date pending' }}</span>
                    </li>
                    <li>
                        <strong>{{ $contract->staff_signatory_name ?: $contract->staff_name }}</strong>
                        <span>{{ $contract->staff_signed_date ? 'Signed '.$contract->staff_signed_date->format('M d, Y') : 'Staff signature date pending' }}</span>
                    </li>
                </ul>
            </section>

            <section class="panel panel-padded">
                <span class="eyebrow">Signed proof copy</span>
                @if ($contract->hasSignedDocument())
                    <h3 class="panel-title">{{ $contract->signed_document_original_name ?: 'Signed document' }}</h3>
                    <p class="form-help" style="margin-top: 10px;">
                        {{ $contract->signed_document_size ? number_format($contract->signed_document_size / 1024, 1).' KB' : 'Stored privately' }}
                        @if ($contract->signed_document_mime)
                            · {{ $contract->signed_document_mime }}
                        @endif
                    </p>
                    <a class="button" href="{{ route('admin.staff-contracts.signed-document', $contract) }}" style="margin-top: 14px;">Download signed copy</a>
                @else
                    <h3 class="panel-title">No signed copy uploaded</h3>
                    <p class="form-help" style="margin-top: 10px;">When the agreement is signed, upload the proof copy from Edit Contract so it remains attached to this project record.</p>
                    <a class="ghost-button" href="{{ route('admin.staff-contracts.edit', $contract) }}" style="margin-top: 14px;">Upload signed copy</a>
                @endif
            </section>
        </aside>
    </div>

    @if ($canManageProjectFiles)
        <section class="panel panel-padded" style="margin-top: 24px;">
            <div class="panel-head panel-head--row">
                <div>
                    <span class="eyebrow">Shared project workspace</span>
                    <h2>Files available to this project team</h2>
                    <p>These external files belong to {{ $contract->project->name }} and are visible from every staff agreement tied to the project.</p>
                </div>
                <a class="button" href="{{ route('admin.projects.show', $contract->project).'#project-file-upload' }}">Upload external file</a>
            </div>

            @if ($projectFiles->isNotEmpty())
                <div class="project-file-list" style="margin-top: 18px;">
                    @foreach ($projectFiles as $projectFile)
                        <article class="project-file-card">
                            <div class="project-file-card__icon" aria-hidden="true">{{ strtoupper(substr($projectFile->fileKind(), 0, 1)) }}</div>
                            <div class="project-file-card__body">
                                <div class="project-file-card__heading">
                                    <div>
                                        <h3>{{ $projectFile->original_name }}</h3>
                                        <p>{{ $projectFile->fileKind() }} · {{ $projectFile->sizeLabel() }} · Added {{ optional($projectFile->created_at)->format('M d, Y') }}</p>
                                    </div>
                                    <span class="{{ $projectFile->is_shared ? 'file-share-badge' : 'file-private-badge' }}">
                                        {{ $projectFile->is_shared ? 'Shared' : 'Private' }}
                                    </span>
                                </div>
                                @if ($projectFile->description)
                                    <p class="project-file-card__description">{{ $projectFile->description }}</p>
                                @endif
                                <div class="project-file-card__actions">
                                    <a class="ghost-button" href="{{ route('admin.projects.files.download', $projectFile) }}">Download</a>
                                    @if ($projectFile->is_shared)
                                        <a class="ghost-button" href="{{ route('project-files.share', $projectFile->share_token) }}" target="_blank" rel="noopener">Open share page</a>
                                    @else
                                        <span class="form-help">Open the project workspace to create a secure staff share link.</span>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="project-upload-empty" style="margin-top: 18px;">
                    <strong>No external project files yet.</strong>
                    <p>Upload a brief, reference, approval, or delivery file above. It will appear here for every staff agreement tied to this project.</p>
                </div>
            @endif
        </section>
    @endif
@endsection
