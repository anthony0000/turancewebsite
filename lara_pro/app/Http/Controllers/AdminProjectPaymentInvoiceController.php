<?php

namespace App\Http\Controllers;

use App\Models\LuxuryQuote;
use App\Models\Project;
use App\Models\ProjectPaymentInvoice;
use App\Models\ProjectPaymentReceipt;
use App\Support\AdminAccess;
use App\Support\DocumentTypography;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminProjectPaymentInvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = ProjectPaymentInvoice::query()->with(['project', 'paidBy'])->latest('issue_date')->latest('id');
        $search = trim((string) $request->string('q'));
        $status = in_array($request->string('status')->toString(), ['unpaid', 'paid'], true)
            ? $request->string('status')->toString()
            : null;

        $query
            ->when($search !== '', fn (Builder $invoices) => $invoices->where(function (Builder $matches) use ($search): void {
                $matches
                    ->where('invoice_number', 'like', '%'.$search.'%')
                    ->orWhere('client_company', 'like', '%'.$search.'%')
                    ->orWhere('project_title', 'like', '%'.$search.'%')
                    ->orWhereHas('project', fn (Builder $projects) => $projects
                        ->where('project_number', 'like', '%'.$search.'%')
                        ->orWhere('name', 'like', '%'.$search.'%'));
            }))
            ->when($status, fn (Builder $invoices) => $invoices->where('status', $status))
            ->when($request->filled('project'), fn (Builder $invoices) => $invoices->where('project_id', $request->integer('project')));

        $summary = ProjectPaymentInvoice::query()
            ->selectRaw('COUNT(*) as invoice_count')
            ->selectRaw("SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_count")
            ->first();
        $totalsByCurrency = ProjectPaymentInvoice::query()
            ->selectRaw('currency, status, SUM(total_amount) as aggregate_total')
            ->groupBy('currency', 'status')
            ->get();

        return view('admin.project-payments.index', [
            'invoices' => $query->paginate(15)->withQueryString(),
            'projects' => Project::query()->whereNotIn('status', ['archived', 'cancelled'])->orderBy('name')->get(),
            'summary' => $summary,
            'outstandingTotal' => $this->formatCurrencySummary($totalsByCurrency->where('status', 'unpaid')),
            'paidTotal' => $this->formatCurrencySummary($totalsByCurrency->where('status', 'paid')),
            'filters' => ['q' => $search, 'status' => $status, 'project' => $request->integer('project')],
        ]);
    }

    public function create(Request $request): View
    {
        $projects = $this->pipelineProjects();
        $selectedProject = $projects->first(function (Project $project) use ($request): bool {
            $requested = $request->string('project')->toString();

            return $requested !== '' && ($project->project_number === $requested || (string) $project->id === $requested);
        }) ?? $projects->first();

        return view('admin.project-payments.create', [
            'projects' => $projects,
            'projectOptions' => $this->projectOptions($projects),
            'selectedProject' => $selectedProject,
            'defaults' => $this->defaultsForProject($selectedProject),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateInvoice($request);

        $invoice = DB::transaction(function () use ($validated): ProjectPaymentInvoice {
            return ProjectPaymentInvoice::query()->create([
                ...$this->invoiceAttributes($validated),
                'invoice_number' => $this->generateInvoiceNumber(),
                'status' => 'unpaid',
                'created_by' => AdminAccess::currentUser()?->id,
            ]);
        });

        return redirect()
            ->route('admin.project-payments.show', $invoice)
            ->with('status', "Payment invoice {$invoice->invoice_number} created.");
    }

    public function show(ProjectPaymentInvoice $projectPaymentInvoice): View
    {
        return view('admin.project-payments.show', [
            'invoice' => $projectPaymentInvoice->load([
                'project.staffContracts',
                'originalInvoice',
                'paidBy',
                'receipts' => fn ($receipts) => $receipts->with(['staffContract', 'uploader'])->latest('paid_on')->latest('id'),
            ]),
            'brand' => config('luxury-quotes.brand', []),
            'canMarkPaid' => AdminAccess::isFullAdmin() && ! $projectPaymentInvoice->isPaid(),
            'canManagePayments' => AdminAccess::isFullAdmin(),
            'paymentTypes' => ProjectPaymentReceipt::TYPES,
        ]);
    }

    public function edit(ProjectPaymentInvoice $projectPaymentInvoice): View
    {
        $projects = $this->pipelineProjects($projectPaymentInvoice->project_id);

        return view('admin.project-payments.edit', [
            'invoice' => $projectPaymentInvoice,
            'projects' => $projects,
            'projectOptions' => $this->projectOptions($projects),
        ]);
    }

    public function update(Request $request, ProjectPaymentInvoice $projectPaymentInvoice): RedirectResponse
    {
        $validated = $this->validateInvoice($request);
        $projectPaymentInvoice->update($this->invoiceAttributes($validated));

        return redirect()
            ->route('admin.project-payments.show', $projectPaymentInvoice)
            ->with('status', "Payment invoice {$projectPaymentInvoice->invoice_number} updated.");
    }

    public function markPaid(Request $request, ProjectPaymentInvoice $projectPaymentInvoice): RedirectResponse
    {
        abort_unless(AdminAccess::isFullAdmin(), 403);

        $validated = $request->validate([
            'paid_on' => ['nullable', 'date'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'payment_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($projectPaymentInvoice, $validated): void {
            $invoice = ProjectPaymentInvoice::query()->whereKey($projectPaymentInvoice->id)->lockForUpdate()->firstOrFail();

            if ($invoice->isPaid()) {
                return;
            }

            $invoice->update([
                'status' => 'paid',
                'paid_at' => filled($validated['paid_on'] ?? null)
                    ? Carbon::parse($validated['paid_on'])->startOfDay()
                    : now(),
                'paid_by' => AdminAccess::currentUser()?->id,
                'payment_reference' => $this->nullableString($validated['payment_reference'] ?? null),
                'payment_notes' => $this->nullableString($validated['payment_notes'] ?? null),
            ]);
        });

        return redirect()
            ->route('admin.project-payments.show', $projectPaymentInvoice)
            ->with('status', "Payment invoice {$projectPaymentInvoice->invoice_number} marked as paid.");
    }

    public function downloadPdf(ProjectPaymentInvoice $projectPaymentInvoice): Response
    {
        $pdf = Pdf::loadView('admin.project-payments.pdf', [
            'invoice' => $projectPaymentInvoice->load(['project', 'paidBy']),
            'brand' => config('luxury-quotes.brand', []),
        ])->setPaper('a4');
        DocumentTypography::registerDompdfFonts($pdf->getDomPDF());

        return $pdf->download(Str::slug($projectPaymentInvoice->client_company.' '.$projectPaymentInvoice->invoice_number).'.pdf');
    }

    private function validateInvoice(Request $request): array
    {
        return $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'original_invoice_id' => ['nullable', 'integer', 'exists:luxury_quotes,id'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'payment_terms' => ['required', 'string', 'max:255'],
            'original_invoice_number' => ['nullable', 'string', 'max:255'],
            'client_company' => ['required', 'string', 'max:255'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'client_title' => ['nullable', 'string', 'max:255'],
            'client_email' => ['nullable', 'email', 'max:255'],
            'client_phone' => ['nullable', 'string', 'max:80'],
            'project_title' => ['required', 'string', 'max:255'],
            'project_category' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'payment_description' => ['required', 'string', 'max:255'],
            'currency' => ['required', 'string', 'size:3'],
            'local_currency' => ['required', 'string', 'size:3'],
            'contract_value' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'completion_percentage' => ['required', 'numeric', 'min:0.01', 'max:100'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'exchange_rate' => ['required', 'numeric', 'min:0.0001', 'max:9999999999.9999'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:100'],
            'prepared_by' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function invoiceAttributes(array $validated): array
    {
        $contractValue = round((float) $validated['contract_value'], 2);
        $completionPercentage = round((float) $validated['completion_percentage'], 2);
        $subtotal = round($contractValue * ($completionPercentage / 100), 2);
        $taxRate = round((float) $validated['tax_rate'], 2);
        $taxAmount = round($subtotal * ($taxRate / 100), 2);
        $originalInvoiceId = filled($validated['original_invoice_id'] ?? null)
            && Project::query()
                ->whereKey($validated['project_id'])
                ->whereHas('staffContracts', fn (Builder $contracts) => $contracts->where('luxury_quote_id', $validated['original_invoice_id']))
                ->exists()
                    ? (int) $validated['original_invoice_id']
                    : null;

        return [
            'project_id' => (int) $validated['project_id'],
            'original_invoice_id' => $originalInvoiceId,
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'] ?? null,
            'payment_terms' => trim($validated['payment_terms']),
            'original_invoice_number' => $this->nullableString($validated['original_invoice_number'] ?? null),
            'client_company' => trim($validated['client_company']),
            'client_name' => $this->nullableString($validated['client_name'] ?? null),
            'client_title' => $this->nullableString($validated['client_title'] ?? null),
            'client_email' => $this->nullableString($validated['client_email'] ?? null),
            'client_phone' => $this->nullableString($validated['client_phone'] ?? null),
            'project_title' => trim($validated['project_title']),
            'project_category' => $this->nullableString($validated['project_category'] ?? null),
            'description' => trim($validated['description']),
            'payment_description' => trim($validated['payment_description']),
            'currency' => strtoupper($validated['currency']),
            'local_currency' => strtoupper($validated['local_currency']),
            'contract_value' => $contractValue,
            'completion_percentage' => $completionPercentage,
            'subtotal_amount' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total_amount' => round($subtotal + $taxAmount, 2),
            'exchange_rate' => round((float) $validated['exchange_rate'], 4),
            'bank_name' => $this->nullableString($validated['bank_name'] ?? null),
            'account_name' => $this->nullableString($validated['account_name'] ?? null),
            'account_number' => $this->nullableString($validated['account_number'] ?? null),
            'prepared_by' => $this->nullableString($validated['prepared_by'] ?? null),
            'notes' => $this->nullableString($validated['notes'] ?? null),
        ];
    }

    private function pipelineProjects(?int $includeProjectId = null): Collection
    {
        return Project::query()
            ->with(['client', 'staffContracts.invoice'])
            ->withCount([
                'tasks' => fn (Builder $tasks) => $tasks->whereNull('archived_at'),
                'tasks as completed_tasks_count' => fn (Builder $tasks) => $tasks->whereNull('archived_at')->whereNotNull('completed_at'),
            ])
            ->where(function (Builder $projects) use ($includeProjectId): void {
                $projects->whereNotIn('status', ['archived', 'cancelled']);
                if ($includeProjectId) {
                    $projects->orWhereKey($includeProjectId);
                }
            })
            ->latest('updated_at')
            ->get();
    }

    private function projectOptions($projects): array
    {
        return $projects->mapWithKeys(function (Project $project): array {
            return [(string) $project->id => $this->defaultsForProject($project)];
        })->all();
    }

    private function defaultsForProject(?Project $project): array
    {
        $sourceInvoice = $project?->staffContracts
            ?->sortByDesc('id')
            ->pluck('invoice')
            ->filter()
            ->first();
        $client = $project?->client;
        $progress = $project?->progress_percentage ?? 0;
        $contractValue = (float) ($project?->budget ?: $sourceInvoice?->investment_amount ?: 0);

        return [
            'project_id' => $project?->id,
            'project_number' => $project?->project_number,
            'project_title' => $project?->name ?? '',
            'project_category' => $sourceInvoice?->project_category ?? 'Project delivery',
            'project_status' => $project?->status,
            'progress' => $progress,
            'contract_value' => $contractValue > 0 ? number_format($contractValue, 2, '.', '') : '',
            'client_company' => $client?->company ?: $project?->client_company ?: $sourceInvoice?->company_name ?: '',
            'client_name' => $client?->name ?: $project?->client_name ?: $sourceInvoice?->recipient_name ?: '',
            'client_title' => $sourceInvoice?->recipient_title ?: '',
            'client_email' => $client?->email ?: $sourceInvoice?->recipient_email ?: '',
            'client_phone' => $client?->phone ?: $sourceInvoice?->recipient_phone ?: '',
            'description' => $project?->description ?: $sourceInvoice?->executive_summary ?: '',
            'payment_description' => $progress > 0 ? $progress.'% Project Payment' : 'Project Progress Payment',
            'original_invoice_id' => $sourceInvoice?->id,
            'original_invoice_number' => $sourceInvoice?->quote_number ?: '',
            'exchange_rate' => number_format((float) ($sourceInvoice?->exchange_rate ?: config('project-payments.exchange_rate', 1370)), 4, '.', ''),
            'issue_date' => now()->toDateString(),
            'due_date' => null,
            'payment_terms' => 'Due upon receipt',
            'currency' => config('project-payments.currency', 'USD'),
            'local_currency' => config('project-payments.local_currency', 'NGN'),
            'tax_rate' => '0.00',
            'bank_name' => config('project-payments.bank_name'),
            'account_name' => config('project-payments.account_name'),
            'account_number' => config('project-payments.account_number'),
            'prepared_by' => config('luxury-quotes.brand.studio_name', 'Turance Technologies'),
            'notes' => 'Thank you for your continued partnership.',
        ];
    }

    private function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $sequence = LuxuryQuote::query()->whereDate('created_at', today())->count()
            + ProjectPaymentInvoice::query()->whereDate('created_at', today())->count()
            + 1;

        do {
            $number = sprintf('TT-INV-%s-%03d', $date, $sequence++);
        } while (
            LuxuryQuote::query()->where('quote_number', $number)->exists()
            || ProjectPaymentInvoice::query()->where('invoice_number', $number)->exists()
        );

        return $number;
    }

    private function nullableString(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    private function formatCurrencySummary(Collection $totals): string
    {
        if ($totals->isEmpty()) {
            return strtoupper((string) config('project-payments.currency', 'USD')).' 0.00';
        }

        return $totals
            ->sortBy('currency')
            ->map(fn ($total): string => strtoupper((string) $total->currency).' '.number_format((float) $total->aggregate_total, 2))
            ->implode(' · ');
    }
}
