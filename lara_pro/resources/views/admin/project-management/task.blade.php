@extends('admin.project-management.layout', ['title' => $task->task_key])

@section('project-content')
    <div class="pm-task-view" data-read-only="{{ $canManageWorkspace ? '0' : '1' }}">
        <section class="panel pm-hero pm-task-hero">
            <div>
                <span class="eyebrow">{{ $task->task_key }} &middot; {{ Str::headline($task->type) }}</span>
                <h2>{{ $task->title }}</h2>
                <p>{{ $project->name }} &middot; {{ $task->column?->name ?: Str::headline($task->status) }}</p>
            </div>
            <div class="pm-task-hero__actions">
                <a class="ghost-button" href="{{ route('admin.project-management.board', $project) }}">Back to board</a>
                @if ($task->completed_at)
                    <span class="pm-chip pm-chip--success">Done {{ $task->completed_at->format('M d, Y') }}</span>
                    @if ($canManageTaskStatus)
                        <form method="POST" action="{{ route('admin.project-management.tasks.reopen', $task) }}" data-ajax-form data-ajax-task-state="reopen" data-ajax-next-action="{{ route('admin.project-management.tasks.complete', $task) }}">
                            @csrf @method('PATCH')
                            <button class="ghost-button" type="submit">Mark not completed</button>
                        </form>
                    @endif
                @else
                    <form method="POST" action="{{ route('admin.project-management.tasks.complete', $task) }}" data-ajax-form data-ajax-task-state="complete" data-ajax-next-action="{{ route('admin.project-management.tasks.reopen', $task) }}">
                        @csrf @method('PATCH')
                        <button class="button" type="submit">Mark as done</button>
                    </form>
                @endif
                @if ($canManageWorkspace && ! $task->completed_at)
                    <form method="POST" action="{{ route('admin.project-management.tasks.destroy', $task) }}" data-ajax-form data-ajax-delete-task="1" data-ajax-success-url="{{ route('admin.project-management.board', $project) }}" data-ajax-confirm="Delete this task permanently? This cannot be undone.">
                        @csrf @method('DELETE')
                        <button class="ghost-button pm-danger-button" type="submit">Delete task</button>
                    </form>
                @endif
            </div>
        </section>

        <div class="pm-detail-grid">
            <div class="pm-stack">
                <section class="panel pm-panel pm-task-edit-panel">
                    <div class="pm-panel-head">
                        <div>
                            <span class="eyebrow">Task details</span>
                            <h3>Keep the work moving</h3>
                            <p>Update the essentials here. Less-used planning details are tucked away below.</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.project-management.tasks.update', $task) }}" data-ajax-form data-ajax-task-edit>
                        @csrf @method('PUT')
                        <div class="pm-form-grid pm-task-form-primary">
                            <div class="field-full"><label for="task-title">Task title</label><input id="task-title" name="title" value="{{ $task->title }}" required></div>
                            <div class="field"><label for="task-status">List</label><select id="task-status" name="board_column_id">@foreach ($columns as $column)<option value="{{ $column->id }}" @selected($task->board_column_id == $column->id)>{{ $column->name }}</option>@endforeach</select></div>
                            <div class="field"><label for="task-priority">Priority</label><select id="task-priority" name="priority">@foreach ($priorities as $priority)<option value="{{ $priority }}" @selected($task->priority === $priority)>{{ Str::headline($priority) }}</option>@endforeach</select></div>
                            <div class="field"><label for="task-assignee">Assigned to</label><select id="task-assignee" name="assignee_id"><option value="">Unassigned</option>@foreach ($assignees as $assignee)<option value="{{ $assignee->id }}" @selected($task->assignee_id == $assignee->id)>{{ $assignee->name }}</option>@endforeach</select></div>
                            <div class="field"><label for="task-due">Due date</label><input id="task-due" type="date" name="due_on" value="{{ optional($task->due_on)->format('Y-m-d') }}"></div>
                            <div class="field-full"><label for="task-description">Description</label><textarea id="task-description" name="description" rows="5" placeholder="What needs to happen?">{{ $task->description }}</textarea></div>
                        </div>

                        <details class="pm-advanced-fields">
                            <summary>More task settings <span>Type, planning, estimates and labels</span></summary>
                            <div class="pm-form-grid">
                                <div class="field"><label for="task-type">Type</label><select id="task-type" name="type">@foreach ($taskTypes as $type)<option value="{{ $type }}" @selected($task->type === $type)>{{ Str::headline($type) }}</option>@endforeach</select></div>
                                <div class="field"><label for="task-reporter">Reporter</label><select id="task-reporter" name="reporter_id"><option value="">Unassigned</option>@foreach ($members as $member)<option value="{{ $member->id }}" @selected($task->reporter_id == $member->id)>{{ $member->name }}</option>@endforeach</select></div>
                                <div class="field"><label for="task-start">Start date</label><input id="task-start" type="date" name="starts_on" value="{{ optional($task->starts_on)->format('Y-m-d') }}"></div>
                                <div class="field"><label for="task-milestone">Milestone</label><select id="task-milestone" name="milestone_id"><option value="">None</option>@foreach ($milestones as $milestone)<option value="{{ $milestone->id }}" @selected($task->milestone_id == $milestone->id)>{{ $milestone->title }}</option>@endforeach</select></div>
                                <div class="field"><label for="task-sprint">Sprint</label><select id="task-sprint" name="sprint_id"><option value="">Backlog</option>@foreach ($sprints as $sprint)<option value="{{ $sprint->id }}" @selected($task->sprint_id == $sprint->id)>{{ $sprint->name }}</option>@endforeach</select></div>
                                <div class="field"><label for="task-hours">Estimated hours</label><input id="task-hours" type="number" min="0" step="0.25" name="estimated_hours" value="{{ $task->estimated_hours }}"></div>
                                <div class="field"><label for="task-points">Story points</label><input id="task-points" type="number" min="0" step="0.5" name="story_points" value="{{ $task->story_points }}"></div>
                                @if ($labels->isNotEmpty())<div class="field-full"><label>Labels</label><div class="pm-actions pm-task-label-options">@foreach ($labels as $label)<label class="pm-chip"><input type="checkbox" name="label_ids[]" value="{{ $label->id }}" @checked($task->labels->contains($label->id))>{{ $label->name }}</label>@endforeach</div></div>@endif
                            </div>
                        </details>
                        <div class="pm-form-actions"><button class="button" type="submit">Save changes</button></div>
                    </form>
                </section>

                <section class="panel pm-panel">
                    <div class="pm-panel-head"><div><h3>Subtasks</h3><p>Break the work down without losing the parent task.</p></div><span class="pm-chip">{{ $task->subtasks->whereNotNull('completed_at')->count() }}/{{ $task->subtasks->count() }}</span></div>
                    <div class="pm-list">@forelse ($task->subtasks as $subtask)<div class="pm-list-item"><div><strong><a href="{{ route('admin.project-management.tasks.show', $subtask) }}">{{ $subtask->task_key }} &middot; {{ $subtask->title }}</a></strong><span>{{ $subtask->assignee?->name ?: 'Unassigned' }}</span></div><span class="pm-chip">{{ $subtask->completed_at ? 'Done' : 'Open' }}</span></div>@empty<div class="pm-empty">No subtasks yet. Create one below.</div>@endforelse</div>
                </section>

                <section class="panel pm-panel">
                    <div class="pm-panel-head"><div><h3>Comments</h3><p>Keep decisions and updates with the task.</p></div></div>
                    <div class="pm-list">@forelse ($task->comments as $comment)<div class="pm-list-item"><div><strong>{{ $comment->user?->name ?: 'Team member' }}</strong><span>{{ $comment->created_at->diffForHumans() }}</span><p style="margin:7px 0 0; white-space:pre-wrap; line-height:1.5">{{ $comment->body }}</p></div></div>@empty<div class="pm-empty">No comments yet.</div>@endforelse</div>
                    <form method="POST" action="{{ route('admin.project-management.tasks.comments.store', ['project' => $project, 'task' => $task]) }}" style="margin-top:15px">@csrf<div class="field"><label for="task-comment">Add comment</label><textarea id="task-comment" name="body" rows="3" required placeholder="Share an update with the team."></textarea></div><div class="pm-form-actions"><button class="button" type="submit">Post comment</button></div></form>
                </section>
            </div>

            <aside class="pm-stack">
                <section class="panel pm-panel"><div class="pm-panel-head"><div><h3>Time tracking</h3><p>{{ number_format($task->timeEntries->sum('minutes') / 60, 1) }} hours logged.</p></div></div>@if ($task->timeEntries->whereNull('ended_at')->isNotEmpty())<form method="POST" action="{{ route('admin.project-management.tasks.timer.stop', $task) }}">@csrf<button class="button" type="submit">Stop active timer</button></form>@else<form method="POST" action="{{ route('admin.project-management.tasks.timer.start', $task) }}">@csrf<button class="ghost-button" type="submit">Start timer</button></form>@endif<form method="POST" action="{{ route('admin.project-management.tasks.time.store', $task) }}" style="margin-top:14px">@csrf<div class="field"><label for="time-minutes">Manual minutes</label><input id="time-minutes" type="number" min="1" max="1440" name="minutes" required></div><div class="field" style="margin-top:9px"><label for="time-description">Work description</label><textarea id="time-description" name="description" rows="2"></textarea></div><div class="pm-form-actions"><button class="ghost-button" type="submit">Log time</button></div></form><div class="pm-list" style="margin-top:14px">@foreach ($task->timeEntries->take(8) as $entry)<div class="pm-list-item"><div><strong>{{ number_format($entry->minutes / 60, 1) }}h &middot; {{ $entry->user?->name ?: 'Team member' }}</strong><span>{{ $entry->description ?: 'No description' }}</span></div></div>@endforeach</div></section>
                <section class="panel pm-panel"><div class="pm-panel-head"><div><h3>Files</h3><p>Private project attachments.</p></div></div><div class="pm-list">@forelse ($task->attachments as $attachment)<div class="pm-list-item"><div><strong>{{ $attachment->original_name }}</strong><span>{{ $attachment->uploader?->name ?: 'Team member' }} &middot; {{ number_format(($attachment->size ?: 0) / 1024, 1) }} KB</span></div><a class="pm-icon-link" href="{{ route('admin.project-management.attachments.download', $attachment) }}">Download</a></div>@empty<div class="pm-empty">No files attached.</div>@endforelse</div><form method="POST" action="{{ route('admin.project-management.tasks.attachments.store', $task) }}" enctype="multipart/form-data" style="margin-top:14px">@csrf<div class="field"><label for="task-file">Upload attachment</label><input id="task-file" type="file" name="file" required></div><div class="pm-form-actions"><button class="ghost-button" type="submit">Upload</button></div></form></section>
                <section class="panel pm-panel"><div class="pm-panel-head"><div><h3>Checklists</h3><p>Small, explicit completion steps.</p></div></div>@forelse ($task->checklists as $checklist)<div class="pm-list" style="margin-bottom:14px"><strong>{{ $checklist->title }}</strong>@foreach ($checklist->items as $item)<form class="pm-list-item" method="POST" action="{{ route('admin.project-management.checklist-items.toggle', $item) }}">@csrf @method('PATCH')<label style="display:flex;gap:8px;align-items:center"><input type="checkbox" onchange="this.form.submit()" @checked($item->is_complete)> <span style="text-decoration:{{ $item->is_complete ? 'line-through' : 'none' }}">{{ $item->content }}</span></label></form>@endforeach</div>@empty<div class="pm-empty">No checklist yet.</div>@endforelse<form class="pm-inline-form" method="POST" action="{{ route('admin.project-management.tasks.checklists.store', $task) }}">@csrf<div class="field"><label for="checklist-title">New checklist</label><input id="checklist-title" name="title" placeholder="Launch checks" required></div><button class="ghost-button" type="submit">Add</button></form>@if ($task->checklists->isNotEmpty())<form class="pm-inline-form" method="POST" action="{{ route('admin.project-management.tasks.checklist-items.store', $task) }}" style="margin-top:10px">@csrf<div class="field"><label for="checklist-id">Checklist</label><select id="checklist-id" name="checklist_id">@foreach ($task->checklists as $checklist)<option value="{{ $checklist->id }}">{{ $checklist->title }}</option>@endforeach</select></div><div class="field"><label for="checklist-content">Item</label><input id="checklist-content" name="content" required></div><button class="ghost-button" type="submit">Add item</button></form>@endif</section>
            </aside>
        </div>

        <section class="panel pm-panel pm-subtask-create"><div class="pm-panel-head"><div><h3>Create a subtask</h3><p>Add a smaller piece of work without leaving this task.</p></div></div><form method="POST" action="{{ route('admin.project-management.tasks.store', $project) }}">@csrf<input type="hidden" name="parent_task_id" value="{{ $task->id }}"><input type="hidden" name="board_column_id" value="{{ $task->board_column_id }}"><div class="pm-inline-form"><div class="field"><label for="subtask-title">Title</label><input id="subtask-title" name="title" required></div><div class="field"><label for="subtask-type">Type</label><select id="subtask-type" name="type"><option value="task">Task</option><option value="bug">Bug</option><option value="improvement">Improvement</option></select></div><div class="field"><label for="subtask-priority">Priority</label><select id="subtask-priority" name="priority"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option></select></div><button class="ghost-button" type="submit">Create subtask</button></div></form></section>
    </div>
@endsection
