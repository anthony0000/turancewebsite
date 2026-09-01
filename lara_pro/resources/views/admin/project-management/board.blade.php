@extends('admin.project-management.layout', ['title' => 'Board'])

@section('project-content')
    @php
        $boardTasks = $tasksByColumn->flatten(1);
        $assigneeCounts = $boardTasks->filter(fn ($task) => $task->assignee_id)->countBy('assignee_id');
    @endphp

    <div class="pm-board-view" data-read-only="{{ $canManageWorkspace ? '0' : '1' }}">
        <section class="panel pm-hero pm-board-hero">
            <div>
                <span class="eyebrow">{{ $project->project_number }} · Project board</span>
                <h2>{{ $project->name }}</h2>
                <p>Move work through the lists, keep ownership visible, and give every task a clear next step.</p>
            </div>
            <div class="pm-actions">
                <a class="ghost-button" href="{{ route('admin.project-management.projects.summary', $project) }}">Project summary</a>
                @if ($canManageWorkspace)
                    <a class="button" href="#new-task">+ New task</a>
                @endif
            </div>
        </section>

        <section class="panel pm-board-toolbar" aria-label="Board controls">
            <div class="pm-board-toolbar__topline">
                <div>
                    <span class="pm-board-toolbar__eyebrow">Workspace view</span>
                    <strong>Plan, assign and deliver</strong>
                </div>
                <span class="pm-board-toolbar__hint"><span class="pm-board-toolbar__drag-mark" aria-hidden="true">⋮⋮</span> Drag cards between lists</span>
            </div>
            <div class="pm-board-toolbar__controls">
                <div class="pm-inline-form">
                    <div class="field">
                        <label for="board-assignee">Filter by member</label>
                        <select id="board-assignee" data-board-filter="assignee">
                            <option value="">Everyone</option>
                            @foreach ($assignees as $assignee)
                                <option value="{{ $assignee->id }}">{{ $assignee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="board-priority">Priority</label>
                        <select id="board-priority" data-board-filter="priority">
                            <option value="">Every priority</option>
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority }}">{{ Str::headline($priority) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label class="pm-chip pm-chip--check"><input type="checkbox" data-board-filter="overdue"> Overdue only</label>
                    <button class="ghost-button" type="button" data-reset-board-filters>Reset</button>
                </div>
            </div>
            <div class="pm-board-team" aria-label="Filter tasks by team member">
                <span class="pm-board-team__label">Team</span>
                <button class="pm-team-filter is-active" type="button" data-team-filter="" aria-pressed="true">Everyone <b>{{ $boardTasks->count() }}</b></button>
                @foreach ($assignees as $assignee)
                    @php
                        $initials = collect(preg_split('/\s+/', trim($assignee->name)))->filter()->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))->take(2)->join('');
                    @endphp
                    <button class="pm-team-filter" type="button" data-team-filter="{{ $assignee->id }}" aria-pressed="false">
                        <span class="pm-avatar" title="{{ $assignee->name }}">{{ $initials ?: 'TM' }}</span>
                        <span>{{ Str::limit($assignee->name, 18) }}</span>
                        <b>{{ $assigneeCounts->get($assignee->id, 0) }}</b>
                    </button>
                @endforeach
                <button class="pm-team-filter" type="button" data-team-filter="unassigned" aria-pressed="false"><span class="pm-avatar pm-avatar--empty">—</span><span>Unassigned</span><b>{{ $boardTasks->whereNull('assignee_id')->count() }}</b></button>
            </div>
        </section>

        <section class="pm-board pm-board--trello" data-project-board data-project-id="{{ $project->id }}" data-read-only="{{ $canManageWorkspace ? '0' : '1' }}" aria-label="{{ $project->name }} task board">
            @foreach ($columns as $column)
                @php $columnTasks = $tasksByColumn->get($column->id, collect()); @endphp
                <article class="pm-column" data-column-id="{{ $column->id }}" data-column-name="{{ $column->name }}" style="--column-color: {{ $column->color }};">
                    <header class="pm-column-head">
                        <div class="pm-column-title">
                            <span class="pm-column-dot" aria-hidden="true"></span>
                            <strong>{{ $column->name }}</strong>
                            <span class="pm-column-count" data-column-count>{{ $columnTasks->count() }}</span>
                        </div>
                        @if ($canManageWorkspace)
                            <a class="pm-column-add" href="#new-task" data-add-to-column="{{ $column->id }}" aria-label="Add task to {{ $column->name }}">+</a>
                        @endif
                    </header>
                    <div class="pm-column-list" data-column-list tabindex="0" role="list" aria-label="{{ $column->name }} tasks" aria-dropeffect="move">
                        @foreach ($columnTasks as $task)
                            @php
                                $taskInitials = $task->assignee
                                    ? collect(preg_split('/\s+/', trim($task->assignee->name)))->filter()->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))->take(2)->join('')
                                    : '—';
                            @endphp
                            <article class="pm-task-card" draggable="true" tabindex="0" role="listitem" data-task-card data-task-id="{{ $task->task_key }}" data-assignee="{{ $task->assignee_id ?: '' }}" data-priority="{{ $task->priority }}" data-overdue="{{ $task->is_overdue ? '1' : '0' }}">
                                <div class="pm-task-card__top">
                                    <span class="pm-task-key">{{ $task->task_key }}</span>
                                    <span class="pm-task-type">{{ Str::headline($task->type) }}</span>
                                    <span class="pm-task-grip" aria-hidden="true">⋮⋮</span>
                                </div>
                                <h4><a href="{{ route('admin.project-management.tasks.show', $task) }}">{{ $task->title }}</a></h4>
                                @if ($task->labels->isNotEmpty())
                                    <div class="pm-card-labels">
                                        @foreach ($task->labels as $label)
                                            <span style="--label-color: {{ $label->color }}">{{ $label->name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="pm-task-card__details">
                                    <span class="pm-task-due {{ $task->is_overdue ? 'is-overdue' : '' }}">{{ $task->is_overdue ? 'Overdue' : (optional($task->due_on)->format('M d') ?: 'No due date') }}</span>
                                    <span class="pm-chip {{ $task->priority === 'urgent' ? 'pm-chip--danger' : 'pm-chip--priority-'.$task->priority }}">{{ Str::headline($task->priority) }}</span>
                                </div>
                                <div class="pm-task-card__footer">
                                    <label class="pm-task-assignee" title="{{ $task->assignee?->name ?: 'Unassigned' }}">
                                        <span class="pm-avatar {{ $task->assignee ? '' : 'pm-avatar--empty' }}">{{ $taskInitials }}</span>
                                        <select data-card-assignee aria-label="Assign {{ $task->task_key }}">
                                            <option value="">Unassigned</option>
                                            @foreach ($assignees as $assignee)
                                                <option value="{{ $assignee->id }}" @selected((int) $task->assignee_id === (int) $assignee->id)>{{ $assignee->name }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <span class="pm-task-stats">
                                        @if ($task->comments_count)<span title="Comments">▢ {{ $task->comments_count }}</span>@endif
                                        @if ($task->subtasks_count)<span title="Subtasks">☷ {{ $task->completed_subtasks_count }}/{{ $task->subtasks_count }}</span>@endif
                                    </span>
                                </div>
                                <div class="pm-task-card__keyboard pm-actions" aria-label="Keyboard move controls">
                                    <button type="button" class="ghost-button" data-keyboard-move="previous" aria-label="Move {{ $task->task_key }} to previous column">← Previous</button>
                                    <button type="button" class="ghost-button" data-keyboard-move="next" aria-label="Move {{ $task->task_key }} to next column">Next →</button>
                                </div>
                            </article>
                        @endforeach
                        <div class="pm-drop-placeholder" data-drop-placeholder @if ($columnTasks->isNotEmpty()) hidden @endif>
                            <span aria-hidden="true">＋</span>
                            <strong>Drop a task here</strong>
                            <small>or add one with the plus button</small>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        @if ($canManageWorkspace)
            <div class="pm-grid-wide" id="new-task">
                <section class="panel pm-panel">
                    <div class="pm-panel-head"><div><h3>Create task</h3><p>Add the essentials now. You can refine the planning details later.</p></div></div>
                    <form method="POST" action="{{ route('admin.project-management.tasks.store', $project) }}">
                        @csrf
                        <div class="pm-form-grid pm-task-create-primary">
                            <div class="field-full"><label for="task-title">Title</label><input id="task-title" name="title" required placeholder="Prepare client review notes"></div>
                            <div class="field"><label for="task-column">Column</label><select id="task-column" name="board_column_id">@foreach ($columns as $column)<option value="{{ $column->id }}">{{ $column->name }}</option>@endforeach</select></div>
                            <div class="field"><label for="task-assignee">Assignee</label><select id="task-assignee" name="assignee_id"><option value="">Unassigned</option>@foreach ($assignees as $assignee)<option value="{{ $assignee->id }}">{{ $assignee->name }}</option>@endforeach</select></div>
                            <div class="field"><label for="task-due">Due date</label><input id="task-due" type="date" name="due_on"></div>
                            <div class="field-full"><label for="task-description">Description</label><textarea id="task-description" name="description" rows="4" placeholder="Keep the acceptance context clear."></textarea></div>
                        </div>
                        <details class="pm-advanced-fields">
                            <summary>More options <span>Type, priority, estimates and labels</span></summary>
                            <div class="pm-form-grid">
                                <div class="field"><label for="task-type">Type</label><select id="task-type" name="type">@foreach ($taskTypes as $type)<option value="{{ $type }}">{{ Str::headline($type) }}</option>@endforeach</select></div>
                                <div class="field"><label for="task-priority">Priority</label><select id="task-priority" name="priority">@foreach ($priorities as $priority)<option value="{{ $priority }}" @selected($priority === 'medium')>{{ Str::headline($priority) }}</option>@endforeach</select></div>
                                <div class="field"><label for="task-hours">Estimated hours</label><input id="task-hours" type="number" min="0" step="0.25" name="estimated_hours"></div>
                                <div class="field"><label for="task-points">Story points</label><input id="task-points" type="number" min="0" step="0.5" name="story_points"></div>
                                @if ($labels->isNotEmpty())<div class="field-full"><label>Labels</label><div class="pm-actions" style="margin-top:8px">@foreach ($labels as $label)<label class="pm-chip" style="gap:6px"><input type="checkbox" name="label_ids[]" value="{{ $label->id }}">{{ $label->name }}</label>@endforeach</div></div>@endif
                            </div>
                        </details>
                        <div class="pm-form-actions"><button class="button" type="submit">Create task</button></div>
                    </form>
                </section>
                <div class="pm-stack">
                    <section class="panel pm-panel"><div class="pm-panel-head"><div><h3>Board columns</h3><p>Remove only empty columns.</p></div></div><div class="pm-list">@foreach ($columns as $column)<div class="pm-list-item"><div><strong>{{ $column->name }}</strong><span>{{ $column->is_done ? 'Completion column' : 'Open work' }}</span></div><form method="POST" action="{{ route('admin.project-management.columns.destroy', $column) }}">@csrf @method('DELETE')<button class="pm-icon-link" type="submit">Remove</button></form></div>@endforeach</div><form method="POST" action="{{ route('admin.project-management.columns.store', $project) }}" style="margin-top:14px">@csrf<div class="pm-inline-form"><div class="field"><label for="column-name">New column</label><input id="column-name" name="name" required placeholder="QA ready"></div><div class="field"><label for="column-color">Colour</label><input id="column-color" type="text" name="color" value="#b8860b"></div><button class="ghost-button" type="submit">Add</button></div></form></section>
                    <section class="panel pm-panel"><div class="pm-panel-head"><div><h3>Labels</h3><p>Keep board filters meaningful.</p></div></div><div class="pm-actions">@forelse ($labels as $label)<span class="pm-chip" style="border-color:{{ $label->color }}66">{{ $label->name }}</span>@empty<span class="pm-muted">No labels yet.</span>@endforelse</div><form method="POST" action="{{ route('admin.project-management.labels.store', $project) }}" style="margin-top:14px">@csrf<div class="pm-inline-form"><div class="field"><label for="label-name">New label</label><input id="label-name" name="name" required placeholder="Client review"></div><div class="field"><label for="label-color">Colour</label><input id="label-color" type="text" name="color" value="#b8860b"></div><button class="ghost-button" type="submit">Add</button></div></form></section>
                </div>
            </div>
        @else
            <section class="panel pm-panel pm-read-only-note"><strong>Read-only board</strong><span>You only see tasks assigned to you.</span></section>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const board = document.querySelector('[data-project-board]');
            if (!board || board.dataset.readOnly === '1') return;

            const csrf = document.querySelector('input[name="_token"]')?.value;
            const columns = [...board.querySelectorAll('[data-column-id]')];
            let dragState = null;
            let activeTeamFilter = '';

            const saveMove = async (card, column, position) => {
                const response = await fetch('{{ url('/admin/project-management/tasks') }}/' + card.dataset.taskId + '/move', {
                    method: 'PATCH',
                    headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf},
                    body: JSON.stringify({board_column_id: column.dataset.columnId, position}),
                });
                if (!response.ok) throw new Error('Task position could not be saved.');
            };

            const saveAssignee = async (card, assigneeId) => {
                const response = await fetch('{{ url('/admin/project-management/tasks') }}/' + card.dataset.taskId, {
                    method: 'PUT',
                    headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf},
                    body: JSON.stringify({assignee_id: assigneeId || null}),
                });
                if (!response.ok) throw new Error('Task assignment could not be saved.');
            };

            const updatePlaceholders = () => columns.forEach(column => {
                const list = column.querySelector('[data-column-list]');
                const placeholder = list.querySelector('[data-drop-placeholder]');
                if (placeholder) placeholder.hidden = Boolean(list.querySelector('[data-task-card]'));
            });

            const refreshCounts = () => {
                columns.forEach(column => {
                    const visibleCards = [...column.querySelectorAll('[data-task-card]')].filter(card => !card.hidden);
                    column.querySelector('[data-column-count]').textContent = visibleCards.length;
                });
                updatePlaceholders();
            };

            const cardAfterPointer = (list, pointerY) => [...list.querySelectorAll('[data-task-card]:not(.is-dragging):not([hidden])')]
                .reduce((closest, card) => {
                    const box = card.getBoundingClientRect();
                    const offset = pointerY - box.top - box.height / 2;
                    return offset < 0 && offset > closest.offset ? {offset, card} : closest;
                }, {offset: Number.NEGATIVE_INFINITY, card: null}).card;

            const restoreDraggedCard = () => {
                if (!dragState || dragState.saved) return;
                if (dragState.originNext && dragState.originNext.parentElement === dragState.originList) dragState.originList.insertBefore(dragState.card, dragState.originNext);
                else dragState.originList.append(dragState.card);
            };

            const clearDropState = () => columns.forEach(column => {
                column.classList.remove('is-drag-over');
                column.querySelector('[data-column-list]')?.removeAttribute('aria-dropeffect');
            });

            const moveByKeyboard = async (card, delta) => {
                const currentIndex = columns.findIndex(column => column.contains(card));
                const target = columns[currentIndex + delta];
                if (!target) return;
                const targetList = target.querySelector('[data-column-list]');
                targetList.append(card);
                try {
                    await saveMove(card, target, targetList.querySelectorAll('[data-task-card]').length - 1);
                    refreshCounts();
                } catch (error) {
                    window.location.reload();
                }
            };

            const bindCard = card => {
                card.addEventListener('dragstart', event => {
                    dragState = {card, originList: card.parentElement, originNext: card.nextElementSibling, saved: false, dropStarted: false};
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', card.dataset.taskId);
                    card.classList.add('is-dragging');
                    board.classList.add('is-dragging-card');
                });
                card.addEventListener('dragend', () => {
                    if (dragState && !dragState.dropStarted) restoreDraggedCard();
                    card.classList.remove('is-dragging');
                    board.classList.remove('is-dragging-card');
                    clearDropState();
                    dragState = null;
                    refreshCounts();
                });
                card.querySelector('[data-keyboard-move="previous"]')?.addEventListener('click', () => moveByKeyboard(card, -1));
                card.querySelector('[data-keyboard-move="next"]')?.addEventListener('click', () => moveByKeyboard(card, 1));
                card.querySelector('[data-card-assignee]')?.addEventListener('change', async event => {
                    event.stopPropagation();
                    const select = event.currentTarget;
                    card.classList.add('is-saving');
                    try {
                        await saveAssignee(card, select.value);
                        card.dataset.assignee = select.value;
                    } catch (error) {
                        window.location.reload();
                    } finally {
                        card.classList.remove('is-saving');
                    }
                    applyFilters();
                });
            };

            board.querySelectorAll('[data-task-card]').forEach(bindCard);

            columns.forEach(column => {
                const list = column.querySelector('[data-column-list]');
                list.addEventListener('dragover', event => {
                    event.preventDefault();
                    if (!dragState) return;
                    event.dataTransfer.dropEffect = 'move';
                    const after = cardAfterPointer(list, event.clientY);
                    if (after) list.insertBefore(dragState.card, after);
                    else list.append(dragState.card);
                    column.classList.add('is-drag-over');
                    list.setAttribute('aria-dropeffect', 'move');
                });
                list.addEventListener('dragleave', event => {
                    if (!list.contains(event.relatedTarget)) column.classList.remove('is-drag-over');
                });
                list.addEventListener('drop', async event => {
                    event.preventDefault();
                    if (!dragState) return;
                    const state = dragState;
                    const card = state.card;
                    state.dropStarted = true;
                    const position = [...list.querySelectorAll('[data-task-card]')].indexOf(card);
                    try {
                        await saveMove(card, column, Math.max(0, position));
                        state.saved = true;
                        refreshCounts();
                    } catch (error) {
                        if (dragState === state) restoreDraggedCard();
                        window.location.reload();
                    }
                });
            });

            const applyFilters = () => {
                const assignee = activeTeamFilter || document.querySelector('[data-board-filter="assignee"]').value;
                const priority = document.querySelector('[data-board-filter="priority"]').value;
                const overdue = document.querySelector('[data-board-filter="overdue"]').checked;
                board.querySelectorAll('[data-task-card]').forEach(card => {
                    const assigneeMatches = assignee === 'unassigned' ? !card.dataset.assignee : (!assignee || card.dataset.assignee === assignee);
                    card.hidden = !assigneeMatches || (priority && card.dataset.priority !== priority) || (overdue && card.dataset.overdue !== '1');
                });
                refreshCounts();
            };

            document.querySelectorAll('[data-board-filter]').forEach(input => input.addEventListener('change', () => {
                if (input.dataset.boardFilter === 'assignee') {
                    activeTeamFilter = input.value;
                    document.querySelectorAll('[data-team-filter]').forEach(button => {
                        const isActive = button.dataset.teamFilter === activeTeamFilter;
                        button.classList.toggle('is-active', isActive);
                        button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                    });
                }
                applyFilters();
            }));
            document.querySelectorAll('[data-team-filter]').forEach(button => button.addEventListener('click', () => {
                activeTeamFilter = button.dataset.teamFilter;
                const assigneeSelect = document.querySelector('[data-board-filter="assignee"]');
                assigneeSelect.value = activeTeamFilter === 'unassigned' ? '' : activeTeamFilter;
                document.querySelectorAll('[data-team-filter]').forEach(item => {
                    const isActive = item === button;
                    item.classList.toggle('is-active', isActive);
                    item.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                });
                applyFilters();
            }));
            document.querySelector('[data-reset-board-filters]')?.addEventListener('click', () => {
                activeTeamFilter = '';
                document.querySelectorAll('[data-board-filter]').forEach(input => { if (input.type === 'checkbox') input.checked = false; else input.value = ''; });
                document.querySelectorAll('[data-team-filter]').forEach((button, index) => { const isActive = index === 0; button.classList.toggle('is-active', isActive); button.setAttribute('aria-pressed', isActive ? 'true' : 'false'); });
                applyFilters();
            });
            document.addEventListener('pm:ajax-success', event => {
                const {form, payload, action} = event.detail;
                const task = payload.data;
                if (!task || !form.closest('.pm-board-view') || !action.endsWith('/tasks')) return;

                const list = board.querySelector(`[data-column-id="${task.board_column_id}"] [data-column-list]`);
                if (!list) return;

                const make = (tag, className, value) => {
                    const element = document.createElement(tag);
                    if (className) element.className = className;
                    if (value !== undefined) element.textContent = value;
                    return element;
                };
                const card = make('article', 'pm-task-card');
                card.dataset.taskCard = '';
                card.dataset.taskId = task.task_key;
                card.dataset.assignee = task.assignee_id || '';
                card.dataset.priority = task.priority || 'medium';
                card.dataset.overdue = '0';
                card.draggable = true;

                const top = make('div', 'pm-task-card__top');
                top.append(make('span', 'pm-task-key', task.task_key || 'New task'), make('span', 'pm-task-type', task.type || 'Task'), make('span', 'pm-task-grip', '⋮⋮'));
                const title = make('h4');
                const link = make('a', null, task.title || 'New task');
                link.href = '{{ url('/admin/project-management/tasks') }}/' + task.task_key;
                title.append(link);
                const details = make('div', 'pm-task-card__details');
                details.append(make('span', 'pm-task-due', task.due_on ? 'Due date set' : 'No due date'), make('span', 'pm-chip pm-chip--priority-' + (task.priority || 'medium'), (task.priority || 'medium').replace('_', ' ')));
                const footer = make('div', 'pm-task-card__footer');
                const assignee = make('label', 'pm-task-assignee');
                assignee.append(make('span', 'pm-avatar pm-avatar--empty', '—'));
                const assigneeSelect = board.querySelector('[data-card-assignee]')?.cloneNode(true);
                if (assigneeSelect) {
                    assigneeSelect.value = task.assignee_id || '';
                    assignee.append(assigneeSelect);
                }
                footer.append(assignee, make('span', 'pm-task-stats'));
                card.append(top, title, details, footer);
                list.querySelector('[data-drop-placeholder]')?.before(card);
                bindCard(card);
                updatePlaceholders();
                refreshCounts();
            });
            document.querySelectorAll('[data-add-to-column]').forEach(link => link.addEventListener('click', () => {
                const column = document.querySelector('#task-column');
                if (column) column.value = link.dataset.addToColumn;
                window.setTimeout(() => document.querySelector('#task-title')?.focus(), 120);
            }));

            updatePlaceholders();
        })();
    </script>
@endpush
