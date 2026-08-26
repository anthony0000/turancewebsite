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
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminProjectController extends Controller
{
    private const PROJECT_FILES_DIRECTORY = 'projects/files';

    private const FILE_MIMES = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'ppt', 'pptx', 'txt', 'rtf',
        'jpg', 'jpeg', 'png', 'webp', 'zip',
    ];

    public function index(): View
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

        $statusCounts = $projects
            ->groupBy(fn (Project $project) => $this->statusLabel($project->status))
            ->map(fn ($group, string $label) => [
                'label' => $label,
                'count' => $group->count(),
            ])
            ->sortByDesc('count')
            ->values();

        $maxFileCount = max(1, (int) $projects->max('files_count'));
        $fileLeaders = $projects
            ->filter(fn (Project $project) => $project->files_count > 0)
            ->sortByDesc('files_count')
            ->take(6)
            ->map(fn (Project $project) => [
                'name' => $project->name,
                'project_number' => $project->project_number,
                'count' => $project->files_count,
                'width' => round(($project->files_count / $maxFileCount) * 100, 1),
            ])
            ->values();

        $files = $canViewProjectFiles
            ? ProjectFile::query()
                ->with('project')
                ->when(! $canManageProjectFiles, fn ($query) => $query
                    ->where('is_shared', true)
                    ->whereIn('project_id', $projects->modelKeys() ?: [0]))
                ->latest()
                ->get()
            : collect();

        return view('admin.projects.index', [
            'projects' => $projects,
            'files' => $files,
            'statusCounts' => $statusCounts,
            'statusChartStyle' => $this->statusChartStyle($statusCounts, $projects->count()),
            'fileLeaders' => $fileLeaders,
            'projectCount' => $projects->count(),
            'activeCount' => $projects->whereIn('status', ['active', 'in_progress'])->count(),
            'fileCount' => (int) $projects->sum('files_count'),
            'sharedFileCount' => (int) $projects->sum('shared_files_count'),
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

        $validated = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'file' => ['required', 'file', 'max:51200', 'mimes:'.implode(',', self::FILE_MIMES)],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $project = Project::query()->findOrFail($validated['project_id']);
        $projectFile = $this->createProjectFile($project, $validated['file'], $validated['description'] ?? null);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'External file added to the project workspace.',
                'data' => [
                    'id' => $projectFile->id,
                    'project_id' => $projectFile->project_id,
                    'original_name' => $projectFile->original_name,
                    'description' => $projectFile->description,
                    'file_kind' => $projectFile->fileKind(),
                    'size_label' => $projectFile->sizeLabel(),
                    'download_url' => route('admin.projects.files.download', $projectFile),
                    'preview_url' => route('admin.projects.files.preview', $projectFile),
                ],
            ], 201);
        }

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('status', 'External file added to the project workspace.');
    }

    private function createProjectFile(Project $project, UploadedFile $file, ?string $description): ProjectFile
    {
        $path = $this->projectFilePath($project, $file);
        $contents = $file->getContent();

        return DB::transaction(function () use ($project, $file, $description, $path, $contents): ProjectFile {
            $projectFile = ProjectFile::query()->create([
                'project_id' => $project->id,
                'uploaded_by' => AdminAccess::currentUser()?->id,
                'original_name' => $this->originalName($file),
                'path' => $path,
                'mime_type' => $file->getMimeType() ?: $file->getClientMimeType(),
                'size' => $file->getSize(),
                'description' => filled($description) ? trim($description) : null,
            ]);

            $projectFile->content()->create([
                'contents' => $contents,
            ]);

            return $projectFile;
        });
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
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $newPath = null;
        $newContents = null;
        $oldPath = $projectFile->path;

        if ($request->hasFile('file')) {
            $newFile = $validated['file'];
            $newPath = $this->projectFilePath(
                Project::query()->findOrFail($projectFile->project_id),
                $newFile
            );
            $newContents = $newFile->getContent();
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

        DB::transaction(function () use ($projectFile, $attributes, $newPath, $newContents): void {
            $projectFile->forceFill($attributes)->save();

            if ($newPath !== null) {
                $projectFile->content()->updateOrCreate([], [
                    'contents' => $newContents,
                ]);
            }
        });

        if ($newPath !== null && $oldPath !== $newPath) {
            Storage::disk('local')->delete($oldPath);
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
        if (! $projectFile->content()->exists()) {
            Storage::disk('local')->delete($projectFile->path);
        }
        $projectFile->delete();

        return $this->projectFileRedirect(request(), $projectFile, $projectId)
            ->with('status', 'File removed from the project workspace.');
    }

    private function projectFileRedirect(Request $request, ProjectFile $projectFile, ?int $projectId = null): RedirectResponse
    {
        if ($request->string('return_to')->toString() === 'index') {
            return redirect()->route('admin.projects.index');
        }

        return redirect()->route('admin.projects.show', $projectId ?? $projectFile->project_id);
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

    private function projectFilePath(Project $project, UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'file');
        $extension = preg_replace('/[^a-z0-9]+/i', '', $extension) ?: 'file';

        return self::PROJECT_FILES_DIRECTORY.'/'.$project->id.'/'.Str::uuid().'.'.$extension;
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

        $content = $projectFile->content()->first(['contents']);

        if ($content !== null) {
            $headers['Content-Disposition'] = ($inline ? 'inline' : 'attachment').'; filename="'.addslashes($projectFile->original_name).'"';

            if ($projectFile->size !== null) {
                $headers['Content-Length'] = (string) $projectFile->size;
            }

            return response($content->contents, 200, $headers);
        }

        $path = Storage::disk('local')->path($projectFile->path);

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

        abort_unless($projectFile->is_shared && ProjectManagementAccess::canViewSharedFiles($projectFile->project), 403);
    }

    private function statusLabel(?string $status): string
    {
        return filled($status) ? Str::headline($status) : 'Uncategorised';
    }

    private function statusChartStyle($statusCounts, int $total): string
    {
        if ($total === 0) {
            return 'background: conic-gradient(#ece7dd 0deg 360deg);';
        }

        $colors = ['#b8860b', '#2f8054', '#6f5015', '#c08a4a', '#343b48', '#b94a3d'];
        $cursor = 0;
        $stops = [];

        foreach ($statusCounts as $index => $status) {
            $next = $cursor + (($status['count'] / $total) * 360);
            $color = $colors[$index % count($colors)];
            $stops[] = $color.' '.round($cursor, 2).'deg '.round($next, 2).'deg';
            $cursor = $next;
        }

        return 'background: conic-gradient('.implode(', ', $stops).');';
    }
}
