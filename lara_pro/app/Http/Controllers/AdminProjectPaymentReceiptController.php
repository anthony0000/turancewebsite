<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectPaymentInvoice;
use App\Models\ProjectPaymentReceipt;
use App\Models\StaffContract;
use App\Support\AdminAccess;
use App\Support\PersistentUploadStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class AdminProjectPaymentReceiptController extends Controller
{
    private const RECEIPT_DIRECTORY = 'project-payments/receipts';

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $type = array_key_exists($request->string('type')->toString(), ProjectPaymentReceipt::TYPES)
            ? $request->string('type')->toString()
            : null;

        $query = ProjectPaymentReceipt::query()
            ->with(['project', 'invoice', 'staffContract', 'uploader'])
            ->latest('paid_on')
            ->latest('id')
            ->when($search !== '', fn (Builder $receipts) => $receipts->where(function (Builder $matches) use ($search): void {
                $matches
                    ->where('counterparty', 'like', '%'.$search.'%')
                    ->orWhere('reference', 'like', '%'.$search.'%')
                    ->orWhere('receipt_original_name', 'like', '%'.$search.'%')
                    ->orWhereHas('project', fn (Builder $projects) => $projects
                        ->where('project_number', 'like', '%'.$search.'%')
                        ->orWhere('name', 'like', '%'.$search.'%'))
                    ->orWhereHas('invoice', fn (Builder $invoices) => $invoices
                        ->where('invoice_number', 'like', '%'.$search.'%'))
                    ->orWhereHas('staffContract', fn (Builder $contracts) => $contracts
                        ->where('staff_name', 'like', '%'.$search.'%')
                        ->orWhere('contract_number', 'like', '%'.$search.'%'));
            }))
            ->when($type, fn (Builder $receipts) => $receipts->where('payment_type', $type))
            ->when($request->filled('project'), fn (Builder $receipts) => $receipts->where('project_id', $request->integer('project')));

        $totals = ProjectPaymentReceipt::query()
            ->selectRaw('currency, SUM(amount) as aggregate_total')
            ->groupBy('currency')
            ->get();

        return view('admin.project-payments.receipts.index', [
            'receipts' => $query->paginate(20)->withQueryString(),
            'projects' => Project::query()->orderBy('name')->get(),
            'paymentTypes' => ProjectPaymentReceipt::TYPES,
            'receiptCount' => ProjectPaymentReceipt::query()->count(),
            'recordedTotal' => $this->formatCurrencySummary($totals),
            'filters' => ['q' => $search, 'type' => $type, 'project' => $request->integer('project')],
            'canManagePayments' => AdminAccess::isFullAdmin(),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless(AdminAccess::isFullAdmin(), 403);

        $projects = Project::query()
            ->with([
                'paymentInvoices' => fn ($invoices) => $invoices->latest('issue_date'),
                'staffContracts' => fn ($contracts) => $contracts->latest('id'),
            ])
            ->orderBy('name')
            ->get();
        $requestedProject = $request->string('project')->toString();
        $selectedStaffContractId = $request->integer('staff_contract') ?: null;
        $selectedProject = $projects->first(fn (Project $project): bool => $requestedProject !== ''
            && ($project->project_number === $requestedProject || (string) $project->id === $requestedProject));
        $selectedInvoiceId = $request->integer('invoice') ?: null;

        if ($selectedInvoiceId && ! $selectedProject) {
            $invoice = ProjectPaymentInvoice::query()->find($selectedInvoiceId);
            $selectedProject = $projects->firstWhere('id', $invoice?->project_id);
        }

        if ($selectedStaffContractId && ! $selectedProject) {
            $selectedProject = $projects->first(fn (Project $project): bool => $project->staffContracts->contains('id', $selectedStaffContractId));
        }

        return view('admin.project-payments.receipts.create', [
            'projects' => $projects,
            'paymentTypes' => ProjectPaymentReceipt::TYPES,
            'selectedProject' => $selectedProject,
            'selectedInvoiceId' => $selectedInvoiceId,
            'selectedStaffContractId' => $selectedStaffContractId,
            'selectedStaffContract' => $selectedProject?->staffContracts->firstWhere('id', $selectedStaffContractId),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(AdminAccess::isFullAdmin(), 403);

        $validated = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'project_payment_invoice_id' => ['nullable', 'integer', 'exists:project_payment_invoices,id'],
            'staff_contract_id' => ['nullable', 'integer', 'exists:staff_contracts,id'],
            'payment_type' => ['required', 'string', Rule::in(array_keys(ProjectPaymentReceipt::TYPES))],
            'counterparty' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'currency' => ['required', 'string', 'size:3'],
            'paid_on' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'receipt' => ['required', 'file', 'max:20480', 'mimes:pdf,jpg,jpeg,png,webp'],
        ]);

        $this->validateRelationships($validated);

        /** @var UploadedFile $file */
        $file = $request->file('receipt');
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'document');
        $extension = preg_replace('/[^a-z0-9]+/i', '', $extension) ?: 'document';
        $relativeDirectory = self::RECEIPT_DIRECTORY.'/'.(int) $validated['project_id'];
        $storedPath = $file->storeAs($relativeDirectory, Str::uuid().'.'.$extension, PersistentUploadStorage::DISK);

        if (! is_string($storedPath) || $storedPath === '') {
            throw new RuntimeException('The payment receipt could not be stored.');
        }

        try {
            $receipt = DB::transaction(fn (): ProjectPaymentReceipt => ProjectPaymentReceipt::query()->create([
                'project_id' => (int) $validated['project_id'],
                'project_payment_invoice_id' => $validated['project_payment_invoice_id'] ?? null,
                'staff_contract_id' => $validated['staff_contract_id'] ?? null,
                'payment_type' => $validated['payment_type'],
                'counterparty' => $this->nullableString($validated['counterparty'] ?? null),
                'amount' => round((float) $validated['amount'], 2),
                'currency' => strtoupper($validated['currency']),
                'paid_on' => $validated['paid_on'],
                'reference' => $this->nullableString($validated['reference'] ?? null),
                'notes' => $this->nullableString($validated['notes'] ?? null),
                'receipt_path' => $storedPath,
                'receipt_original_name' => $this->safeOriginalName($file),
                'receipt_mime' => $file->getMimeType() ?: $file->getClientMimeType(),
                'receipt_size' => $file->getSize() ?: null,
                'uploaded_by' => AdminAccess::currentUser()?->id,
            ]));
        } catch (Throwable $exception) {
            PersistentUploadStorage::delete($storedPath);

            throw $exception;
        }

        if ($receipt->project_payment_invoice_id) {
            return redirect()
                ->route('admin.project-payments.show', $receipt->invoice)
                ->with('status', 'Payment receipt uploaded and linked to the invoice.');
        }

        return redirect()
            ->route('admin.project-payments.receipts.index')
            ->with('status', 'Payment receipt uploaded successfully.');
    }

    public function preview(ProjectPaymentReceipt $projectPaymentReceipt): BinaryFileResponse|Response
    {
        return $this->receiptResponse($projectPaymentReceipt, true);
    }

    public function download(ProjectPaymentReceipt $projectPaymentReceipt): BinaryFileResponse|Response
    {
        return $this->receiptResponse($projectPaymentReceipt, false);
    }

    private function validateRelationships(array $validated): void
    {
        $errors = [];
        $projectId = (int) $validated['project_id'];

        if (filled($validated['project_payment_invoice_id'] ?? null)
            && ! ProjectPaymentInvoice::query()->whereKey($validated['project_payment_invoice_id'])->where('project_id', $projectId)->exists()) {
            $errors['project_payment_invoice_id'] = 'The selected payment invoice does not belong to this project.';
        }

        $staffPayment = Str::startsWith($validated['payment_type'], 'staff_');
        if ($staffPayment && ! filled($validated['staff_contract_id'] ?? null)) {
            $errors['staff_contract_id'] = 'Select the staff contract associated with this payment.';
        } elseif (filled($validated['staff_contract_id'] ?? null)
            && ! StaffContract::query()->whereKey($validated['staff_contract_id'])->where('project_id', $projectId)->exists()) {
            $errors['staff_contract_id'] = 'The selected staff contract does not belong to this project.';
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function receiptResponse(ProjectPaymentReceipt $receipt, bool $inline): BinaryFileResponse|Response
    {
        abort_unless($receipt->hasStoredReceipt(), 404);
        $absolutePath = PersistentUploadStorage::absolutePath($receipt->receipt_path);
        abort_unless($absolutePath !== null, 404);

        $filename = $receipt->receipt_original_name ?: 'payment-receipt';
        $headers = [
            'Content-Type' => $receipt->receipt_mime ?: 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
        ];

        if ($inline) {
            $headers['Content-Disposition'] = 'inline; filename="'.addslashes($filename).'"';

            return response()->file($absolutePath, $headers);
        }

        return response()->download($absolutePath, $filename, $headers);
    }

    private function nullableString(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    private function safeOriginalName(UploadedFile $file): string
    {
        $name = preg_replace('/[\x00-\x1F\x7F"\\\\\/]+/u', '-', $file->getClientOriginalName());
        $name = trim((string) $name, '.- ');

        return Str::limit($name !== '' ? $name : 'payment-receipt', 255, '');
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
