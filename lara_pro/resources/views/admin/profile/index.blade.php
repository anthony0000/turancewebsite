@extends('admin.layouts.app')

@section('title', 'Profile & Settings | Admin')

@section('content')
    <section class="panel hero-banner">
        <div>
            <span class="eyebrow">Account centre</span>
            <h1>Admin profile & settings.</h1>
            <p>Keep your identity and security details current. Your account access level is shown alongside your profile.</p>
        </div>
        <div class="hero-callout">
            <div class="callout-card">
                <span class="metric-label">Access level</span>
                <strong>{{ $isFullAdmin ? 'Full administrator' : 'Sub-account' }}</strong>
                <p>{{ $isFullAdmin ? 'All admin modules and account controls.' : 'Access is limited by the permissions assigned to this account.' }}</p>
            </div>
        </div>
    </section>

    @if ($isFullAdmin)
        <div class="admin-mobile-page-action">
            <a class="ghost-button" href="{{ route('admin.subaccounts.index') }}">Manage Sub-accounts</a>
        </div>
    @endif

    @if ($errors->any())
        <div class="validation-summary" style="margin-top: 24px; padding: 16px 18px; border: 1px solid rgba(185, 74, 61, 0.3); border-radius: 16px; background: rgba(185, 74, 61, 0.08); color: var(--danger);">
            <strong>Review the account details.</strong>
            <ul style="margin: 8px 0 0 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-grid" style="margin-top: 24px; align-items: start;">
        <form class="panel panel-padded" method="POST" action="{{ route('admin.profile.update') }}">
            @csrf
            @method('PUT')

            <div class="panel-head">
                <span class="eyebrow">Profile details</span>
                <h2>How your admin account appears</h2>
                <p>These details are used in the admin workspace and account menu.</p>
            </div>

            <div class="form-grid">
                <div class="field">
                    <label for="name">Full name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required maxlength="255">
                </div>
                <div class="field">
                    <label for="email">Email address</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="255">
                </div>
                <div class="field">
                    <label for="job_title">Job title</label>
                    <input id="job_title" type="text" name="job_title" value="{{ old('job_title', $user->job_title) }}" maxlength="255" placeholder="e.g. Managing Director">
                </div>
                <div class="field">
                    <label for="phone">Phone number</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="80" placeholder="+234 ...">
                </div>
            </div>

            <div class="form-section" style="margin-top: 28px;">
                <div>
                    <span class="eyebrow">Security</span>
                    <h3>Change password</h3>
                    <p class="section-copy">Leave these fields blank to keep your current password.</p>
                </div>
                <div class="form-grid">
                    <div class="field">
                        <label for="password">New password</label>
                        <input id="password" type="password" name="password" minlength="8" maxlength="72" autocomplete="new-password">
                    </div>
                    <div class="field">
                        <label for="password_confirmation">Confirm new password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" minlength="8" maxlength="72" autocomplete="new-password">
                    </div>
                </div>
            </div>

            <div class="wizard-actions" style="margin-top: 28px;">
                <span class="admin-pill">{{ ucfirst($user->role) }} account</span>
                <button class="button" type="submit">Save Profile Settings</button>
            </div>
        </form>

        <aside class="sticky-stack">
            <section class="panel panel-padded">
                <span class="eyebrow">Account summary</span>
                <h2 class="panel-title">{{ $user->name }}</h2>
                <div class="meta-list" style="margin-top: 18px;">
                    <div class="meta-item">
                        <span>Email</span>
                        <strong>{{ $user->email }}</strong>
                    </div>
                    <div class="meta-item">
                        <span>Role</span>
                        <strong>{{ $isFullAdmin ? 'Full administrator' : 'Limited sub-account' }}</strong>
                    </div>
                    <div class="meta-item">
                        <span>Created</span>
                        <strong>{{ optional($user->created_at)->format('M d, Y') ?: 'Not available' }}</strong>
                    </div>
                </div>
            </section>

            @if ($isFullAdmin)
                <section class="panel panel-padded">
                    <span class="eyebrow">Team access</span>
                    <h3 class="panel-title">Create limited accounts</h3>
                    <p class="section-copy">Give team members access to only the workspaces they need, without sharing the main admin credentials.</p>
                    <a class="button" href="{{ route('admin.subaccounts.index') }}" style="margin-top: 18px;">Manage Sub-accounts</a>
                </section>
            @endif
        </aside>
    </div>
@endsection
