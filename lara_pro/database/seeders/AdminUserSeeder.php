<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\AdminAccess;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the configured full admin account without resetting its password.
     */
    public function run(): void
    {
        $email = Str::lower(trim((string) config('luxury-quotes.admin.email')));
        $password = (string) config('luxury-quotes.admin.password');

        if ($email === '' || $password === '') {
            throw new RuntimeException(
                'Set LUXURY_QUOTE_ADMIN_EMAIL and LUXURY_QUOTE_ADMIN_PASSWORD before seeding the admin account.'
            );
        }

        $admin = User::query()->firstOrNew(['email' => $email]);
        $isNew = ! $admin->exists;

        $admin->fill([
            'name' => 'Administrator',
            'role' => AdminAccess::ROLE_ADMIN,
            'permissions' => AdminAccess::permissionKeys(),
            'is_active' => true,
        ]);

        if ($isNew) {
            $admin->password = $password;
            $admin->email_verified_at = now();
        }

        $admin->save();
    }
}
