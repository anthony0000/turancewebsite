<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->session()->get($this->sessionKey())) {
            return redirect()->route('admin.quotes.index');
        }

        return view('admin.auth.login', [
            'configured' => $this->credentialsConfigured(),
            'templateCount' => count(config('luxury-quotes.templates', [])),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! $this->credentialsConfigured()) {
            return back()
                ->withErrors([
                    'email' => 'Set LUXURY_INVOICE_ADMIN_EMAIL and LUXURY_INVOICE_ADMIN_PASSWORD before using this area.',
                ])
                ->onlyInput('email');
        }

        $configuredEmail = (string) config('luxury-quotes.admin.email');
        $configuredPassword = (string) config('luxury-quotes.admin.password');

        $emailMatches = mb_strtolower($validated['email']) === mb_strtolower($configuredEmail);
        $passwordMatches = hash_equals($configuredPassword, $validated['password']);

        if (! $emailMatches || ! $passwordMatches) {
            return back()
                ->withErrors([
                    'email' => 'Those admin credentials did not match the configured invoice generator access.',
                ])
                ->onlyInput('email');
        }

        $request->session()->put($this->sessionKey(), true);
        $request->session()->put('luxury_quote_admin_email', $configuredEmail);
        $request->session()->regenerate();

        return redirect()
            ->route('admin.quotes.index')
            ->with('status', 'Invoice generator unlocked.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->forget([
            $this->sessionKey(),
            'luxury_quote_admin_email',
        ]);
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
