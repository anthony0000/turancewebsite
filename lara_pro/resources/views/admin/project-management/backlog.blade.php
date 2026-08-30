@extends('admin.project-management.layout', ['title' => 'Backlog'])

@section('project-content')
    @php
        $backlogCount = $tasks->total();
        $activeSprint = $sprints->firstWhere('status', 'active');
        $futureSprints = $sprints->where('status', 'future');
    @endphp

    <div class="pm-backlog-view" data-read-only="{{ $canManageWorkspace ? '0' : '1' }}">
        <section class="panel pm-hero pm-backlog-hero">
            <div>
                <span class="eyebrow">{{ $project->project_number }} · Planning workspace</span>
                <h2>Shape the next sprint with confidence.</h2>
                <p>Keep unscheduled work visible, then commit it when the team has enough context to deliver.</p>
            </div>
            <div class="pm-actions">
                <a class="button" href="{{ route('admin.project-management.board', $project) }}">Open board</a>
                <a class="ghost-button" href="{{ route('admin.project-management.sprints', $project) }}">Manage sprints</a>
            </div>
        </section>

        <section class="pm-backlog-overview" aria-label="Backlog overview">
            <div class="pm-backlog-stat pm-backlog-stat--accent">
                <span class="pm-backlog-stat__icon" aria-hidden="true">↗</span>
                <div><strong>{{ $backlogCount }}</strong><span>Unplanned tasks</span></div>
            </div>
            <div class="pm-backlog-stat">
                <span class="pm-backlog-stat__icon" aria-hidden="true">◎</span>
                <div><strong>{{ $futureSprints->count() }}</strong><span>Future sprints</span></div>
            </div>
            <div class="pm-backlog-stat">
                <span class="pm-backlog-stat__icon" aria-hidden="true">◌</span>
                <div><strong>{{ $activeSprint ? $activeSprint->name : 'None' }}</strong><span>Active sprint</span></div>
            </div>
        </section>

        <section class="panel pm-panel pm-backlog-panel">
            <div class="pm-backlog-panel__head">
                <div class="pm-panel-head">
                    <div>
                        <span class="pm-section-kicker">Ready for commitment</span>
                        <h3>Backlog</h3>
                        <p>{{ $backlogCount ? 'Review ownership and priority before adding work to a sprint.' : 'A clear backlog gives the team room to focus.' }}</p>
                    </div>
                </div>
                @if ($backlogCount)
                    <span class="pm-backlog-count"><strong>{{ $backlogCount }}</strong> {{ Str::plural('task', $backlogCount) }}</span>
                @endif
            </div>

            <div class="pm-backlog-table" role="table" aria-label="Tasks without a sprint">
                <div class="pm-backlog-row pm-backlog-row--head" role="row">
                    <span role="columnheader">Task</span>
                    <span role="columnheader">Priority</span>
                    <span role="columnheader">Owner</span>
                    <span role="columnheader">Commit to sprint</span>
                </div>
                @forelse ($tasks as $task)
                    @php
                        $taskInitials = $task->assignee
                            ? collect(preg_split('/\s+/', trim($task->assignee->name)))->filter()->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))->take(2)->join('')
                            : '—';
                    @endphp
                    <div class="pm-backlog-row" role="row">
                        <div class="pm-backlog-cell pm-backlog-cell--task" role="cell" data-label="Task">
                            <span class="pm-backlog-task-mark" aria-hidden="true">{{ Str::upper(Str::substr($task->priority, 0, 1)) }}</span>
                            <div>
                                <a class="pm-backlog-task-key" href="{{ route('admin.project-management.tasks.show', $task) }}">{{ $task->task_key }}</a>
                                <strong><a href="{{ route('admin.project-management.tasks.show', $task) }}">{{ $task->title }}</a></strong>
                                <span class="pm-backlog-task-meta">{{ Str::headline($task->status) }} · {{ optional($task->due_on)->format('M d, Y') ?: 'No due date' }}</span>
                            </div>
                        </div>
                        <div class="pm-backlog-cell" role="cell" data-label="Priority">
                            <span class="pm-chip {{ $task->priority === 'urgent' ? 'pm-chip--danger' : ($task->priority === 'high' ? 'pm-chip--warning' : '') }}">{{ Str::headline($task->priority) }}</span>
                        </div>
                        <div class="pm-backlog-cell pm-backlog-cell--owner" role="cell" data-label="Owner">
                            <span class="pm-avatar {{ $task->assignee ? '' : 'pm-avatar--empty' }}">{{ $taskInitials }}</span>
                            <span>{{ $task->assignee?->name ?: 'Unassigned' }}</span>
                        </div>
                        <div class="pm-backlog-cell pm-backlog-cell--action" role="cell" data-label="Commit to sprint">
                            @if ($canManageWorkspace)
                                <form class="pm-backlog-assign" method="POST" action="{{ route('admin.project-management.tasks.sprint', $task) }}">
                                    @csrf @method('PATCH')
                                    <label class="sr-only" for="backlog-sprint-{{ $task->id }}">Choose a sprint for {{ $task->task_key }}</label>
                                    <select id="backlog-sprint-{{ $task->id }}" name="sprint_id">
                                        <option value="">Choose sprint</option>
                                        @foreach ($sprints as $sprint)
                                            <option value="{{ $sprint->id }}">{{ $sprint->name }} · {{ Str::headline($sprint->status) }}</option>
                                        @endforeach
                                    </select>
                                    <button class="pm-assign-button" type="submit">Assign <span aria-hidden="true">→</span></button>
                                </form>
                            @else
                                <span class="pm-muted">Planning access required</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="pm-backlog-empty">
                        <span class="pm-backlog-empty__icon" aria-hidden="true">✓</span>
                        <div><strong>The backlog is clear.</strong><span>Every task is already committed to a sprint.</span></div>
                        <a class="pm-icon-link" href="{{ route('admin.project-management.board', $project) }}">View board →</a>
                    </div>
                @endforelse
            </div>
            @if ($tasks->hasPages())
                <div class="pm-backlog-pagination">{{ $tasks->links() }}</div>
            @endif
        </section>
    </div>
@endsection
