<section class="tt-page tt-page--archive" id="saved-quotes">
    <header class="tt-subpage-head">
        <div>
            <span class="eyebrow">Invoice archive</span>
            <h1>Saved invoices</h1>
            <p>Preview, edit, export, or generate an MOU from one focused workspace.</p>
        </div>
        <span class="tt-page-badge"><i class="tt-page-badge__gold"></i>{{ number_format($quoteCount) }} {{ \Illuminate\Support\Str::plural('invoice', $quoteCount) }} saved</span>
    </header>

    <section class="tt-section tt-archive-section">
        <div class="tt-section-head">
            <div><span class="eyebrow">Document library</span><h2>Invoice list</h2><p>Recent documents and exports.</p></div>
            <a class="panel-head__link" href="{{ route('admin.quotes.create') }}">Create invoice</a>
        </div>
        <div class="table-wrap">
            <table class="quote-table tt-archive-table">
                <thead><tr><th>Reference</th><th>Company</th><th>Template</th><th>Investment</th><th>Validity</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse ($quotes as $quote)
                        <tr>
                            <td><strong>{{ $quote->quote_number }}</strong><span>{{ $quote->project_category }}</span></td>
                            <td><strong>{{ $quote->company_name }}</strong><span>{{ $quote->project_title }}</span></td>
                            <td><strong>{{ $templates[$quote->template]['name'] ?? ucfirst($quote->template) }}</strong><span>{{ $templates[$quote->template]['badge'] ?? 'Invoice' }}</span></td>
                            <td><strong>${{ number_format((float) $quote->investment_amount, 0) }}</strong><span>{{ $quote->timeline }}</span></td>
                            <td><strong>{{ optional($quote->valid_until)->format('M d, Y') }}</strong><span>Created {{ optional($quote->created_at)->format('M d, Y') }}</span></td>
                            <td>
                                <details class="action-menu"><summary>Actions</summary><div class="action-menu-panel">
                                    <a href="{{ route('admin.quotes.show', $quote) }}">Preview</a><a href="{{ route('admin.quotes.edit', $quote) }}">Edit</a><a href="{{ route('admin.quotes.pdf', $quote) }}">Download PDF</a><a href="{{ route('admin.quotes.mou', $quote) }}">Download MOU</a>
                                </div></details>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><strong>No invoices created yet.</strong><span>Create the first invoice using the builder above.</span></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</section>
