@extends('admin.layouts.app')

@section('title', 'Sign in | Turance Technologies Admin Workspace')

@push('styles')
    <style>
        @include('admin.auth.partials.styles')
    </style>
@endpush

@section('content')
    <div class="auth-page" data-auth-page>
        <div class="auth-grid" data-component="auth-layout">
            <section class="auth-card" aria-labelledby="login-title">
                <div class="auth-card__inner">
                    <a class="auth-brand-lockup" href="{{ route('home') }}" aria-label="{{ config('luxury-quotes.brand.studio_name', 'Turance Technologies') }} home">
                        <span class="admin-brand-mark">
                            <img src="{{ asset('/assets/img/logo/favicon.png') }}" alt="" aria-hidden="true">
                        </span>
                        <span class="auth-brand-copy">
                            <strong>{{ config('luxury-quotes.brand.studio_name', 'Turance Technologies') }}</strong>
                            <span>Admin Workspace</span>
                        </span>
                    </a>

                    <div class="auth-card-head">
                        <span class="eyebrow">Secure workspace access</span>
                        <h1 id="login-title">Welcome back</h1>
                        <p>Sign in to manage projects, clients, and company operations.</p>
                    </div>

                    @if (! $configured)
                        <div class="auth-alert auth-config-alert" role="alert">
                            <strong>Admin access is not configured.</strong>
                            <span>Set the workspace credentials before using this area.</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="auth-alert alert-danger" id="auth-error" role="alert" tabindex="-1">
                            <strong>We couldn’t sign you in.</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login.store') }}" class="auth-form" data-auth-form>
                        @csrf

                        <div class="auth-field">
                            <label class="auth-field__label" for="email">Email address</label>
                            <div class="auth-input-shell">
                                <span class="auth-input-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                        <path d="m4 7 8 6 8-6"></path>
                                    </svg>
                                </span>
                                <input
                                    class="auth-input"
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    autocomplete="username"
                                    inputmode="email"
                                    autocapitalize="none"
                                    spellcheck="false"
                                    aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                    @if (! $errors->has('email') && ! old('email')) autofocus @endif
                                    required
                                >
                            </div>
                            @if ($errors->has('email'))
                                <p class="auth-field-error" id="email-error">{{ $errors->first('email') }}</p>
                            @endif
                        </div>

                        <div class="auth-field">
                            <label class="auth-field__label" for="password">Password</label>
                            <div class="auth-input-shell">
                                <span class="auth-input-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                                        <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                                    </svg>
                                </span>
                                <input
                                    class="auth-input auth-input--password"
                                    id="password"
                                    type="password"
                                    name="password"
                                    autocomplete="current-password"
                                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                    required
                                >
                                <button class="auth-password-toggle" type="button" data-password-toggle aria-controls="password" aria-pressed="false" aria-label="Show password">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M2.5 12s3.2-5 9.5-5 9.5 5 9.5 5-3.2 5-9.5 5-9.5-5-9.5-5Z"></path>
                                        <circle cx="12" cy="12" r="2.5"></circle>
                                    </svg>
                                </button>
                            </div>
                            @if ($errors->has('password'))
                                <p class="auth-field-error" id="password-error">{{ $errors->first('password') }}</p>
                            @endif
                        </div>

                        <div class="auth-form__options">
                            <label class="auth-check" for="remember">
                                <input id="remember" type="checkbox" name="remember" value="1" @checked(old('remember'))>
                                <span>Remember me</span>
                            </label>
                            <a class="auth-text-link" href="mailto:{{ config('luxury-quotes.brand.contact_email', 'hello@turancetechnologies.com') }}?subject={{ rawurlencode('Admin Workspace password reset') }}">Forgot password?</a>
                        </div>

                        <button class="auth-button" type="submit" data-auth-submit>
                            <span data-auth-submit-label>Sign in</span>
                            <span class="auth-button__spinner" aria-hidden="true"></span>
                        </button>
                    </form>

                    <p class="auth-support-note">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                        </svg>
                        <span>Protected workspace access. Your session is secured.</span>
                    </p>
                </div>
            </section>

            <section class="auth-visual-panel" aria-label="Turance Technologies team workspace">
                <picture>
                    <source
                        type="image/webp"
                        srcset="{{ asset('/assets/img/auth/workspace-team-800.webp') }} 800w, {{ asset('/assets/img/auth/workspace-team-1440.webp') }} 1440w"
                        sizes="(max-width: 760px) 100vw, 56vw"
                    >
                    <img
                        class="auth-visual-image"
                        src="{{ asset('/assets/img/auth/workspace-team.jpg') }}"
                        alt="Two colleagues collaborating around a laptop in a modern office."
                        width="1800"
                        height="1013"
                        fetchpriority="high"
                    >
                </picture>
                <span class="auth-visual-overlay" aria-hidden="true"></span>

                <div class="auth-visual-brand" aria-hidden="true">
                    <img src="{{ asset('/assets/img/logo/logo-label.png') }}" alt="">
                    <span>Admin Workspace</span>
                </div>

                <div class="auth-visual-content">
                    <div class="auth-visual-copy">
                        <span class="eyebrow">One clear view of the work</span>
                        <h2>Everything your team needs to operate with clarity.</h2>
                        <span class="auth-visual-rule" aria-hidden="true"></span>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const form = document.querySelector('[data-auth-form]');
            const submitButton = document.querySelector('[data-auth-submit]');
            const submitLabel = document.querySelector('[data-auth-submit-label]');
            const passwordToggle = document.querySelector('[data-password-toggle]');
            const password = document.getElementById('password');
            const authError = document.getElementById('auth-error');

            passwordToggle?.addEventListener('click', () => {
                if (!password) {
                    return;
                }

                const isVisible = password.type === 'text';
                password.type = isVisible ? 'password' : 'text';
                passwordToggle.setAttribute('aria-pressed', String(!isVisible));
                passwordToggle.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
            });

            form?.addEventListener('submit', () => {
                if (!form.checkValidity() || !submitButton) {
                    return;
                }

                submitButton.disabled = true;
                submitButton.setAttribute('aria-busy', 'true');
                form.classList.add('is-submitting');

                if (submitLabel) {
                    submitLabel.textContent = 'Signing in…';
                }
            });

            if (authError) {
                window.requestAnimationFrame(() => authError.focus());
            }

            window.addEventListener('pageshow', (event) => {
                if (!event.persisted || !form || !submitButton) {
                    return;
                }

                submitButton.disabled = false;
                submitButton.removeAttribute('aria-busy');
                form.classList.remove('is-submitting');

                if (submitLabel) {
                    submitLabel.textContent = 'Sign in';
                }
            });
        })();
    </script>
@endpush
