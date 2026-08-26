<?php

use App\Models\LuxuryQuote;
use App\Models\Project;
use App\Models\StaffContract;
use App\Models\StaffContractDocumentContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('creates an invoice-linked staff contract with price terms and signing details', function () {
    $sessionKey = config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated');
    $session = [
        $sessionKey => true,
        'luxury_quote_admin_email' => 'admin@example.com',
    ];

    $invoice = LuxuryQuote::query()->create([
        'quote_number' => 'TT-INV-STAFF-001',
        'template' => 'obsidian',
        'project_category' => 'Digital Product',
        'company_name' => 'Asterion Holdings',
        'recipient_name' => 'Nora Kelvin',
        'project_title' => 'Northstar Client Portal',
        'executive_summary' => 'A project engagement for the Northstar client portal.',
        'investment_amount' => 1250000,
        'exchange_rate' => 1400,
        'timeline' => '8 weeks',
        'valid_until' => '2026-09-30',
        'scope_items' => ['Product interface direction and responsive delivery'],
    ]);

    $this
        ->withSession($session)
        ->get(route('admin.staff-contracts.create'))
        ->assertOk()
        ->assertSee('Create an invoice-linked staff contract')
        ->assertSee('Existing invoice')
        ->assertSee('TT-INV-STAFF-001')
        ->assertDontSee('Start the project record alongside the contract')
        ->assertSee('data-rich-editor', false)
        ->assertDontSee('Signing section')
        ->assertDontSee('Company signatory')
        ->assertDontSee('Staff signatory')
        ->assertDontSee('Company signed date')
        ->assertDontSee('Staff signed date')
        ->assertDontSee('Staff email')
        ->assertDontSee('Staff phone')
        ->assertDontSee('Engagement starts')
        ->assertDontSee('Internal notes')
        ->assertDontSee('Company / engaging party');

    $payload = [
        'luxury_quote_id' => $invoice->id,
        'status' => 'pending_signature',
        'staff_name' => 'Amina Stone',
        'staff_role' => 'Product Designer',
        'currency' => 'USD',
        'agreed_fee' => '583.94',
        'payment_terms' => '50% on signing and 50% after approved final handoff.',
        'scope_of_work' => '<p><strong>Create the product interface direction</strong>, responsive screens, and handoff documentation for the Northstar portal.</p><ul><li>Responsive interface delivery</li></ul>',
        'terms' => '<p>All project information is confidential.</p><p>Work created for the project is assigned to the company after payment.</p>',
        'company_name' => 'Turance Technologies',
    ];

    $response = $this
        ->withSession($session)
        ->post(route('admin.staff-contracts.store'), $payload);

    $project = Project::query()->where('name', 'Northstar Client Portal')->first();
    $contract = StaffContract::query()->where('staff_name', 'Amina Stone')->first();

    expect($project)->not->toBeNull();
    expect($contract)->not->toBeNull()
        ->and($contract->project_id)->toBe($project->id)
        ->and($contract->luxury_quote_id)->toBe($invoice->id)
        ->and($contract->scope_of_work)->toContain('<strong>Create the product interface direction</strong>')
        ->and((float) $contract->agreed_fee)->toBe(583.94)
        ->and($contract->status)->toBe('pending_signature');

    $response->assertRedirect(route('admin.staff-contracts.show', $contract));

    $this
        ->withSession($session)
        ->get(route('admin.staff-contracts.show', $contract))
        ->assertOk()
        ->assertSee('Contract Staff Agreement')
        ->assertSee('Northstar Client Portal')
        ->assertSee('USD 583.94')
        ->assertSee('Naira equivalent')
        ->assertSee('NGN 817,516.00')
        ->assertSee('Create the product interface direction')
        ->assertSee('RC No. 3646478')
        ->assertSee('Contract staff')
        ->assertSee('Acceptance and signatures')
        ->assertDontSee('Shared project workspace');

    $this
        ->withSession($session)
        ->get(route('admin.staff-contracts.index'))
        ->assertOk()
        ->assertSee('Northstar Client Portal')
        ->assertSee('Invoice-linked staff documents')
        ->assertSee('Invoice TT-INV-STAFF-001');

    $pdfResponse = $this
        ->withSession($session)
        ->get(route('admin.staff-contracts.pdf', $contract));

    expect($pdfResponse->headers->get('content-type'))->toContain('application/pdf');
    expect(substr($pdfResponse->getContent(), 0, 4))->toBe('%PDF');
    expect(strlen($pdfResponse->getContent()))->toBeGreaterThan(1000);
});

it('updates a staff contract without losing its invoice and project relationships', function () {
    $sessionKey = config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated');
    $session = [
        $sessionKey => true,
        'luxury_quote_admin_email' => 'admin@example.com',
    ];

    $project = Project::query()->create([
        'project_number' => 'TT-PRJ-TEST-001',
        'name' => 'Existing Product Build',
        'client_company' => 'Client Co',
        'status' => 'active',
    ]);

    $invoice = LuxuryQuote::query()->create([
        'quote_number' => 'TT-INV-STAFF-002',
        'template' => 'obsidian',
        'project_category' => 'Digital Product',
        'company_name' => 'Client Co',
        'project_title' => 'Existing Product Build',
        'executive_summary' => 'A project engagement for an existing product build.',
        'investment_amount' => 600000,
        'timeline' => '6 weeks',
        'valid_until' => '2026-09-30',
        'scope_items' => ['Frontend build and review'],
    ]);

    $contract = StaffContract::query()->create([
        'project_id' => $project->id,
        'luxury_quote_id' => $invoice->id,
        'contract_number' => 'TT-STAFF-TEST-001',
        'status' => 'draft',
        'staff_name' => 'Daniel Cole',
        'staff_role' => 'Frontend Engineer',
        'currency' => 'NGN',
        'agreed_fee' => 250000,
        'payment_terms' => 'Paid monthly after approved work is delivered.',
        'scope_of_work' => 'Build and review the assigned frontend features for the project.',
        'terms' => 'Confidentiality and intellectual property terms apply throughout the engagement.',
        'company_name' => 'Turance Technologies',
        'company_signatory_name' => 'Tony Stark',
        'company_signed_date' => '2026-08-21',
        'staff_signatory_name' => 'Daniel Cole',
        'staff_signed_date' => '2026-08-22',
    ]);

    $payload = [
        'luxury_quote_id' => $invoice->id,
        'status' => 'active',
        'staff_name' => 'Daniel Cole',
        'staff_role' => 'Senior Frontend Engineer',
        'currency' => 'NGN',
        'agreed_fee' => '325000',
        'payment_terms' => 'Paid monthly after approved work is delivered and accepted.',
        'scope_of_work' => 'Build, test, and review the assigned frontend features for the project.',
        'terms' => 'Confidentiality and intellectual property terms apply throughout the engagement and after completion.',
        'company_name' => 'Turance Technologies',
    ];

    $this
        ->withSession($session)
        ->put(route('admin.staff-contracts.update', $contract), $payload)
        ->assertRedirect(route('admin.staff-contracts.show', $contract));

    $contract->refresh();

    expect($contract->project_id)->toBe($project->id)
        ->and($contract->luxury_quote_id)->toBe($invoice->id)
        ->and($contract->contract_number)->toBe('TT-STAFF-TEST-001')
        ->and($contract->staff_role)->toBe('Senior Frontend Engineer')
        ->and((float) $contract->agreed_fee)->toBe(325000.0)
        ->and($contract->company_signatory_name)->toBe('Tony Stark')
        ->and($contract->staff_signatory_name)->toBe('Daniel Cole');
});

it('stores an automatically named private signed copy and locks the contract', function () {
    Storage::fake('local');

    $sessionKey = config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated');
    $session = [
        $sessionKey => true,
        'luxury_quote_admin_email' => 'admin@example.com',
    ];

    $project = Project::query()->create([
        'project_number' => 'TT-PRJ-PROOF-001',
        'name' => 'Signed Proof Project',
        'status' => 'active',
    ]);

    $invoice = LuxuryQuote::query()->create([
        'quote_number' => 'TT-INV-PROOF-001',
        'template' => 'obsidian',
        'project_category' => 'Digital Product',
        'company_name' => 'Client Co',
        'project_title' => 'Signed Proof Project',
        'executive_summary' => 'A project engagement with a stored signed proof copy.',
        'investment_amount' => 500000,
        'timeline' => '4 weeks',
        'valid_until' => '2026-09-30',
        'scope_items' => ['Product design and delivery'],
    ]);

    $contract = StaffContract::query()->create([
        'project_id' => $project->id,
        'luxury_quote_id' => $invoice->id,
        'contract_number' => 'TT-STAFF-PROOF-001',
        'status' => 'signed',
        'staff_name' => 'Amina Stone',
        'staff_role' => 'Product Designer',
        'currency' => 'NGN',
        'agreed_fee' => 250000,
        'payment_terms' => 'Paid after each approved monthly delivery.',
        'scope_of_work' => 'Design and review the assigned product interface work.',
        'terms' => 'Confidentiality and ownership terms apply throughout the engagement.',
        'company_name' => 'Turance Technologies',
    ]);

    $payload = [
        'luxury_quote_id' => $invoice->id,
        'status' => 'signed',
        'staff_name' => 'Amina Stone',
        'staff_role' => 'Product Designer',
        'currency' => 'NGN',
        'agreed_fee' => '250000',
        'payment_terms' => 'Paid after each approved monthly delivery.',
        'scope_of_work' => 'Design and review the assigned product interface work.',
        'terms' => 'Confidentiality and ownership terms apply throughout the engagement.',
        'company_name' => 'Turance Technologies',
    ];

    $firstFile = UploadedFile::fake()->create('signed-staff-contract.pdf', 240, 'application/pdf');

    $this
        ->withSession($session)
        ->put(route('admin.staff-contracts.update', $contract), [
            ...$payload,
            'signed_document' => $firstFile,
        ])
        ->assertRedirect(route('admin.staff-contracts.show', $contract));

    $contract->refresh();
    $firstPath = $contract->signed_document_path;

    expect($contract->hasSignedDocument())->toBeTrue()
        ->and($contract->signed_document_original_name)->toBe('tt-staff-proof-001-amina-stone-signed.pdf')
        ->and($contract->signed_document_mime)->toBe('application/pdf')
        ->and($firstPath)->toStartWith('staff-contracts/signed-documents/');

    expect(StaffContractDocumentContent::query()->where('staff_contract_id', $contract->id)->exists())->toBeTrue();
    Storage::disk('public')->assertMissing($firstPath);
    Storage::disk('local')->deleteDirectory('staff-contracts');

    $this
        ->withSession($session)
        ->get(route('admin.staff-contracts.show', $contract))
        ->assertOk()
        ->assertSee('Uploaded signed version')
        ->assertSee('tt-staff-proof-001-amina-stone-signed.pdf')
        ->assertSee('Locked after signed upload')
        ->assertDontSee('Edit Contract');

    $this
        ->withSession($session)
        ->get(route('admin.staff-contracts.signed-document', $contract))
        ->assertOk()
        ->assertHeader('Content-Disposition', 'attachment; filename="tt-staff-proof-001-amina-stone-signed.pdf"');

    $this
        ->withSession($session)
        ->get(route('admin.staff-contracts.signed-document.preview', $contract))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertHeader('Content-Disposition', 'inline; filename="tt-staff-proof-001-amina-stone-signed.pdf"');

    $this
        ->withSession($session)
        ->get(route('admin.staff-contracts.edit', $contract))
        ->assertRedirect(route('admin.staff-contracts.show', $contract))
        ->assertSessionHas('error', 'This contract is locked because its signed document has already been uploaded.');

    $secondFile = UploadedFile::fake()->create('signed-staff-contract-v2.pdf', 280, 'application/pdf');

    $this
        ->withSession($session)
        ->put(route('admin.staff-contracts.update', $contract), [
            ...$payload,
            'staff_role' => 'Changed after signing',
            'signed_document' => $secondFile,
        ])
        ->assertRedirect(route('admin.staff-contracts.show', $contract));

    $contract->refresh();

    expect($contract->staff_role)->toBe('Product Designer')
        ->and($contract->signed_document_original_name)->toBe('tt-staff-proof-001-amina-stone-signed.pdf')
        ->and($contract->signed_document_path)->toBe($firstPath);

    expect(StaffContractDocumentContent::query()->where('staff_contract_id', $contract->id)->exists())->toBeTrue();
});

it('does not lock a contract when its recorded signed file is missing', function () {
    Storage::fake('local');

    $contract = StaffContract::query()->create([
        'project_id' => Project::query()->create([
            'project_number' => 'TT-PRJ-MISSING-001',
            'name' => 'Missing Signed File Project',
            'status' => 'active',
        ])->id,
        'contract_number' => 'TT-STAFF-MISSING-001',
        'status' => 'signed',
        'staff_name' => 'Amina Stone',
        'staff_role' => 'Product Designer',
        'currency' => 'NGN',
        'agreed_fee' => 250000,
        'payment_terms' => 'Paid after each approved monthly delivery.',
        'scope_of_work' => 'Design and review the assigned product interface work.',
        'terms' => 'Confidentiality and ownership terms apply throughout the engagement.',
        'company_name' => 'Turance Technologies',
        'signed_document_path' => 'staff-contracts/signed-documents/missing.pdf',
    ]);

    expect($contract->hasSignedDocument())->toBeFalse();
});
