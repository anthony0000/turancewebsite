@extends('admin.project-management.layout', ['title' => 'Notifications'])

@section('project-content')
    @php($unreadCount = $notifications->getCollection()->whereNull('read_at')->count())

    <section class="panel pm-hero">
        <div>
            <span class="eyebrow">Team signals</span>
            <h2>Notifications</h2>
            <p>Assignments, task progress, mentions, and project updates stay tied to the work that needs your attention.</p>
        </div>
        @if ($unreadCount)<span class="pm-chip pm-chip--danger">{{ $unreadCount }} unread</span>@endif
    </section>

    <section class="panel pm-panel">
        <div class="pm-panel-head"><div><h3>Your activity</h3><p>Unread items are highlighted until you mark them as read.</p></div></div>
        <div class="pm-list pm-notifications-list">
            @forelse ($notifications as $notification)
                @php($data = json_decode($notification->data, true) ?: [])
                <div class="pm-list-item {{ $notification->read_at ? '' : 'is-unread' }}">
                    <div>
                        <span class="pm-notification-type">{{ Str::headline(str_replace(['.', '_'], ' ', $notification->type)) }}</span>
                        <strong class="pm-notification-message">{{ $data['message'] ?? 'Project activity' }}</strong>
                        <span class="pm-notification-meta">{{ \Illuminate\Support\Carbon::parse($notification->created_at)->diffForHumans() }} &middot; {{ $notification->read_at ? 'Read' : 'Unread' }}</span>
                    </div>
                    <div class="pm-actions">
                        @if (! empty($data['url']))<a class="pm-icon-link" href="{{ $data['url'] }}">Open</a>@endif
                        @if (! $notification->read_at)<form method="POST" action="{{ route('admin.project-management.notifications.read', $notification->id) }}">@csrf @method('PATCH')<button class="ghost-button" type="submit">Mark read</button></form>@endif
                    </div>
                </div>
            @empty
                <div class="pm-empty"><strong>You’re all caught up.</strong><span>New project activity will appear here.</span></div>
            @endforelse
        </div>
        <div style="margin-top:14px">{{ $notifications->links() }}</div>
    </section>
@endsection
