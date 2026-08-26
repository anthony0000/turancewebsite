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
        <div class="tt-project-upload-progress project-file-edit__progress" data-project-file-update-progress hidden>
            <div class="tt-project-upload-progress__head">
                <span data-project-file-update-progress-label>Preparing update</span>
                <strong data-project-file-update-progress-value>0%</strong>
            </div>
            <div class="tt-project-upload-progress__track" role="progressbar" aria-label="File update progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-project-file-update-progress-track>
                <span data-project-file-update-progress-fill></span>
            </div>
            <span class="tt-project-upload-progress__detail" data-project-file-update-progress-detail>Getting the replacement ready...</span>
        </div>
        <p class="project-file-edit__status" data-project-file-update-status role="status" aria-live="polite"></p>
    </form>
</details>

@once
    @push('scripts')
        <script>
            (() => {
                const updateFile = (formData, action, onProgress) => new Promise((resolve, reject) => {
                    const request = new XMLHttpRequest();

                    request.open('POST', action);
                    request.setRequestHeader('Accept', 'application/json');
                    request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    request.upload.addEventListener('progress', (event) => {
                        if (!event.lengthComputable) {
                            onProgress(0, 'Uploading replacement', 'Uploading securely...');
                            return;
                        }

                        const percent = (event.loaded / event.total) * 100;
                        onProgress(percent, 'Uploading replacement', Math.round(percent) + '% uploaded');
                    });
                    request.addEventListener('load', () => {
                        let payload = {};

                        try {
                            payload = JSON.parse(request.responseText || '{}');
                        } catch (error) {
                            payload = {};
                        }

                        if (request.status >= 200 && request.status < 300) {
                            resolve(payload);
                            return;
                        }

                        const validationMessage = Object.values(payload.errors || {}).flat().find((message) => typeof message === 'string');
                        reject(new Error(validationMessage || payload.message || 'The file could not be updated.'));
                    });
                    request.addEventListener('error', () => reject(new Error('The file could not be updated.')));
                    request.addEventListener('abort', () => reject(new Error('The update was cancelled.')));
                    request.send(formData);
                });

                document.addEventListener('submit', async (event) => {
                    const form = event.target.closest('[data-project-file-update]');

                    if (!form) {
                        return;
                    }

                    event.preventDefault();

                    const submitButton = form.querySelector('button[type="submit"]');
                    const fileInput = form.querySelector('input[type="file"]');
                    const status = form.querySelector('[data-project-file-update-status]');
                    const progress = form.querySelector('[data-project-file-update-progress]');
                    const progressLabel = form.querySelector('[data-project-file-update-progress-label]');
                    const progressValue = form.querySelector('[data-project-file-update-progress-value]');
                    const progressTrack = form.querySelector('[data-project-file-update-progress-track]');
                    const progressFill = form.querySelector('[data-project-file-update-progress-fill]');
                    const progressDetail = form.querySelector('[data-project-file-update-progress-detail]');
                    const defaultLabel = submitButton ? submitButton.textContent.trim() : 'Save update';
                    const hasReplacement = Boolean(fileInput && fileInput.files.length);

                    const setProgress = (value, label, detail) => {
                        if (!progress) {
                            return;
                        }

                        const safeValue = Math.max(0, Math.min(100, Math.round(value)));
                        progress.hidden = false;
                        progress.classList.toggle('is-indeterminate', safeValue === 0);
                        if (progressLabel) progressLabel.textContent = label;
                        if (progressValue) progressValue.textContent = safeValue + '%';
                        if (progressDetail) progressDetail.textContent = detail;
                        if (progressTrack) progressTrack.setAttribute('aria-valuenow', String(safeValue));
                        if (progressFill) progressFill.style.width = safeValue + '%';
                    };

                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.textContent = 'Saving...';
                    }

                    if (status) {
                        status.className = 'project-file-edit__status';
                        status.textContent = hasReplacement ? 'Preparing file update...' : 'Saving file details...';
                    }

                    if (progress) {
                        progress.classList.remove('is-complete', 'is-error');
                        if (hasReplacement) {
                            setProgress(0, 'Preparing update', 'Getting the replacement ready...');
                        } else {
                            progress.hidden = true;
                        }
                    }

                    try {
                        const payload = await updateFile(new FormData(form), form.action, hasReplacement ? setProgress : () => {});

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

                        if (hasReplacement && progress) {
                            progress.classList.remove('is-indeterminate', 'is-error');
                            progress.classList.add('is-complete');
                            setProgress(100, 'Update complete', 'File is now available in the project workspace.');
                        }

                        form.closest('details').open = false;

                        if (status) {
                            status.className = 'project-file-edit__status is-success';
                            status.textContent = payload.message || 'Project file updated successfully.';
                        }
                    } catch (error) {
                        if (hasReplacement && progress) {
                            progress.classList.add('is-error');
                            if (progressLabel) progressLabel.textContent = 'Update failed';
                            if (progressDetail) progressDetail.textContent = 'Check the file and try again.';
                        }
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
