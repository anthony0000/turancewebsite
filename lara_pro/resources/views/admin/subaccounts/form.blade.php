@extends('admin.layouts.app')

@php
    $editing = filled($user);
    $pageTitle = $editing ? 'Edit sub-account access' : 'Create a sub-account';
    $formAction = $editing ? route('admin.subaccounts.update', $user) : route('admin.subaccounts.store');
    $selectedPermissions = old('permissions', $user?->permissions ?? []);
@endphp

@section('title', $pageTitle.' | Admin')

@section('content')
    @if ($errors->any())
        <div class="validation-summary" style="margin-bottom: 24px; padding: 16px 18px; border: 1px solid rgba(185, 74, 61, 0.3); border-radius: 16px; background: rgba(185, 74, 61, 0.08); color: var(--danger);">
            <strong>Review the sub-account details.</strong>
            <ul style="margin: 8px 0 0 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="panel panel-padded" method="POST" action="{{ $formAction }}">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="panel-head">
            <span class="eyebrow">{{ $editing ? 'Update access' : 'New team login' }}</span>
            <h2>{{ $pageTitle }}</h2>
            <p>Use a separate login and grant only the workspaces this person needs.</p>
        </div>

        <div class="form-grid">
            <div class="field">
                <label for="name">Full name</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user?->name) }}" required maxlength="255">
            </div>
            <div class="field">
                <label for="email">Email address</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user?->email) }}" required maxlength="255">
            </div>
            <div class="field">
                <label for="job_title">Job title</label>
                <input id="job_title" type="text" name="job_title" value="{{ old('job_title', $user?->job_title) }}" maxlength="255" placeholder="e.g. Finance Assistant">
            </div>
            <div class="field">
                <label for="phone">Phone number</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone', $user?->phone) }}" maxlength="80">
            </div>
            <div class="field">
                <label for="password">{{ $editing ? 'New password (optional)' : 'Password' }}</label>
                <input id="password" type="password" name="password" {{ $editing ? '' : 'required' }} minlength="8" maxlength="72" autocomplete="new-password">
            </div>
            <div class="field">
                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" {{ $editing ? '' : 'required' }} minlength="8" maxlength="72" autocomplete="new-password">
            </div>
        </div>

        <section class="form-section" style="margin-top: 28px;">
            <div>
                <span class="eyebrow">Permissions</span>
                <h3>Choose workspace access</h3>
                <p class="section-copy">The account can only open the areas selected below. Profile settings remain available to the account owner.</p>
            </div>
            <div class="form-grid">
                @foreach ($permissionOptions as $key => $permission)
                    <label class="panel" style="display: flex; align-items: flex-start; gap: 12px; padding: 16px; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="{{ $key }}" @checked(in_array($key, $selectedPermissions, true)) style="width: auto; min-height: auto; margin-top: 3px;">
                        <span>
                            <strong>{{ $permission['label'] }}</strong>
                            <small style="display: block; margin-top: 4px; color: var(--muted); line-height: 1.5;">{{ $permission['description'] }}</small>
                        </span>
                    </label>
                @endforeach
            </div>
        </section>

        <div class="wizard-actions" style="margin-top: 28px;">
            <a class="ghost-button" href="{{ route('admin.subaccounts.index') }}">Cancel</a>
            <button class="button" type="submit">{{ $editing ? 'Save Access Changes' : 'Create Sub-account' }}</button>
        </div>
    </form>
@endsection
