<?php

use App\Models\Project;
use App\Models\ProjectPaymentInvoice;
use App\Models\ProjectPaymentReceipt;
use App\Models\StaffContract;
use App\Models\User;
use App\Support\AdminAccess;
use App\Support\PersistentUploadStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function paymentReceiptSession(User $user): array
{
    return [
        config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated') => true,
        'admin_user_id' => $user->id,
        'admin_user_name' => $user->name,
        'admin_role' => $user->role,
        'admin_permissions' => $user->permissions ?? [],
        'luxury_quote_admin_email' => $user->email,
    ];
}

function paymentReceiptInvoice(Project $project, User $admin): ProjectPaymentInvoice
{
    return ProjectPaymentInvoice::query()->create([
        'project_id' => $project->id,
        'invoice_number' => 'TT-INV-RECEIPT-'.$project->id,
        'status' => 'unpaid',
        'issue_date' => '2026-09-18',
        'payment_terms' => 'Due upon receipt',
        'client_company' => 'Acme Limited',
        'project_title' => $project->name,
        'description' => 'Progress payment for delivery work completed on the project.',
        'payment_description' => '40% project payment',
        'currency' => 'USD',
        'local_currency' => 'NGN',
        'contract_value' => 2500,
        'completion_percentage' => 40,
        'subtotal_amount' => 1000,
        'tax_rate' => 0,
        'tax_amount' => 0,
        'total_amount' => 1000,
        'exchange_rate' => 1370,
        'created_by' => $admin->id,
    ]);
}

function paymentReceiptContract(Project $project, string $number = 'SC-RECEIPT-001'): StaffContract
{
    return StaffContract::query()->create([
        'project_id' => $project->id,
        'contract_number' => $number,
        'status' => 'active',
        'staff_name' => 'Ada Engineer',
        'staff_role' => 'Software Engineer',
        'currency' => 'NGN',
        'agreed_fee' => 800000,
        'payment_terms' => 'Paid in two project delivery installments.',
        'scope_of_work' => 'Implement and verify the approved project scope.',
        'terms' => 'Deliver the agreed scope in line with project requirements.',
        'company_name' => 'Turance Technologies',
    ]);
}

it('stores a project payment receipt as a private persistent file and only metadata in the database', function () {
    Storage::fake(PersistentUploadStorage::DISK);
    $admin = User::factory()->create(['role' => AdminAccess::ROLE_ADMIN, 'permissions' => []]);
    $project = Project::query()->create(['project_number' => 'PAY-REC-1', 'name' => 'Receipt Project', 'status' => 'active']);
    $invoice = paymentReceiptInvoice($project, $admin);

    $response = $this->withSession(paymentReceiptSession($admin))->post(
        route('admin.project-payments.receipts.store'),
        [
            'project_id' => $project->id,
            'project_payment_invoice_id' => $invoice->id,
            'payment_type' => 'project_part_payment',
            'counterparty' => 'Acme Limited',
            'amount' => '400.00',
            'currency' => 'USD',
            'paid_on' => '2026-09-18',
            'reference' => 'TRF-400',
            'receipt' => UploadedFile::fake()->create('transfer-receipt.pdf', 120, 'application/pdf'),
        ],
    );

    $receipt = ProjectPaymentReceipt::query()->firstOrFail();
    $response->assertRedirect(route('admin.project-payments.show', $invoice));

    expect($receipt->receipt_path)->toStartWith('project-payments/receipts/'.$project->id.'/')
        ->and($receipt->receipt_original_name)->toBe('transfer-receipt.pdf')
        ->and($receipt->project_payment_invoice_id)->toBe($invoice->id)
        ->and($receipt->uploaded_by)->toBe($admin->id)
        ->and(Schema::getColumnListing('project_payment_receipts'))->not->toContain('blob', 'content', 'file_data', 'receipt_data');
    Storage::disk(PersistentUploadStorage::DISK)->assertExists($receipt->receipt_path);

    $this->withSession(paymentReceiptSession($admin))
        ->get(route('admin.project-payments.show', $invoice))
        ->assertOk()
        ->assertSee('transfer-receipt.pdf')
        ->assertSee('Payment evidence')
        ->assertSee('Add payment receipt')
        ->assertDontSee('The file is stored privately outside the application release');

    $this->withSession(paymentReceiptSession($admin))
        ->get(route('admin.project-payments.receipts.preview', $receipt))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf')
        ->assertHeader('x-content-type-options', 'nosniff');

    $this->withSession(paymentReceiptSession($admin))
        ->get(route('admin.project-payments.receipts.download', $receipt))
        ->assertOk()
        ->assertDownload('transfer-receipt.pdf');
});

it('supports staff part payments and rejects links belonging to another project', function () {
    Storage::fake(PersistentUploadStorage::DISK);
    $admin = User::factory()->create(['role' => AdminAccess::ROLE_ADMIN, 'permissions' => []]);
    $project = Project::query()->create(['project_number' => 'PAY-STAFF-1', 'name' => 'Staff Payment Project', 'status' => 'active']);
    $otherProject = Project::query()->create(['project_number' => 'PAY-STAFF-2', 'name' => 'Other Project', 'status' => 'active']);
    $contract = paymentReceiptContract($project);
    $otherContract = paymentReceiptContract($otherProject, 'SC-RECEIPT-002');

    $this->withSession(paymentReceiptSession($admin))
        ->get(route('admin.project-payments.receipts.create', [
            'project' => $project->project_number,
            'staff_contract' => $contract->id,
        ]))
        ->assertOk()
        ->assertSee('value="staff_part_payment" selected', false)
        ->assertSee($contract->staff_name);

    $this->withSession(paymentReceiptSession($admin))->post(
        route('admin.project-payments.receipts.store'),
        [
            'project_id' => $project->id,
            'payment_type' => 'staff_part_payment',
            'staff_contract_id' => $contract->id,
            'counterparty' => $contract->staff_name,
            'amount' => '400000',
            'currency' => 'NGN',
            'paid_on' => '2026-09-18',
            'receipt' => UploadedFile::fake()->image('staff-payment.jpg'),
        ],
    )->assertRedirect(route('admin.project-payments.receipts.index'));

    $this->assertDatabaseHas('project_payment_receipts', [
        'project_id' => $project->id,
        'staff_contract_id' => $contract->id,
        'payment_type' => 'staff_part_payment',
        'amount' => 400000,
    ]);

    $this->withSession(paymentReceiptSession($admin))->from(route('admin.project-payments.receipts.create'))->post(
        route('admin.project-payments.receipts.store'),
        [
            'project_id' => $project->id,
            'payment_type' => 'staff_full_payment',
            'staff_contract_id' => $otherContract->id,
            'amount' => '800000',
            'currency' => 'NGN',
            'paid_on' => '2026-09-18',
            'receipt' => UploadedFile::fake()->image('wrong-project.jpg'),
        ],
    )->assertRedirect(route('admin.project-payments.receipts.create'))
        ->assertSessionHasErrors('staff_contract_id');

    expect(ProjectPaymentReceipt::query()->count())->toBe(1);
});

it('allows invoice-permitted subaccounts to review receipts but only full admins can upload them', function () {
    Storage::fake(PersistentUploadStorage::DISK);
    $admin = User::factory()->create(['role' => AdminAccess::ROLE_ADMIN, 'permissions' => []]);
    $subaccount = User::factory()->create(['role' => AdminAccess::ROLE_SUBACCOUNT, 'permissions' => ['invoices']]);
    $project = Project::query()->create(['project_number' => 'PAY-ACCESS', 'name' => 'Access Project', 'status' => 'active']);

    $this->withSession(paymentReceiptSession($subaccount))
        ->get(route('admin.project-payments.receipts.index'))
        ->assertOk()
        ->assertDontSee('Upload receipt');

    $this->withSession(paymentReceiptSession($subaccount))
        ->get(route('admin.project-payments.receipts.create'))
        ->assertForbidden();

    $this->withSession(paymentReceiptSession($subaccount))->post(
        route('admin.project-payments.receipts.store'),
        [
            'project_id' => $project->id,
            'payment_type' => 'other_payment',
            'amount' => '100',
            'currency' => 'USD',
            'paid_on' => '2026-09-18',
            'receipt' => UploadedFile::fake()->image('receipt.jpg'),
        ],
    )->assertForbidden();

    expect(ProjectPaymentReceipt::query()->count())->toBe(0);
});
