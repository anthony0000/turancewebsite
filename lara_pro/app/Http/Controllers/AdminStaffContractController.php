<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\StaffContract;
use App\Models\LuxuryQuote;
use App\Support\DocumentTypography;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminStaffContractController extends Controller
{
    private const STATUSES = [
        'draft',
        'pending_signature',
        'signed',
        'active',
        'completed',
        'terminated',
    ];

    public function index(): View
    {
        return view('admin.staff-contracts.index', [
            'contracts' => StaffContract::query()
                ->with(['project', 'invoice'])
                ->latest('updated_at')
                ->get(),
            'invoiceCount' => LuxuryQuote::query()->count(),
            'activeCount' => StaffContract::query()->whereIn('status', ['pending_signature', 'signed', 'active'])->count(),
            'signedCount' => StaffContract::query()->where('status', 'signed')->count(),
            'totalValue' => (float) StaffContract::query()->sum('agreed_fee'),
        ]);
    }

    public function create(): View
    {
        return view('admin.staff-contracts.create', [
            'invoices' => LuxuryQuote::query()->latest('created_at')->get(),
            'contract' => null,
            'statuses' => self::STATUSES,
            'brand' => config('luxury-quotes.brand', []),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateContractRequest($request);

        $contract = DB::transaction(function () use ($validated): StaffContract {
            $invoice = LuxuryQuote::query()->findOrFail($validated['luxury_quote_id']);
            $project = $this->projectForInvoice($invoice);

            return StaffContract::query()->create($this->contractAttributes(
                $validated,
                $project,
                $invoice,
                $this->generateContractNumber(),
            ));
        });

        return redirect()
            ->route('admin.staff-contracts.show', $contract)
            ->with('status', "Staff contract {$contract->contract_number} created.");
    }

    public function show(StaffContract $staffContract): View
    {
        return view('admin.staff-contracts.show', [
            'contract' => $staffContract->load(['project', 'invoice']),
            'brand' => config('luxury-quotes.brand', []),
        ]);
    }

    public function edit(StaffContract $staffContract): View
    {
        return view('admin.staff-contracts.create', [
            'invoices' => LuxuryQuote::query()->latest('created_at')->get(),
            'contract' => $staffContract->load(['project', 'invoice']),
            'statuses' => self::STATUSES,
            'brand' => config('luxury-quotes.brand', []),
        ]);
    }

    public function update(Request $request, StaffContract $staffContract): RedirectResponse
    {
        $validated = $this->validateContractRequest($request);
        $invoice = LuxuryQuote::query()->findOrFail($validated['luxury_quote_id']);

        $staffContract->update($this->contractAttributes(
            $validated,
            $this->projectForInvoice($invoice),
            $invoice,
            $staffContract->contract_number,
            $staffContract,
        ));

        return redirect()
            ->route('admin.staff-contracts.show', $staffContract)
            ->with('status', "Staff contract {$staffContract->contract_number} updated.");
    }

    public function downloadPdf(StaffContract $staffContract): Response
    {
        $staffContract->load(['project', 'invoice']);

        $pdf = Pdf::loadView('admin.staff-contracts.pdf', [
            'contract' => $staffContract,
            'brand' => config('luxury-quotes.brand', []),
        ])->setPaper('a4');
        DocumentTypography::registerDompdfFonts($pdf->getDomPDF());

        return $pdf->download(Str::slug($staffContract->contract_number.' '.$staffContract->staff_name).'.pdf');
    }

    private function validateContractRequest(Request $request): array
    {
        return $request->validate([
            'luxury_quote_id' => ['required', 'integer', 'exists:luxury_quotes,id'],
            'status' => ['required', 'string', Rule::in(self::STATUSES)],
            'staff_name' => ['required', 'string', 'max:255'],
            'staff_email' => ['nullable', 'email', 'max:255'],
            'staff_phone' => ['nullable', 'string', 'max:80'],
            'staff_role' => ['required', 'string', 'max:255'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'currency' => ['required', 'string', 'size:3'],
            'agreed_fee' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'payment_terms' => ['required', 'string', 'min:10', 'max:5000'],
            'scope_of_work' => ['required', 'string', 'min:10', 'max:15000'],
            'terms' => ['required', 'string', 'min:20', 'max:20000'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_signatory_name' => ['nullable', 'string', 'max:255'],
            'company_signatory_title' => ['nullable', 'string', 'max:255'],
            'company_signed_date' => ['nullable', 'date'],
            'staff_signatory_name' => ['nullable', 'string', 'max:255'],
            'staff_signed_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function projectForInvoice(LuxuryQuote $invoice): Project
    {
        $projectName = trim($invoice->project_title);
        $clientCompany = $this->nullableTrim($invoice->company_name);

        return Project::query()->firstOrCreate(
            [
                'name' => $projectName,
                'client_company' => $clientCompany,
            ],
            [
                'project_number' => $this->generateProjectNumber(),
                'client_name' => $this->nullableTrim($invoice->recipient_name),
                'status' => 'active',
                'description' => $this->nullableTrim($invoice->executive_summary),
            ],
        );
    }

    private function contractAttributes(
        array $validated,
        Project $project,
        LuxuryQuote $invoice,
        string $contractNumber,
        ?StaffContract $existing = null,
    ): array
    {
        return [
            'project_id' => $project->id,
            'luxury_quote_id' => $invoice->id,
            'contract_number' => $contractNumber,
            'status' => $validated['status'],
            'staff_name' => trim($validated['staff_name']),
            'staff_email' => $this->optionalContractString($validated, 'staff_email', $existing?->staff_email),
            'staff_phone' => $this->optionalContractString($validated, 'staff_phone', $existing?->staff_phone),
            'staff_role' => trim($validated['staff_role']),
            'starts_on' => $this->optionalContractDate($validated, 'starts_on', $existing?->starts_on),
            'ends_on' => $this->optionalContractDate($validated, 'ends_on', $existing?->ends_on),
            'currency' => strtoupper(trim($validated['currency'])),
            'agreed_fee' => round((float) $validated['agreed_fee'], 2),
            'payment_terms' => trim($validated['payment_terms']),
            'scope_of_work' => trim($validated['scope_of_work']),
            'terms' => trim($validated['terms']),
            'company_name' => $this->nullableTrim($validated['company_name'] ?? null)
                ?: ($existing?->company_name ?: config('luxury-quotes.brand.studio_name', 'Turance Technologies')),
            'company_signatory_name' => $this->optionalContractString($validated, 'company_signatory_name', $existing?->company_signatory_name),
            'company_signatory_title' => $this->optionalContractString($validated, 'company_signatory_title', $existing?->company_signatory_title),
            'company_signed_date' => $this->optionalContractDate($validated, 'company_signed_date', $existing?->company_signed_date),
            'staff_signatory_name' => $this->optionalContractString($validated, 'staff_signatory_name', $existing?->staff_signatory_name),
            'staff_signed_date' => $this->optionalContractDate($validated, 'staff_signed_date', $existing?->staff_signed_date),
            'notes' => $this->optionalContractString($validated, 'notes', $existing?->notes),
        ];
    }

    private function generateProjectNumber(): string
    {
        do {
            $number = 'TT-PRJ-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
        } while (Project::query()->where('project_number', $number)->exists());

        return $number;
    }

    private function generateContractNumber(): string
    {
        do {
            $number = 'TT-STAFF-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
        } while (StaffContract::query()->where('contract_number', $number)->exists());

        return $number;
    }

    private function nullableTrim(?string $value): ?string
    {
        return filled($value) ? trim($value) : null;
    }

    private function optionalContractString(array $validated, string $key, ?string $existing): ?string
    {
        return array_key_exists($key, $validated)
            ? $this->nullableTrim($validated[$key] ?? null)
            : $existing;
    }

    private function optionalContractDate(array $validated, string $key, mixed $existing): mixed
    {
        return array_key_exists($key, $validated)
            ? $this->nullableDate($validated[$key] ?? null)
            : $existing;
    }

    private function nullableDate(?string $value): ?Carbon
    {
        return filled($value) ? Carbon::parse($value) : null;
    }
}
