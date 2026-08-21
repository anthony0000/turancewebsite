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
                <a class="ghost-button" href="{{ route('admin.staff-contracts.edit', $contract) }}">Edit Contract</a>
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
        </aside>
    </div>
@endsection
