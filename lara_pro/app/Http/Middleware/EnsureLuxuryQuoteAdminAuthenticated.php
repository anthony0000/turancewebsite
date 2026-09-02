<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\AdminAccess;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureLuxuryQuoteAdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $sessionKey = (string) config(
            'luxury-quotes.admin.session_key',
            'luxury_quote_admin_authenticated'
        );

        if (! $request->session()->get($sessionKey)) {
            return redirect()->route('admin.login');
        }

        if (! $request->session()->has('admin_user_id') && filled($request->session()->get('luxury_quote_admin_email'))) {
            $email = (string) $request->session()->get('luxury_quote_admin_email');
            $user = User::query()->where('email', $email)->first();

            if (! $user && mb_strtolower($email) === mb_strtolower((string) config('luxury-quotes.admin.email'))) {
                $user = User::query()->create([
                    'name' => 'Administrator',
                    'email' => $email,
                    'password' => config('luxury-quotes.admin.password') ?: Str::random(32),
                    'role' => AdminAccess::ROLE_ADMIN,
                    'permissions' => AdminAccess::permissionKeys(),
                    'is_active' => true,
                ]);
            }

            if ($user && in_array($user->role, [AdminAccess::ROLE_ADMIN, AdminAccess::ROLE_SUBACCOUNT], true)) {
                if (! $user->is_active) {
                    $request->session()->invalidate();

                    return redirect()
                        ->route('admin.login')
                        ->with('admin_notice', 'This admin account is no longer active.');
                }

                AdminAccess::signIn($request, $user);
            }
        }

        if ($request->session()->has('admin_user_id')) {
            if ((bool) $request->session()->get('admin_remember', false)) {
                config()->set(
                    'session.lifetime',
                    (int) config('luxury-quotes.admin.remember_lifetime', 43200)
                );
            }

            $user = User::query()->find($request->session()->get('admin_user_id'));

            if (! $user || ! $user->is_active) {
                $request->session()->invalidate();

                return redirect()
                    ->route('admin.login')
                    ->with('admin_notice', 'This admin account is no longer active.');
            }

            $request->session()->put([
                'admin_user_name' => $user->name,
                'admin_role' => $user->role,
                'admin_permissions' => $user->permissions ?? [],
                'luxury_quote_admin_email' => $user->email,
            ]);
        }

        return $next($request);
    }
}
