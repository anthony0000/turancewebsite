<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\AdminAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->session()->get($this->sessionKey())) {
            return redirect()->route('admin.home');
        }

        return view('admin.auth.login', [
            'configured' => $this->credentialsConfigured(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = Str::lower(trim($validated['email']));
        $user = User::query()->where('email', $email)->first();

        if ($user) {
            if (! in_array($user->role, [AdminAccess::ROLE_ADMIN, AdminAccess::ROLE_SUBACCOUNT], true)
                || ! $user->is_active
                || ! Hash::check($validated['password'], (string) $user->password)) {
                return back()
                    ->withErrors([
                        'email' => 'Those admin credentials did not match an active admin account.',
                    ])
                    ->onlyInput('email');
            }

            $request->session()->regenerate();
            AdminAccess::signIn($request, $user);

            return redirect()
                ->route($user->role === AdminAccess::ROLE_ADMIN ? 'admin.quotes.index' : 'admin.home')
                ->with('status', 'Welcome back, '.$user->name.'.');
        }

        if (! $this->credentialsConfigured()) {
            return back()
                ->withErrors([
                    'email' => 'Set LUXURY_INVOICE_ADMIN_EMAIL and LUXURY_INVOICE_ADMIN_PASSWORD before using this area.',
                ])
                ->onlyInput('email');
        }

        $configuredEmail = (string) config('luxury-quotes.admin.email');
        $configuredPassword = (string) config('luxury-quotes.admin.password');

        $emailMatches = $email === mb_strtolower($configuredEmail);
        $passwordMatches = hash_equals($configuredPassword, $validated['password']);

        if (! $emailMatches || ! $passwordMatches) {
            return back()
                ->withErrors([
                    'email' => 'Those admin credentials did not match the configured invoice generator access.',
                ])
                ->onlyInput('email');
        }

        $user = User::query()->create([
            'name' => 'Administrator',
            'email' => $email,
            'password' => $validated['password'],
            'role' => AdminAccess::ROLE_ADMIN,
            'permissions' => AdminAccess::permissionKeys(),
            'is_active' => true,
        ]);

        $request->session()->regenerate();
        AdminAccess::signIn($request, $user);

        return redirect()
            ->route('admin.quotes.index')
            ->with('status', 'Invoice generator unlocked.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        AdminAccess::signOut($request);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->with('status', 'Admin session closed.');
    }

    private function credentialsConfigured(): bool
    {
        return filled(config('luxury-quotes.admin.email'))
            && filled(config('luxury-quotes.admin.password'));
    }

    private function sessionKey(): string
    {
        return (string) config(
            'luxury-quotes.admin.session_key',
            'luxury_quote_admin_authenticated'
        );
    }
}
