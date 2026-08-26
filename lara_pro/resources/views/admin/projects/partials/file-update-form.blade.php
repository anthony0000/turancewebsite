<details class="project-file-edit">
    <summary class="ghost-button">Update</summary>
    <form method="POST" action="{{ route('admin.projects.files.update', $file) }}" enctype="multipart/form-data" class="project-file-edit__form">
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
    </form>
</details>
