<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
            return redirect()
                ->route('admin.login')
                ->with('admin_notice', 'Sign in to access the invoice generator.');
        }

        return $next($request);
    }
}
