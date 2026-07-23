@extends('admin.layouts.app')

@section('title', 'Admin Login | Invoice Generator')

@section('content')
    <div class="auth-grid">
        <section class="auth-brand-panel" aria-labelledby="auth-title">
            <div class="auth-brand-lockup">
                <span class="admin-brand-mark">TT</span>
                <div class="auth-brand-copy">
                    <strong>{{ config('luxury-quotes.brand.studio_name', 'Turance Technologies') }}</strong>
                    <span>Invoice Admin</span>
                </div>
            </div>

            <div class="auth-hero-copy">
                <span class="eyebrow">Secure Workspace</span>
                <h1 id="auth-title">Control room for client invoices.</h1>
                <p>Sign in to review requests, prepare polished quotes, and export client-ready documents from one focused workspace.</p>
            </div>

            <div class="auth-checklist" aria-label="Workspace safeguards">
                <span>Environment credentials</span>
                <span>Private admin session</span>
                <span>Export-ready records</span>
            </div>

            <div class="auth-meta-strip">
                <div class="auth-meta-item">
                    <strong>{{ $templateCount }}</strong>
                    <span>invoice templates</span>
                </div>
                <div class="auth-meta-item">
                    <strong>PDF</strong>
                    <span>client exports</span>
                </div>
                <div class="auth-meta-item">
                    <strong>{{ config('luxury-quotes.brand.currency', 'NGN') }}</strong>
                    <span>default currency</span>
                </div>
            </div>
        </section>

        <section class="auth-card">
            @if (! $configured)
                <div class="alert alert-warning">
                    Set <code>LUXURY_INVOICE_ADMIN_EMAIL</code> and <code>LUXURY_INVOICE_ADMIN_PASSWORD</code> in your
                    environment file before attempting to sign in.
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="auth-card-head">
                <span class="eyebrow">Private Login</span>
                <h2>Welcome back</h2>
                <p>Enter the admin credentials configured for this installation.</p>
            </div>

            <form method="POST" action="{{ route('admin.login.store') }}" class="auth-form">
                @csrf

                <div class="field">
                    <label for="email">Email address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        placeholder="admin@company.com" autocomplete="username" required>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password"
                        placeholder="Enter password" autocomplete="current-password" required>
                </div>

                <div class="field">
                    <button type="submit" class="button">Sign in</button>
                </div>
            </form>

            <p class="auth-support-note">Access is limited to the credentials stored in the application environment.</p>
        </section>
    </div>
@endsection
