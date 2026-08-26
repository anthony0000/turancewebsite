<details class="project-file-edit">
    <summary class="ghost-button">Update</summary>
    <form method="POST" action="{{ route('admin.projects.files.update', $file) }}" enctype="multipart/form-data" class="project-file-edit__form" data-project-file-update data-project-file-update-id="{{ $file->id }}">
        @csrf
        @method('PUT')
        @if (($returnTo ?? null) === 'index')
            <input type="hidden" name="return_to" value="index">
        @endif
        <label for="project-file-replacement-{{ $file->id }}">Replace file <span>(optional)</span></label>
        <input id="project-file-replacement-{{ $file->id }}" type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.ppt,.pptx,.txt,.rtf,.jpg,.jpeg,.png,.webp,.zip">
        <small>Leave this empty to update only the description.</small>
        <label for="project-file-update-description-{{ $file->id }}">Description <span>(optional)</span></label>
        <textarea id="project-file-update-description-{{ $file->id }}" name="description" rows="3" maxlength="500">{{ old('description', $file->description) }}</textarea>
        <button class="button" type="submit">Save update</button>
        <p class="project-file-edit__status" data-project-file-update-status role="status" aria-live="polite"></p>
    </form>
</details>

@once
    @push('scripts')
        <script>
            (() => {
                document.addEventListener('submit', async (event) => {
                    const form = event.target.closest('[data-project-file-update]');

                    if (!form) {
                        return;
                    }

                    event.preventDefault();

                    const submitButton = form.querySelector('button[type="submit"]');
                    const fileInput = form.querySelector('input[type="file"]');
                    const status = form.querySelector('[data-project-file-update-status]');
                    const defaultLabel = submitButton ? submitButton.textContent : 'Save update';

                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.textContent = 'Saving...';
                    }

                    if (status) {
                        status.className = 'project-file-edit__status';
                        status.textContent = 'Saving file details...';
                    }

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: new FormData(form),
                        });
                        const payload = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            const validationMessage = Object.values(payload.errors || {}).flat()[0];
                            throw new Error(validationMessage || payload.message || 'The file could not be updated.');
                        }

                        const data = payload.data || {};
                        const item = document.querySelector('[data-project-file-item="' + data.id + '"]');

                        if (item) {
                            const name = item.querySelector('[data-project-file-name]');
                            const meta = item.querySelector('[data-project-file-meta]');
                            let description = item.querySelector('[data-project-file-description]');

                            if (name) {
                                name.textContent = data.original_name;
                            }

                            if (meta) {
                                meta.textContent = data.file_kind + ' \u00b7 ' + data.size_label;
                            }

                            if (data.description) {
                                if (!description) {
                                    description = document.createElement('p');
                                    description.className = 'project-file-card__description';
                                    description.setAttribute('data-project-file-description', '');
                                    item.querySelector('.project-file-card__actions').before(description);
                                }

                                description.textContent = data.description;
                            } else if (description) {
                                description.remove();
                            }
                        }

                        if (fileInput) {
                            fileInput.value = '';
                        }

                        form.closest('details').open = false;

                        if (status) {
                            status.className = 'project-file-edit__status is-success';
                            status.textContent = payload.message || 'Project file updated successfully.';
                        }
                    } catch (error) {
                        if (status) {
                            status.className = 'project-file-edit__status is-error';
                            status.textContent = error.message;
                        }
                    } finally {
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.textContent = defaultLabel;
                        }
                    }
                });
            })();
        </script>
    @endpush
@endonce
