<?php

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectPaymentInvoice;
use App\Models\User;
use App\Support\AdminAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function projectPaymentSession(User $user): array
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

function projectPaymentPayload(Project $project, array $overrides = []): array
{
    return array_merge([
        'project_id' => $project->id,
        'issue_date' => '2026-09-18',
        'due_date' => null,
        'payment_terms' => 'Due upon receipt',
        'original_invoice_number' => 'TT-INV-20260824-001',
        'client_company' => '3H Consulting Limited',
        'client_name' => 'John Akinwumi',
        'client_title' => 'Managing Director',
        'client_email' => 'john@example.com',
        'client_phone' => '+234 812 369 1569',
        'project_title' => '3hJobs Platform Redesign',
        'project_category' => 'SaaS Platform Delivery',
        'description' => 'Progress payment for the approved platform redesign and development scope.',
        'payment_description' => '40% Project Payment',
        'currency' => 'USD',
        'local_currency' => 'NGN',
        'contract_value' => '2729.00',
        'completion_percentage' => '40',
        'tax_rate' => '0',
        'exchange_rate' => '1370',
        'bank_name' => 'ZENITH BANK PLC',
        'account_name' => 'TURANCE TECHNOLOGIES',
        'account_number' => '1313068587',
        'prepared_by' => 'Turance Technologies',
        'notes' => 'Thank you for your continued partnership.',
    ], $overrides);
}

it('builds an editable payment invoice from project completion and calculates totals on the server', function () {
    $admin = User::factory()->create(['role' => AdminAccess::ROLE_ADMIN, 'permissions' => []]);
    $client = Client::query()->create(['name' => 'John Akinwumi', 'company' => '3H Consulting Limited', 'email' => 'john@example.com']);
    $project = Project::query()->create([
        'project_number' => '3HJOBS',
        'name' => '3hJobs Platform Redesign',
        'client_id' => $client->id,
        'client_name' => $client->name,
        'client_company' => $client->company,
        'status' => 'active',
        'budget' => 2729,
        'progress_mode' => 'manual',
        'progress_override' => 40,
        'description' => 'Approved platform redesign and development scope.',
    ]);

    $this->withSession(projectPaymentSession($admin))
        ->get(route('admin.project-payments.create', ['project' => $project->project_number]))
        ->assertOk()
        ->assertSee('3hJobs Platform Redesign')
        ->assertSee('40% complete')
        ->assertSee('Generate payment invoice');

    $response = $this->withSession(projectPaymentSession($admin))
        ->post(route('admin.project-payments.store'), projectPaymentPayload($project, [
            'subtotal_amount' => '1.00',
            'total_amount' => '1.00',
        ]));

    $invoice = ProjectPaymentInvoice::query()->firstOrFail();
    $response->assertRedirect(route('admin.project-payments.show', $invoice));

    expect((float) $invoice->subtotal_amount)->toBe(1091.60)
        ->and((float) $invoice->tax_amount)->toBe(0.0)
        ->and((float) $invoice->total_amount)->toBe(1091.60)
        ->and($invoice->status)->toBe('unpaid')
        ->and($invoice->invoice_number)->toStartWith('TT-INV-20260918-');

    $this->withSession(projectPaymentSession($admin))
        ->get(route('admin.project-payments.index'))
        ->assertOk()
        ->assertSee('Turn project progress')
        ->assertSee('Collection rate')
        ->assertSee('View payment receipts')
        ->assertDontSee('Project payments, tied directly')
        ->assertSee($invoice->invoice_number);

    $this->withSession(projectPaymentSession($admin))
        ->put(route('admin.project-payments.update', $invoice), projectPaymentPayload($project, [
            'completion_percentage' => '50',
            'payment_description' => '50% Project Payment',
            'client_company' => '3H Technology Limited',
        ]))
        ->assertRedirect(route('admin.project-payments.show', $invoice));

    $invoice->refresh();
    expect((float) $invoice->subtotal_amount)->toBe(1364.50)
        ->and($invoice->client_company)->toBe('3H Technology Limited');

    $this->withSession(projectPaymentSession($admin))
        ->get(route('admin.project-payments.show', $invoice))
        ->assertOk()
        ->assertSee('Mark as paid')
        ->assertSee('$1,364.50')
        ->assertSee('NGN 1,869,365');
});

it('lets only a full admin mark a project payment invoice as paid', function () {
    $admin = User::factory()->create(['role' => AdminAccess::ROLE_ADMIN, 'permissions' => []]);
    $staff = User::factory()->create(['role' => AdminAccess::ROLE_SUBACCOUNT, 'permissions' => ['invoices']]);
    $project = Project::query()->create(['project_number' => 'PAYMENT', 'name' => 'Payment Workflow', 'status' => 'active']);

    $this->withSession(projectPaymentSession($admin))
        ->post(route('admin.project-payments.store'), projectPaymentPayload($project));

    $invoice = ProjectPaymentInvoice::query()->firstOrFail();

    $this->withSession(projectPaymentSession($staff))
        ->patch(route('admin.project-payments.paid', $invoice), ['paid_on' => '2026-09-18'])
        ->assertForbidden();

    $this->withSession(projectPaymentSession($admin))
        ->patch(route('admin.project-payments.paid', $invoice), [
            'paid_on' => '2026-09-18',
            'payment_reference' => 'TRF-0042',
            'payment_notes' => 'Confirmed in the business account.',
        ])
        ->assertRedirect(route('admin.project-payments.show', $invoice));

    $invoice->refresh();
    expect($invoice->isPaid())->toBeTrue()
        ->and($invoice->paid_by)->toBe($admin->id)
        ->and($invoice->payment_reference)->toBe('TRF-0042');

    $this->withSession(projectPaymentSession($admin))
        ->get(route('admin.project-payments.show', $invoice))
        ->assertOk()
        ->assertSee('Payment received')
        ->assertSee('pd-paid-status', false)
        ->assertSee('TRF-0042')
        ->assertDontSee('Mark as paid');
});

it('exports the project payment invoice as a branded pdf', function () {
    $admin = User::factory()->create(['role' => AdminAccess::ROLE_ADMIN, 'permissions' => []]);
    $project = Project::query()->create(['project_number' => 'PDFPAY', 'name' => 'PDF Payment', 'status' => 'active']);

    $this->withSession(projectPaymentSession($admin))
        ->post(route('admin.project-payments.store'), projectPaymentPayload($project));

    $invoice = ProjectPaymentInvoice::query()->firstOrFail();

    $this->withSession(projectPaymentSession($admin))
        ->get(route('admin.project-payments.pdf', $invoice))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});
