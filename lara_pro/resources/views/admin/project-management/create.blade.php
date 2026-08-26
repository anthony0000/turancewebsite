@extends('admin.project-management.layout', ['title' => 'New Project'])

@section('project-content')
    <div class="pm-create-layout">
    <section class="panel pm-panel pm-create-main"><div class="pm-panel-head"><div><span class="eyebrow">Foundation</span><h3>Create a project workspace</h3><p>Set the delivery context once; the board, task keys, and team space will be created with it.</p></div></div>
        <form class="pm-create-form" method="POST" action="{{ route('admin.project-management.projects.store') }}">@csrf
            <div class="pm-form-grid">
                <div class="field"><label for="project-key">Project key</label><input id="project-key" name="project_key" value="{{ old('project_key') }}" placeholder="WEB" required><small>Short unique key used in task IDs.</small></div>
                <div class="field"><label for="project-name">Project name</label><input id="project-name" name="name" value="{{ old('name') }}" required></div>
                <div class="field"><label for="client-id">Client</label><select id="client-id" name="client_id"><option value="">Internal project</option>@foreach ($clients as $client)<option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>{{ $client->name }}{{ $client->company ? ' · '.$client->company : '' }}</option>@endforeach</select></div>
                <div class="field"><label for="manager-id">Project manager</label><select id="manager-id" name="project_manager_id"><option value="">Unassigned</option>@foreach ($members as $member)<option value="{{ $member->id }}" @selected(old('project_manager_id') == $member->id)>{{ $member->name }}</option>@endforeach</select></div>
                <div class="field"><label for="project-status">Status</label><select id="project-status" name="status">@foreach ($statuses as $status)<option value="{{ $status }}" @selected(old('status', 'planning') === $status)>{{ Str::headline($status) }}</option>@endforeach</select></div>
                <div class="field"><label for="project-priority">Priority</label><select id="project-priority" name="priority">@foreach ($priorities as $priority)<option value="{{ $priority }}" @selected(old('priority', 'medium') === $priority)>{{ Str::headline($priority) }}</option>@endforeach</select></div>
                <div class="field"><label for="starts-on">Start date</label><input id="starts-on" type="date" name="starts_on" value="{{ old('starts_on') }}"></div>
                <div class="field"><label for="ends-on">Due date</label><input id="ends-on" type="date" name="ends_on" value="{{ old('ends_on') }}"></div>
                <div class="field"><label for="budget">Budget <span class="pm-muted">(optional)</span></label><input id="budget" type="number" min="0" step="0.01" name="budget" value="{{ old('budget') }}"></div>
                <div class="field"><label for="estimated-hours">Estimated hours</label><input id="estimated-hours" type="number" min="0" step="0.25" name="estimated_hours" value="{{ old('estimated_hours') }}"></div>
                <div class="field-full"><label>Team members</label><div class="pm-member-grid">@foreach ($members as $member)<label class="pm-member-option"><input type="checkbox" name="member_ids[]" value="{{ $member->id }}" @checked(in_array($member->id, old('member_ids', [])))><span>{{ $member->name }}</span></label>@endforeach</div></div>
                <div class="field-full"><label for="description">Description</label><textarea id="description" name="description" rows="4" placeholder="What outcome is this project responsible for?">{{ old('description') }}</textarea></div>
                <div class="field-full"><label for="project-brief">Project brief</label><textarea id="project-brief" name="project_brief" rows="5" placeholder="Scope, constraints, delivery notes, and internal context.">{{ old('project_brief') }}</textarea></div>
                <div class="field"><label for="progress-mode">Progress calculation</label><select id="progress-mode" name="progress_mode"><option value="tasks">Calculate from completed tasks</option><option value="manual">Use authorized manual percentage</option></select></div>
                <div class="field"><label for="progress-override">Manual percentage</label><input id="progress-override" type="number" min="0" max="100" name="progress_override" value="{{ old('progress_override', 0) }}"></div>
            </div>
            <div class="pm-form-actions"><button class="button" type="submit">Create project</button><a class="ghost-button" href="{{ route('admin.project-management.projects') }}">Cancel</a></div>
        </form>
    </section>
    <aside class="pm-create-aside">
        <section class="panel pm-panel pm-create-summary">
            <span class="eyebrow">Workspace output</span>
            <h3>Ready for delivery</h3>
            <p>One setup creates the shared structure your team needs to start work.</p>
            <ul>
                <li><strong>Board</strong><span>Default workflow columns</span></li>
                <li><strong>Task IDs</strong><span>Generated from the project key</span></li>
                <li><strong>Team space</strong><span>Members and permissions connected</span></li>
            </ul>
        </section>
        <div class="pm-create-tip"><span class="pm-create-tip__mark">i</span><span>You can adjust columns, labels, and settings after creation.</span></div>
    </aside>
    </div>
@endsection
