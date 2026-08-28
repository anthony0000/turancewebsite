@extends('admin.layouts.app')

@section('title', 'Staff Contracts | Admin')

@section('content')
    <style>
        .contract-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 30px;
            padding: 5px 11px 5px 9px;
            border-radius: 999px;
            border: 1px solid transparent;
            background: rgba(184, 134, 11, 0.1);
            color: var(--muted-strong, var(--muted));
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.06em;
            line-height: 1;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .contract-status::before {
            content: "";
            width: 7px;
            height: 7px;
            flex: 0 0 auto;
            border-radius: 999px;
            background: var(--accent);
            box-shadow: 0 0 0 3px rgba(184, 134, 11, 0.12);
        }

        .quote-table td .contract-status {
            display: inline-flex;
            margin-top: 0;
            color: var(--muted-strong, var(--muted));
            font-size: 10px;
        }

        .quote-table td .contract-status > span {
            display: inline;
            margin-top: 0;
            color: inherit;
            font-size: inherit;
        }

        .contract-status--signed,
        .quote-table td .contract-status--signed {
            border-color: rgba(47, 128, 84, 0.2);
            background: #eaf5ee;
            color: #34734e;
        }

        .contract-status--signed::before {
            background: var(--success);
            box-shadow: 0 0 0 3px rgba(47, 128, 84, 0.13);
        }

        .contract-status--active,
        .quote-table td .contract-status--active {
            border-color: rgba(45, 126, 119, 0.2);
            background: #e8f4f2;
            color: #2e716b;
        }

        .contract-status--active::before {
            background: #2d7e77;
            box-shadow: 0 0 0 3px rgba(45, 126, 119, 0.13);
        }

        .contract-status--pending_signature,
        .quote-table td .contract-status--pending_signature {
            border-color: rgba(184, 134, 11, 0.22);
            background: #fff6df;
            color: #86630d;
        }

        .contract-status--pending_signature::before {
            background: #d09a1e;
            box-shadow: 0 0 0 3px rgba(208, 154, 30, 0.14);
        }

        .contract-status--draft,
        .quote-table td .contract-status--draft {
            border-color: #e1e5e9;
            background: #f4f5f6;
            color: #68717b;
        }

        .contract-status--draft::before {
            background: #98a1ab;
            box-shadow: 0 0 0 3px rgba(152, 161, 171, 0.14);
        }

        .contract-status--completed,
        .quote-table td .contract-status--completed {
            border-color: rgba(91, 107, 166, 0.2);
            background: #eef1fb;
            color: #5669a1;
        }

        .contract-status--completed::before {
            background: #687fc1;
            box-shadow: 0 0 0 3px rgba(104, 127, 193, 0.14);
        }

        .contract-status--terminated,
        .quote-table td .contract-status--terminated {
            border-color: rgba(185, 74, 61, 0.2);
            background: #fff0ef;
            color: #a24d48;
        }

        .contract-status--terminated::before {
            background: var(--danger);
            box-shadow: 0 0 0 3px rgba(185, 74, 61, 0.13);
        }

        .staff-contract-analytics {
            align-items: stretch;
        }

        .staff-contract-analytics > .panel {
            height: 100%;
        }

        .staff-contract-month-chart {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            align-items: stretch;
            min-height: 224px;
            margin-top: 8px;
            padding: 16px;
            gap: 12px;
        }

        .staff-contract-month-chart .month-bar {
            display: grid;
            grid-template-rows: auto minmax(0, 1fr) auto;
            align-items: end;
            min-height: 190px;
            gap: 10px;
            text-align: center;
        }

        .staff-contract-month-chart .month-bar-column {
            align-self: end;
            justify-self: center;
            width: min(100%, 38px);
            max-width: none;
            border-radius: 10px 10px 4px 4px;
            background: linear-gradient(180deg, var(--quote), var(--pipeline));
            box-shadow: 0 12px 22px rgba(102, 76, 20, 0.14);
        }

        .staff-contract-month-chart .month-bar strong {
            color: var(--text);
            font-size: 11px;
        }

        .staff-contract-month-chart .month-bar > span {
            color: var(--muted);
            font-size: 11px;
        }

        .staff-contract-currency-list {
            display: grid;
            gap: 12px;
        }

        .staff-contract-currency-list .bar-row {
            background: rgba(184, 134, 11, 0.035);
        }

        .staff-contract-currency-value {
            color: var(--text);
            font-size: 13px;
            white-space: nowrap;
        }

        .staff-contract-chart-note {
            margin: 14px 0 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.6;
        }

        .staff-contract-profit-value {
            color: var(--success);
        }

        .staff-contract-profit-value.is-negative,
        .staff-contract-profit-inline.is-negative {
            color: var(--danger);
        }

        .staff-contract-profit-inline {
            color: var(--success);
            font-weight: 700;
        }

        @media (max-width: 640px) {
            .staff-contract-month-chart {
                gap: 6px;
                padding: 12px 8px;
            }

            .staff-contract-month-chart .month-bar {
                gap: 7px;
            }

            .staff-contract-month-chart .month-bar-column {
                width: 26px;
            }
        }
    </style>

    <section class="panel hero-banner">
        <div>
            <span class="eyebrow">Invoice-linked agreements</span>
            <h1>Keep every contract staff engagement clear, priced, and ready to sign.</h1>
            <p>
                Create a formal staff contract from an existing invoice, record the agreed price and terms, and keep
                both signing parties in one exportable document.
            </p>
            <div class="hero-actions">
                <a class="button" href="{{ route('admin.staff-contracts.create') }}">Create Staff Contract</a>
                <a class="ghost-button" href="{{ route('admin.proposals.index') }}">Open Proposal Studio</a>
            </div>
        </div>

        <div class="hero-callout">
            <div class="callout-card">
                <span class="metric-label">Invoices available</span>
                <strong>{{ number_format($invoiceCount) }}</strong>
                <p>Existing invoices available to the staff agreement register.</p>
            </div>
            <div class="callout-card">
                <span class="metric-label">Agreement value</span>
                <strong>{{ number_format($totalValue, 2) }}</strong>
                <p>{{ number_format($signedCount) }} signed {{ $signedCount === 1 ? 'agreement' : 'agreements' }}. Each contract retains its own currency.</p>
            </div>
            <div class="callout-card">
                <span class="metric-label">Profit left (NGN)</span>
                @if ($profitSummary['available'])
                    <strong class="staff-contract-profit-value{{ $profitSummary['value'] < 0 ? ' is-negative' : '' }}">NGN {{ number_format($profitSummary['value'], 2) }}</strong>
                    <p>Invoice value less all staff fees across {{ number_format($profitSummary['invoiceCount']) }} linked {{ \Illuminate\Support\Str::plural('invoice', $profitSummary['invoiceCount']) }}.</p>
                @else
                    <strong>Unavailable</strong>
                    <p>A linked invoice is needed to calculate profit.</p>
                @endif
            </div>
        </div>
    </section>

    <section class="kpi-grid" style="margin-top: 24px;">
        <article class="panel kpi-card kpi-card--quotes">
            <span class="metric-label">Total contracts</span>
            <strong class="kpi-value">{{ number_format($contracts->count()) }}</strong>
            <span class="kpi-meta">Every project-bound staff agreement.</span>
        </article>
        <article class="panel kpi-card kpi-card--leads">
            <span class="metric-label">In signature flow</span>
            <strong class="kpi-value">{{ number_format($activeCount) }}</strong>
            <span class="kpi-meta">Pending signature, signed, or active.</span>
        </article>
        <article class="panel kpi-card kpi-card--pipeline">
            <span class="metric-label">Signed agreements</span>
            <strong class="kpi-value">{{ number_format($signedCount) }}</strong>
            <span class="kpi-meta">Ready to use as the engagement record.</span>
        </article>
        <article class="panel kpi-card kpi-card--traffic">
            <span class="metric-label">Invoices available</span>
            <strong class="kpi-value">{{ number_format($invoiceCount) }}</strong>
            <span class="kpi-meta">The required source for every new contract.</span>
        </article>
    </section>

    <section class="analytics-grid staff-contract-analytics" style="margin-top: 24px;">
        <article class="panel panel-padded">
            <div class="panel-head panel-head--tight">
                <span class="eyebrow">Agreement activity</span>
                <h2 class="panel-title">Agreements created</h2>
                <p>New staff agreements added during the last six months.</p>
            </div>

            @if ($monthlyActivity->sum('count') > 0)
                <div class="mini-chart staff-contract-month-chart" role="img" aria-label="Staff agreements created during the last six months">
                    @foreach ($monthlyActivity as $month)
                        <div class="month-bar" title="{{ $month['period'] }}: {{ number_format($month['count']) }} {{ \Illuminate\Support\Str::plural('agreement', $month['count']) }}">
                            <span>{{ number_format($month['count']) }}</span>
                            <div class="month-bar-column" style="height: {{ max(6, $month['height'] * 1.55) }}px;" aria-hidden="true"></div>
                            <strong>{{ $month['label'] }}</strong>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="data-note">New agreement activity will appear here after a contract is created.</div>
            @endif
        </article>

        <article class="panel panel-padded">
            <div class="panel-head panel-head--tight">
                <span class="eyebrow">Commercial mix</span>
                <h2 class="panel-title">Fee by currency</h2>
                <p>Compare agreement value within each currency.</p>
            </div>

            @if ($currencyBreakdown->isNotEmpty())
                <div class="staff-contract-currency-list">
                    @foreach ($currencyBreakdown as $currency)
                        <div class="bar-row">
                            <div class="bar-header">
                                <div>
                                    <strong>{{ $currency['currency'] }}</strong>
                                    <span class="bar-meta">{{ number_format($currency['count']) }} {{ \Illuminate\Support\Str::plural('agreement', $currency['count']) }}</span>
                                </div>
                                <strong class="staff-contract-currency-value">{{ $currency['currency'] }} {{ number_format($currency['value'], 2) }}</strong>
                            </div>
                            <div class="bar-track" role="progressbar" aria-label="{{ $currency['currency'] }} agreement value" aria-valuenow="{{ $currency['value'] }}" aria-valuemin="0" aria-valuemax="{{ $maxCurrencyValue }}">
                                <div class="bar-fill bar-fill--lead" style="width: {{ max(5, $currency['width']) }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="staff-contract-chart-note">Currencies remain separate; no exchange-rate conversion is applied.</p>
            @else
                <div class="data-note">Fee distribution will appear after the first staff agreement is created.</div>
            @endif
        </article>
    </section>

    <section class="panel panel-padded" style="margin-top: 24px;">
        <div class="panel-head panel-head--tight">
            <span class="eyebrow">Signature flow</span>
            <h2 class="panel-title">Contract status mix</h2>
            <p>Use the distribution to see where agreements are waiting before opening the register.</p>
        </div>

        @if ($statusBreakdown->isNotEmpty())
            @php($maxStatusCount = max(1, (int) $statusBreakdown->max('count')))
            <div class="stat-list">
                @foreach ($statusBreakdown as $status)
                    <div class="bar-row">
                        <div class="bar-header">
                            <div>
                                <strong>{{ $status['label'] }}</strong>
                                <span class="bar-meta">{{ $status['count'] === 1 ? 'One agreement' : number_format($status['count']).' agreements' }}</span>
                            </div>
                            <strong class="bar-count">{{ number_format($status['count']) }}</strong>
                        </div>
                        <div class="bar-track" role="progressbar" aria-label="{{ $status['label'] }} contracts"
                            aria-valuenow="{{ $status['count'] }}" aria-valuemin="0" aria-valuemax="{{ $maxStatusCount }}">
                            <div class="bar-fill bar-fill--quote" style="width: {{ max(5, round(($status['count'] / $maxStatusCount) * 100, 1)) }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="data-note">The status mix will appear after the first staff agreement is created.</div>
        @endif
    </section>

    <section class="panel panel-padded" style="margin-top: 24px;">
        <div class="panel-head">
            <span class="eyebrow">Contract register</span>
            <h2>Invoice-linked staff documents</h2>
            <p>Preview, edit, or export the latest version of an agreement.</p>
        </div>

        <div class="table-wrap">
            <table class="quote-table">
                <thead>
                    <tr>
                        <th>Contract</th>
                        <th>Staff member</th>
                        <th>Project</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contracts as $contract)
                        <tr>
                            <td>
                                <strong>{{ $contract->contract_number }}</strong>
                                <span>Updated {{ optional($contract->updated_at)->format('M d, Y') }}</span>
                            </td>
                            <td>
                                <strong>{{ $contract->staff_name }}</strong>
                                <span>{{ $contract->staff_role }}</span>
                            </td>
                            <td>
                                <strong>{{ $contract->project->name }}</strong>
                                <span>{{ $contract->project->project_number }}{{ $contract->project->client_company ? ' / '.$contract->project->client_company : '' }}</span>
                                @if ($contract->invoice)
                                    <span>Invoice {{ $contract->invoice->quote_number }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $contract->currency }} {{ number_format((float) $contract->agreed_fee, 2) }}</strong>
                                <span>Agreed fee</span>
                                @php($financialSummary = $contractFinancials[$contract->getKey()] ?? null)
                                @if ($financialSummary)
                                    <span class="staff-contract-profit-inline{{ $financialSummary['profitNaira'] < 0 ? ' is-negative' : '' }}">Profit left: NGN {{ number_format($financialSummary['profitNaira'], 2) }}</span>
                                @else
                                    <span>Profit unavailable</span>
                                @endif
                            </td>
                            <td>
                                <span class="contract-status contract-status--{{ $contract->status }}">
                                    <span>{{ str_replace('_', ' ', $contract->status) }}</span>
                                </span>
                            </td>
                            <td>
                                <details class="action-menu">
                                    <summary>Actions</summary>
                                    <div class="action-menu-panel">
                                        <a class="action-menu-primary" href="{{ route('admin.staff-contracts.show', $contract) }}">Preview</a>
                                        @if ($contract->hasSignedDocument())
                                            <span class="action-menu-status">
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                                                Locked after signed upload
                                            </span>
                                        @else
                                            <a href="{{ route('admin.staff-contracts.edit', $contract) }}">Edit</a>
                                        @endif
                                        <span class="action-menu-divider" aria-hidden="true"></span>
                                        <a class="action-menu-download" href="{{ route('admin.staff-contracts.pdf', $contract) }}">Download PDF</a>
                                        @if ($contract->hasSignedDocument())
                                            <a class="action-menu-download" href="{{ route('admin.staff-contracts.signed-document', $contract) }}">Download signed copy</a>
                                        @endif
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <strong>No staff contracts created yet.</strong>
                                <span>Create an invoice first, then attach the agreement to that existing invoice.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
