@extends('admin.layouts.app')

@section('title', 'Sub-accounts | Admin')

@section('content')
    <style>
        .access-chip {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(184, 134, 11, 0.1);
            color: var(--accent-soft);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .access-chip--off {
            background: rgba(185, 74, 61, 0.1);
            color: var(--danger);
        }

        .permission-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }

        .permission-list span {
            padding: 4px 8px;
            border-radius: 999px;
            background: rgba(47, 128, 84, 0.1);
            color: var(--success);
            font-size: 11px;
        }
    </style>

    <section class="panel hero-banner">
        <div>
            <span class="eyebrow">Team access</span>
            <h1>Sub-accounts with clear boundaries.</h1>
            <p>Create separate admin logins for your team and choose exactly which workspace areas each person can use.</p>
            <div class="hero-actions">
                <a class="button" href="{{ route('admin.subaccounts.create') }}">Create Sub-account</a>
                <a class="ghost-button" href="{{ route('admin.profile') }}">Back to Profile</a>
            </div>
        </div>
        <div class="hero-callout">
            <div class="callout-card">
                <span class="metric-label">Active sub-accounts</span>
                <strong>{{ number_format($subaccounts->where('is_active', true)->count()) }}</strong>
                <p>Separate logins currently allowed into the admin workspace.</p>
            </div>
        </div>
    </section>

    <section class="panel panel-padded" style="margin-top: 24px;">
        <div class="panel-head">
            <span class="eyebrow">Access register</span>
            <h2>Team accounts</h2>
            <p>Suspended accounts cannot sign in. Updating permissions takes effect on their next request.</p>
        </div>

        <div class="table-wrap">
            <table class="quote-table">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Workspace access</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subaccounts as $subaccount)
                        <tr>
                            <td>
                                <strong>{{ $subaccount->name }}</strong>
                                <span>{{ $subaccount->email }}</span>
                                @if ($subaccount->job_title)
                                    <span>{{ $subaccount->job_title }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ count($subaccount->permissions ?? []) }} areas</strong>
                                <div class="permission-list">
                                    @foreach ($subaccount->permissions ?? [] as $permission)
                                        @if (isset($permissionOptions[$permission]))
                                            <span>{{ $permissionOptions[$permission]['label'] }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <span class="access-chip {{ $subaccount->is_active ? '' : 'access-chip--off' }}">
                                    {{ $subaccount->is_active ? 'Active' : 'Suspended' }}
                                </span>
                            </td>
                            <td>
                                <details class="action-menu">
                                    <summary>Actions</summary>
                                    <div class="action-menu-panel">
                                        <a href="{{ route('admin.subaccounts.edit', $subaccount) }}">Edit access</a>
                                        <form method="POST" action="{{ route('admin.subaccounts.toggle', $subaccount) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit">{{ $subaccount->is_active ? 'Suspend account' : 'Activate account' }}</button>
                                        </form>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <strong>No sub-accounts created yet.</strong>
                                <span>Create a separate login instead of sharing the primary admin credentials.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
