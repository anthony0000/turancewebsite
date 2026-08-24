<?php

use App\Models\User;
use App\Support\AdminAccess;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('seeds one configured full admin account idempotently', function () {
    config()->set('luxury-quotes.admin.email', 'seed-admin@example.com');
    config()->set('luxury-quotes.admin.password', 'SeedAdmin123!');

    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    $admin = User::query()->where('email', 'seed-admin@example.com')->first();

    expect($admin)->not->toBeNull()
        ->and(User::query()->count())->toBe(1)
        ->and($admin->name)->toBe('Administrator')
        ->and($admin->role)->toBe(AdminAccess::ROLE_ADMIN)
        ->and($admin->permissions)->toBe(AdminAccess::permissionKeys())
        ->and($admin->is_active)->toBeTrue()
        ->and(Hash::check('SeedAdmin123!', $admin->password))->toBeTrue();

    $this
        ->post(route('admin.login.store'), [
            'email' => 'seed-admin@example.com',
            'password' => 'SeedAdmin123!',
        ])
        ->assertRedirect(route('admin.quotes.index'))
        ->assertSessionHas('luxury_quote_admin_authenticated', true);
});
