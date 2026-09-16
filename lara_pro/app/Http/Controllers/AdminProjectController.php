<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFile;
use App\Support\AdminAccess;
use App\Support\ProjectManagementAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class AdminProjectController extends Controller
{
    private const PROJECT_FILES_DIRECTORY = 'projects/files';

    private const PROJECT_FILES_DISK = 'public_uploads';

    private const FILE_MIMES = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'ppt', 'pptx', 'txt', 'rtf',
        'jpg', 'jpeg', 'png', 'webp', 'zip',
    ];

    public function index(Request $request): View
    {
        $canManageProjectFiles = AdminAccess::isFullAdmin();
        $projectsQuery = ProjectManagementAccess::scopeVisibleSharedProjects(Project::query())
            ->withCount('staffContracts')
            ->withCount([
                'files' => fn ($query) => $query->when(! $canManageProjectFiles, fn ($files) => $files->where('is_shared', true)),
                'files as shared_files_count' => fn ($query) => $query->where('is_shared', true),
            ]);

        $projects = $projectsQuery
            ->latest('updated_at')
            ->get();
        $canViewProjectFiles = $canManageProjectFiles || $projects->isNotEmpty();

        $visibleFilesQuery = ProjectFile::query()
            ->when(! $canManageProjectFiles, fn ($query) => $query
                ->where('document_scope', 'project')
                ->where('is_shared', true)
                ->whereIn('project_id', $projects->modelKeys() ?: [0]));
        $availableFolders = $canManageProjectFiles
            ? (clone $visibleFilesQuery)
                ->where('document_scope', 'company')
                ->whereNotNull('folder')
                ->distinct()
                ->orderBy('folder')
                ->pluck('folder')
            : collect();

        $search = $request->string('q')->trim()->toString();
        $scope = in_array($request->string('scope')->toString(), ['company', 'project'], true)
            ? $request->string('scope')->toString()
            : 'all';
        $type = in_array($request->string('type')->toString(), ['pdf', 'image', 'document', 'spreadsheet', 'other'], true)
            ? $request->string('type')->toString()
            : 'all';
        $sharing = in_array($request->string('sharing')->toString(), ['shared', 'private'], true)
            ? $request->string('sharing')->toString()
            : 'all';
        $sort = in_array($request->string('sort')->toString(), ['oldest', 'name', 'size'], true)
            ? $request->string('sort')->toString()
            : 'newest';
        $projectFilter = $request->integer('project');
        $folderFilter = $request->string('folder')->trim()->toString();

        $filesQuery = (clone $visibleFilesQuery)->with(['project', 'uploader'])
            ->when($search !== '', fn ($query) => $query->where(function ($searchQuery) use ($search): void {
                $searchQuery
                    ->where('original_name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhere('folder', 'like', '%'.$search.'%')
                    ->orWhereHas('project', fn ($projectQuery) => $projectQuery
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('project_number', 'like', '%'.$search.'%'));
            }))
            ->when($scope !== 'all', fn ($query) => $query->where('document_scope', $scope))
            ->when($projectFilter > 0, fn ($query) => $query->where('project_id', $projectFilter))
            ->when($folderFilter !== '', fn ($query) => $query->where('folder', $folderFilter))
            ->when($sharing === 'shared', fn ($query) => $query->where('is_shared', true))
            ->when($sharing === 'private', fn ($query) => $query->where('is_shared', false));

        $this->applyFileTypeFilter($filesQuery, $type);

        $recentFiles = $canViewProjectFiles
            ? (clone $filesQuery)->latest()->limit(6)->get()
            : collect();

        match ($sort) {
            'oldest' => $filesQuery->oldest(),
            'name' => $filesQuery->orderBy('original_name'),
            'size' => $filesQuery->orderByDesc('size'),
            default => $filesQuery->latest(),
        };

        $files = $canViewProjectFiles
            ? $filesQuery->paginate(12)->withQueryString()
            : ProjectFile::query()->whereRaw('1 = 0')->paginate(12);
        $fileCount = $canViewProjectFiles ? (clone $visibleFilesQuery)->count() : 0;
        $companyFileCount = $canManageProjectFiles
            ? (clone $visibleFilesQuery)->where('document_scope', 'company')->count()
            : 0;
        $projectFileCount = $canViewProjectFiles
            ? (clone $visibleFilesQuery)->where('document_scope', 'project')->count()
            : 0;
        $sharedFileCount = $canViewProjectFiles
            ? (clone $visibleFilesQuery)->where('is_shared', true)->count()
            : 0;
        $storageUsed = $canViewProjectFiles ? (int) (clone $visibleFilesQuery)->sum('size') : 0;

        return view('admin.projects.index', [
            'projects' => $projects,
            'files' => $files,
            'recentFiles' => $recentFiles,
            'availableFolders' => $availableFolders,
            'projectCount' => $projects->count(),
            'fileCount' => $fileCount,
            'companyFileCount' => $companyFileCount,
            'projectFileCount' => $projectFileCount,
            'sharedFileCount' => $sharedFileCount,
            'storageUsed' => $storageUsed,
            'filters' => compact('search', 'scope', 'type', 'sharing', 'sort', 'projectFilter', 'folderFilter'),
            'canViewProjectFiles' => $canViewProjectFiles,
            'canManageProjectFiles' => $canManageProjectFiles,
        ]);
    }

    public function show(Project $project): View
    {
        $canManageProjectFiles = AdminAccess::isFullAdmin();
        $canViewProjectFiles = ProjectManagementAccess::canViewSharedFiles($project);
        abort_unless($canViewProjectFiles, 403);

        if ($canManageProjectFiles) {
            $project->load([
                'staffContracts' => fn ($query) => $query->with('invoice')->latest('updated_at'),
            ]);
        } else {
            $project->setRelation('staffContracts', collect());
        }

        if ($canViewProjectFiles) {
            $project->load([
                'files' => fn ($query) => $query->when(! $canManageProjectFiles, fn ($files) => $files->where('is_shared', true))->with('uploader')->latest(),
            ]);
        } else {
            $project->setRelation('files', collect());
        }

        return view('admin.projects.show', [
            'project' => $project,
            'files' => $project->files,
            'contracts' => $project->staffContracts,
            'sharedFileCount' => $project->files->where('is_shared', true)->count(),
            'canViewProjectFiles' => $canViewProjectFiles,
            'canManageProjectFiles' => $canManageProjectFiles,
        ]);
    }

    public function storeFile(Request $request, Project $project): RedirectResponse
    {
        abort_unless(AdminAccess::isFullAdmin(), 403);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:51200', 'mimes:'.implode(',', self::FILE_MIMES)],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $this->createProjectFile($project, $validated['file'], $validated['description'] ?? null);

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('status', 'File added to the project workspace.');
    }

    public function storeExternalFile(Request $request): RedirectResponse|JsonResponse
    {
        abort_unless(AdminAccess::isFullAdmin(), 403);

        $request->merge([
            'document_scope' => $request->input('document_scope', 'project'),
        ]);

        $validated = $request->validate([
            'document_scope' => ['required', Rule::in(['company', 'project'])],
            'project_id' => [Rule::requiredIf(fn () => $request->input('document_scope') === 'project'), 'nullable', 'integer', 'exists:projects,id'],
            'folder' => ['nullable', 'string', 'max:100'],
            'files' => ['nullable', 'array', 'min:1', 'max:10'],
            'files.*' => ['file', 'max:51200', 'mimes:'.implode(',', self::FILE_MIMES)],
            'file' => ['nullable', 'file', 'max:51200', 'mimes:'.implode(',', self::FILE_MIMES)],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $uploads = collect($validated['files'] ?? []);

        if (isset($validated['file'])) {
            $uploads->push($validated['file']);
        }

        if ($uploads->isEmpty()) {
            throw ValidationException::withMessages([
                'files' => 'Choose at least one document to upload.',
            ]);
        }

        if ($uploads->count() > 10) {
            throw ValidationException::withMessages([
                'files' => 'You may upload a maximum of 10 documents at once.',
            ]);
        }

        $project = $validated['document_scope'] === 'project'
            ? Project::query()->findOrFail($validated['project_id'])
            : null;
        $projectFiles = collect();

        try {
            foreach ($uploads as $upload) {
                $projectFiles->push($this->createProjectFile(
                    $project,
                    $upload,
                    $validated['description'] ?? null,
                    $validated['folder'] ?? null,
                    $validated['document_scope']
                ));
            }
        } catch (Throwable $exception) {
            foreach ($projectFiles as $storedFile) {
                $storedPath = $storedFile->path;
                $storedFile->delete();
                Storage::disk(self::PROJECT_FILES_DISK)->delete($storedPath);
            }

            throw $exception;
        }

        $count = $projectFiles->count();
        $message = $count === 1
            ? ($project ? 'File added to the project workspace.' : 'Company document added to the library.')
            : ($project ? $count.' files added to the project workspace.' : $count.' company documents added to the library.');
        $firstFile = $projectFiles->first();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'data' => [
                    ...$this->projectFilePayload($firstFile),
                    'count' => $count,
                    'files' => $projectFiles->map(fn (ProjectFile $file) => $this->projectFilePayload($file))->values(),
                ],
            ], 201);
        }

        return redirect()
            ->route($project ? 'admin.projects.show' : 'admin.projects.index', $project ? [$project] : [])
            ->with('status', $message);
    }

    private function createProjectFile(
        ?Project $project,
        UploadedFile $file,
        ?string $description,
        ?string $folder = null,
        string $documentScope = 'project'
    ): ProjectFile
    {
        $path = $this->projectFilePath($project, $file);
        $storedPath = $file->storeAs(dirname($path), basename($path), self::PROJECT_FILES_DISK);

        if (! is_string($storedPath) || $storedPath === '') {
            throw new RuntimeException('The uploaded project file could not be stored.');
        }

        try {
            return DB::transaction(function () use ($project, $file, $description, $folder, $documentScope, $storedPath): ProjectFile {
                return ProjectFile::query()->create([
                    'project_id' => $project?->id,
                    'document_scope' => $documentScope,
                    'folder' => filled($folder)
                        ? Str::limit(trim($folder), 100, '')
                        : ($documentScope === 'company' ? 'General' : null),
                    'uploaded_by' => AdminAccess::currentUser()?->id,
                    'original_name' => $this->originalName($file),
                    'path' => $storedPath,
                    'mime_type' => $file->getMimeType() ?: $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'description' => filled($description) ? trim($description) : null,
                ]);
            });
        } catch (Throwable $exception) {
            Storage::disk(self::PROJECT_FILES_DISK)->delete($storedPath);

            throw $exception;
        }
    }

    private function projectFilePayload(ProjectFile $projectFile): array
    {
        return [
            'id' => $projectFile->id,
            'project_id' => $projectFile->project_id,
            'document_scope' => $projectFile->document_scope,
            'folder' => $projectFile->folder,
            'original_name' => $projectFile->original_name,
            'description' => $projectFile->description,
            'file_kind' => $projectFile->fileKind(),
            'size_label' => $projectFile->sizeLabel(),
            'download_url' => route('admin.projects.files.download', $projectFile),
            'preview_url' => route('admin.projects.files.preview', $projectFile),
        ];
    }

    public function downloadFile(ProjectFile $projectFile): BinaryFileResponse|Response
    {
        $this->ensureReadableFile($projectFile);

        return $this->fileResponse($projectFile, false);
    }

    public function previewFile(ProjectFile $projectFile): BinaryFileResponse|Response
    {
        $this->ensureReadableFile($projectFile);

        return $this->fileResponse($projectFile, true);
    }

    public function updateFile(Request $request, ProjectFile $projectFile): RedirectResponse|JsonResponse
    {
        abort_unless(AdminAccess::isFullAdmin(), 403);

        $validated = $request->validate([
            'file' => ['nullable', 'file', 'max:51200', 'mimes:'.implode(',', self::FILE_MIMES)],
            'folder' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $newPath = null;
        $oldPath = $projectFile->path;

        if ($request->hasFile('file')) {
            $newFile = $validated['file'];
            $replacementPath = $this->projectFilePath(
                $projectFile->project_id ? Project::query()->findOrFail($projectFile->project_id) : null,
                $newFile
            );
            $newPath = $newFile->storeAs(dirname($replacementPath), basename($replacementPath), self::PROJECT_FILES_DISK);

            if (! is_string($newPath) || $newPath === '') {
                throw new RuntimeException('The replacement project file could not be stored.');
            }
        }

        $attributes = [];

        if ($newPath !== null) {
            $file = $validated['file'];
            $attributes = [
                'original_name' => $this->originalName($file),
                'path' => $newPath,
                'mime_type' => $file->getMimeType() ?: $file->getClientMimeType(),
                'size' => $file->getSize(),
            ];
        }

        if ($request->exists('description')) {
            $attributes['description'] = filled($validated['description'] ?? null)
                ? trim($validated['description'])
                : null;
        }

        if ($request->exists('folder')) {
            $attributes['folder'] = filled($validated['folder'] ?? null)
                ? Str::limit(trim($validated['folder']), 100, '')
                : null;
        }

        try {
            DB::transaction(function () use ($projectFile, $attributes): void {
                $projectFile->forceFill($attributes)->save();
            });
        } catch (Throwable $exception) {
            if ($newPath !== null) {
                Storage::disk(self::PROJECT_FILES_DISK)->delete($newPath);
            }

            throw $exception;
        }

        if ($newPath !== null && $oldPath !== $newPath) {
            Storage::disk(self::PROJECT_FILES_DISK)->delete($oldPath);
        }

        $message = $newPath !== null
            ? 'Project file updated and replaced successfully.'
            : 'Project file details updated successfully.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'data' => [
                    'id' => $projectFile->id,
                    'original_name' => $projectFile->original_name,
                    'description' => $projectFile->description,
                    'folder' => $projectFile->folder,
                    'file_kind' => $projectFile->fileKind(),
                    'size_label' => $projectFile->sizeLabel(),
                    'mime_type' => $projectFile->mime_type,
                ],
            ]);
        }

        return $this->projectFileRedirect($request, $projectFile)
            ->with('status', $message);
    }

    public function toggleShare(ProjectFile $projectFile): RedirectResponse
    {
        abort_unless(AdminAccess::isFullAdmin(), 403);
        abort_unless($projectFile->hasStoredFile(), 404);

        $isSharing = ! $projectFile->is_shared;
        $projectFile->forceFill([
            'is_shared' => $isSharing,
            'share_token' => $isSharing ? Str::random(64) : $projectFile->share_token,
            'shared_at' => $isSharing ? now() : null,
        ])->save();

        return $this->projectFileRedirect(request(), $projectFile)
            ->with('status', $isSharing
                ? 'A secure share link is ready for this file.'
                : 'The file share link has been revoked.');
    }

    public function destroyFile(ProjectFile $projectFile): RedirectResponse
    {
        abort_unless(AdminAccess::isFullAdmin(), 403);
        $projectId = $projectFile->project_id;
        $path = $projectFile->path;
        $projectFile->delete();
        Storage::disk(self::PROJECT_FILES_DISK)->delete($path);

        return $this->projectFileRedirect(request(), $projectFile, $projectId)
            ->with('status', 'File removed from the document library.');
    }

    private function projectFileRedirect(Request $request, ProjectFile $projectFile, ?int $projectId = null): RedirectResponse
    {
        if ($request->string('return_to')->toString() === 'index') {
            return redirect()->route('admin.projects.index');
        }

        $resolvedProjectId = $projectId ?? $projectFile->project_id;

        if ($resolvedProjectId === null) {
            return redirect()->route('admin.projects.index');
        }

        $project = Project::query()->findOrFail($resolvedProjectId);

        return redirect()->route('admin.projects.show', $project);
    }

    public function sharedFile(ProjectFile $projectFile): View
    {
        $this->abortUnlessShared($projectFile);

        return view('project-files.share', [
            'projectFile' => $projectFile->load('project'),
        ]);
    }

    public function downloadSharedFile(ProjectFile $projectFile): BinaryFileResponse|Response
    {
        $this->abortUnlessShared($projectFile);

        return $this->fileResponse($projectFile, false);
    }

    private function projectFilePath(?Project $project, UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'file');
        $extension = preg_replace('/[^a-z0-9]+/i', '', $extension) ?: 'file';

        $location = $project ? (string) $project->id : 'company';

        return self::PROJECT_FILES_DIRECTORY.'/'.$location.'/'.Str::uuid().'.'.$extension;
    }

    private function originalName(UploadedFile $file): string
    {
        $name = trim((string) $file->getClientOriginalName());

        return Str::limit($name !== '' ? $name : 'Project file', 255, '');
    }

    private function fileResponse(ProjectFile $projectFile, bool $inline): BinaryFileResponse|Response
    {
        abort_unless($projectFile->hasStoredFile(), 404);

        $headers = [
            'Content-Type' => $projectFile->mime_type ?: 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
        ];

        $path = Storage::disk(self::PROJECT_FILES_DISK)->path($projectFile->path);

        if ($inline) {
            $headers['Content-Disposition'] = 'inline; filename="'.addslashes($projectFile->original_name).'"';

            return response()->file($path, $headers);
        }

        return response()->download($path, $projectFile->original_name, $headers);
    }

    private function abortUnlessShared(ProjectFile $projectFile): void
    {
        abort_unless($projectFile->is_shared && filled($projectFile->share_token) && $projectFile->hasStoredFile(), 404);
    }

    private function ensureReadableFile(ProjectFile $projectFile): void
    {
        if (AdminAccess::isFullAdmin()) {
            return;
        }

        abort_unless(
            ! $projectFile->isCompanyDocument()
            && $projectFile->is_shared
            && $projectFile->project
            && ProjectManagementAccess::canViewSharedFiles($projectFile->project),
            403
        );
    }

    private function applyFileTypeFilter($query, string $type): void
    {
        $documentMimes = [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'application/rtf',
            'text/rtf',
        ];
        $spreadsheetMimes = [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv',
        ];

        match ($type) {
            'pdf' => $query->where('mime_type', 'application/pdf'),
            'image' => $query->where('mime_type', 'like', 'image/%'),
            'document' => $query->whereIn('mime_type', $documentMimes),
            'spreadsheet' => $query->whereIn('mime_type', $spreadsheetMimes),
            'other' => $query->where(function ($otherQuery) use ($documentMimes, $spreadsheetMimes): void {
                $otherQuery
                    ->whereNull('mime_type')
                    ->orWhere(function ($knownQuery) use ($documentMimes, $spreadsheetMimes): void {
                        $knownQuery
                            ->where('mime_type', '!=', 'application/pdf')
                            ->where('mime_type', 'not like', 'image/%')
                            ->whereNotIn('mime_type', [...$documentMimes, ...$spreadsheetMimes]);
                    });
            }),
            default => null,
        };
    }

}
