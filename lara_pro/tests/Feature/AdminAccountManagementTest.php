<?php

use App\Models\User;
use App\Support\AdminAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function adminAccountSession(User $user): array
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

it('lets the full admin update profile settings and create limited sub-accounts', function () {
    $admin = User::factory()->create([
        'name' => 'Primary Admin',
        'email' => 'owner@example.com',
        'role' => AdminAccess::ROLE_ADMIN,
        'permissions' => AdminAccess::permissionKeys(),
        'is_active' => true,
    ]);

    $session = adminAccountSession($admin);

    $this
        ->withSession($session)
        ->get(route('admin.profile'))
        ->assertOk()
        ->assertSee('Profile details');

    $this
        ->withSession($session)
        ->put(route('admin.profile.update'), [
            'name' => 'Updated Admin',
            'email' => 'updated-owner@example.com',
            'job_title' => 'Managing Director',
            'phone' => '+234 800 000 0000',
        ])
        ->assertRedirect(route('admin.profile'));

    $admin->refresh();

    expect($admin->name)->toBe('Updated Admin')
        ->and($admin->email)->toBe('updated-owner@example.com')
        ->and($admin->job_title)->toBe('Managing Director');

    $this
        ->withSession(adminAccountSession($admin))
        ->post(route('admin.subaccounts.store'), [
            'name' => 'Finance Assistant',
            'email' => 'finance@example.com',
            'job_title' => 'Finance Assistant',
            'phone' => '+234 811 111 1111',
            'password' => 'Subaccount123!',
            'password_confirmation' => 'Subaccount123!',
            'permissions' => ['invoices', 'archive'],
        ])
        ->assertRedirect(route('admin.subaccounts.index'));

    $subaccount = User::query()->where('email', 'finance@example.com')->first();

    expect($subaccount)->not->toBeNull()
        ->and($subaccount->role)->toBe(AdminAccess::ROLE_SUBACCOUNT)
        ->and($subaccount->permissions)->toBe(['invoices', 'archive'])
        ->and($subaccount->is_active)->toBeTrue();

    $this
        ->withSession(adminAccountSession($admin))
        ->get(route('admin.subaccounts.index'))
        ->assertOk()
        ->assertSee('Finance Assistant')
        ->assertSee('Invoices')
        ->assertSee('Archive');

    $this
        ->withSession(adminAccountSession($admin))
        ->patch(route('admin.subaccounts.toggle', $subaccount))
        ->assertRedirect(route('admin.subaccounts.index'));

    expect($subaccount->fresh()->is_active)->toBeFalse();
});

it('enforces limited sub-account access on the server', function () {
    $subaccount = User::factory()->create([
        'name' => 'Invoice Operator',
        'email' => 'operator@example.com',
        'password' => 'Operator123!',
        'role' => AdminAccess::ROLE_SUBACCOUNT,
        'permissions' => ['invoices'],
        'is_active' => true,
    ]);

    $response = $this
        ->withSession(adminAccountSession($subaccount))
        ->get(route('admin.quotes.index'));

    $response->assertOk();

    $this
        ->withSession(adminAccountSession($subaccount))
        ->get(route('admin.proposals.index'))
        ->assertForbidden();

    $this
        ->withSession(adminAccountSession($subaccount))
        ->get(route('admin.subaccounts.index'))
        ->assertForbidden();

    $this
        ->withSession(adminAccountSession($subaccount))
        ->get(route('admin.profile'))
        ->assertOk()
        ->assertSee('Limited sub-account');
});

it('allows a sub-account to sign in with its own credentials', function () {
    $subaccount = User::factory()->create([
        'email' => 'operator-login@example.com',
        'password' => 'Operator123!',
        'role' => AdminAccess::ROLE_SUBACCOUNT,
        'permissions' => ['invoices'],
        'is_active' => true,
    ]);

    $this
        ->post(route('admin.login.store'), [
            'email' => $subaccount->email,
            'password' => 'Operator123!',
        ])
        ->assertRedirect(route('admin.home'));

    expect(session('admin_user_id'))->toBe($subaccount->id)
        ->and(session('admin_role'))->toBe(AdminAccess::ROLE_SUBACCOUNT);
});
