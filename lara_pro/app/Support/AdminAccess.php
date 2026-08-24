<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;

final class AdminAccess
{
    public const ROLE_ADMIN = 'admin';

    public const ROLE_SUBACCOUNT = 'subaccount';

    /** @var array<string, array{label: string, description: string}> */
    private const PERMISSIONS = [
        'invoices' => [
            'label' => 'Invoices',
            'description' => 'View, create, edit, and export invoices.',
        ],
        'proposals' => [
            'label' => 'Proposals',
            'description' => 'Build, edit, share, and export proposals.',
        ],
        'staff-contracts' => [
            'label' => 'Staff contracts',
            'description' => 'Create, edit, review, and export staff contracts.',
        ],
        'projects' => [
            'label' => 'Projects',
            'description' => 'Review project records and project-to-invoice relationships.',
        ],
        'project-files' => [
            'label' => 'Project files',
            'description' => 'Upload, download, preview, and securely share external project files.',
        ],
        'letters' => [
            'label' => 'Letterhead',
            'description' => 'Write and export letters and basic company documents.',
        ],
        'activity' => [
            'label' => 'Activity',
            'description' => 'Review traffic, leads, and invoice movement.',
        ],
        'insights' => [
            'label' => 'Insights',
            'description' => 'View demand, template, category, and pipeline insights.',
        ],
        'promotion' => [
            'label' => 'Promotion',
            'description' => 'Manage the live promotion and campaign settings.',
        ],
        'archive' => [
            'label' => 'Archive',
            'description' => 'Find saved invoices and export documents.',
        ],
    ];

    /**
     * @return array<string, array{label: string, description: string}>
     */
    public static function permissions(): array
    {
        return self::PERMISSIONS;
    }

    /** @return list<string> */
    public static function permissionKeys(): array
    {
        return array_keys(self::PERMISSIONS);
    }

    public static function currentUser(): ?User
    {
        $userId = session('admin_user_id');

        if (filled($userId)) {
            return User::query()->find($userId);
        }

        $email = session('luxury_quote_admin_email');

        return filled($email)
            ? User::query()->where('email', $email)->first()
            : null;
    }

    public static function role(): string
    {
        return (string) session('admin_role', self::ROLE_ADMIN);
    }

    public static function isFullAdmin(): bool
    {
        return self::role() === self::ROLE_ADMIN;
    }

    public static function can(string $permission): bool
    {
        if (self::isFullAdmin()) {
            return true;
        }

        return in_array($permission, self::permissionsForSession(), true);
    }

    /** @return list<string> */
    public static function permissionsForSession(): array
    {
        $permissions = session('admin_permissions', []);

        if (! is_array($permissions)) {
            return [];
        }

        return array_values(array_intersect($permissions, self::permissionKeys()));
    }

    public static function displayName(): string
    {
        return (string) session('admin_user_name', session('luxury_quote_admin_email', 'Admin'));
    }

    public static function email(): string
    {
        return (string) session('luxury_quote_admin_email', '');
    }

    public static function signIn(Request $request, User $user): void
    {
        $request->session()->put([
            'admin_user_id' => $user->id,
            'admin_user_name' => $user->name,
            'admin_role' => $user->role,
            'admin_permissions' => $user->permissions ?? [],
            'luxury_quote_admin_email' => $user->email,
            config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated') => true,
        ]);
    }

    public static function signOut(Request $request): void
    {
        $request->session()->forget([
            'admin_user_id',
            'admin_user_name',
            'admin_role',
            'admin_permissions',
            'luxury_quote_admin_email',
            config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated'),
        ]);
    }
}
