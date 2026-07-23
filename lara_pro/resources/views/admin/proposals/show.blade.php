@extends('admin.layouts.app')

@section('title', $proposal->proposal_number.' | Proposal Preview')

@section('content')
    <style>
        @include('admin.proposals.partials.document-styles', ['proposal' => $proposal])

        .proposal-preview-shell {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 24px;
            align-items: start;
        }

        .proposal-document-stage {
            padding: 24px;
            overflow-x: auto;
            background: #e9ecef;
        }

        .proposal-document-stage .proposal-document {
            max-width: 840px;
            margin: 0 auto;
        }

        .proposal-share-box {
            display: grid;
            gap: 10px;
            margin-top: 18px;
        }

        .proposal-share-box input {
            width: 100%;
            min-height: 42px;
            padding: 0 12px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.86);
        }

        @media (max-width: 1120px) {
            .proposal-preview-shell {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <section class="panel hero-banner">
        <div>
            <span class="eyebrow">Proposal Preview</span>
            <h1>{{ $proposal->title }} is ready for review, sharing, and export.</h1>
            <p>
                Review the full client-ready proposal below. Export PDF or Word, open a print-ready page, or share the
                online link when the status moves from draft to sent.
            </p>
            <div class="hero-actions">
                <a class="button" href="{{ route('admin.proposals.pdf', $proposal) }}">PDF Export</a>
                <a class="button" href="{{ route('admin.proposals.word', $proposal) }}">Word Export</a>
                <a class="ghost-button" href="{{ route('admin.proposals.print', $proposal) }}">Print HTML</a>
                <a class="ghost-button" href="{{ route('admin.proposals.edit', $proposal) }}">Edit Proposal</a>
            </div>
        </div>

        <div class="hero-callout">
            <div class="callout-card">
                <span class="metric-label">Template</span>
                <strong>{{ $proposal->template?->name ?? 'Proposal Template' }}</strong>
                <p>{{ $proposal->template?->description ?? 'Premium proposal layout.' }}</p>
            </div>
            <div class="callout-card">
                <span class="metric-label">Investment</span>
                <strong>{{ $proposal->currency }} {{ number_format((float) $proposal->grand_total, 2) }}</strong>
                <p>{{ ucfirst($proposal->status) }} / {{ $proposal->sections->where('is_visible', true)->count() }} visible sections</p>
            </div>
        </div>
    </section>

    <div class="proposal-preview-shell">
        <section class="panel proposal-document-stage">
            @include('admin.proposals.partials.document', ['proposal' => $proposal])
        </section>

        <aside class="sticky-stack">
            <section class="panel panel-padded">
                <span class="eyebrow">Shareable Link</span>
                <h3 class="panel-title">Online proposal</h3>
                <p class="panel-copy">Use this URL when the proposal is ready for client viewing.</p>
                <div class="proposal-share-box">
                    <input type="text" value="{{ $shareUrl }}" readonly>
                    <a class="button" href="{{ $shareUrl }}" target="_blank" rel="noopener">Open Link</a>
                </div>
            </section>

            <section class="panel panel-padded">
                <span class="eyebrow">Proposal Status</span>
                <h3 class="panel-title">{{ ucfirst($proposal->status) }}</h3>
                <ul class="stack-list" style="margin-top: 18px;">
                    <li>
                        <strong>{{ $proposal->proposal_number }}</strong>
                        <span>{{ $proposal->reference_number }}</span>
                    </li>
                    <li>
                        <strong>{{ $proposal->client_company ?: $proposal->client_name ?: 'Client' }}</strong>
                        <span>Prepared by {{ $proposal->prepared_by ?: $proposal->company_name }}</span>
                    </li>
                    <li>
                        <strong>{{ $proposal->exports->count() }} exports</strong>
                        <span>PDF and Word history is recorded automatically.</span>
                    </li>
                </ul>
            </section>
        </aside>
    </div>
@endsection
