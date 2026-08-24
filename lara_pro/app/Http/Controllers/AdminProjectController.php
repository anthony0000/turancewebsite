<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFile;
use App\Support\AdminAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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
        $canManageProjectFiles = AdminAccess::can('project-files');
        $projectsQuery = Project::query()
            ->withCount('staffContracts');

        if ($canManageProjectFiles) {
            $projectsQuery->withCount([
                'files',
                'files as shared_files_count' => fn ($query) => $query->where('is_shared', true),
            ]);
        }

        $projects = $projectsQuery
            ->latest('updated_at')
            ->get();

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

        return view('admin.projects.index', [
            'projects' => $projects,
            'statusCounts' => $statusCounts,
            'statusChartStyle' => $this->statusChartStyle($statusCounts, $projects->count()),
            'fileLeaders' => $fileLeaders,
            'projectCount' => $projects->count(),
            'activeCount' => $projects->whereIn('status', ['active', 'in_progress'])->count(),
            'fileCount' => (int) $projects->sum('files_count'),
            'sharedFileCount' => (int) $projects->sum('shared_files_count'),
            'canManageProjectFiles' => $canManageProjectFiles,
        ]);
    }

    public function show(Project $project): View
    {
        $canManageProjectFiles = AdminAccess::can('project-files');
        $project->load([
            'staffContracts' => fn ($query) => $query->with('invoice')->latest('updated_at'),
        ]);

        if ($canManageProjectFiles) {
            $project->load([
                'files' => fn ($query) => $query->with('uploader')->latest(),
            ]);
        } else {
            $project->setRelation('files', collect());
        }

        return view('admin.projects.show', [
            'project' => $project,
            'files' => $project->files,
            'contracts' => $project->staffContracts,
            'sharedFileCount' => $project->files->where('is_shared', true)->count(),
            'canManageProjectFiles' => $canManageProjectFiles,
        ]);
    }

    public function storeFile(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:51200', 'mimes:'.implode(',', self::FILE_MIMES)],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $this->createProjectFile($project, $validated['file'], $validated['description'] ?? null);

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('status', 'File added to the project workspace.');
    }

    public function storeExternalFile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'file' => ['required', 'file', 'max:51200', 'mimes:'.implode(',', self::FILE_MIMES)],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $project = Project::query()->findOrFail($validated['project_id']);
        $this->createProjectFile($project, $validated['file'], $validated['description'] ?? null);

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('status', 'External file added to the project workspace.');
    }

    private function createProjectFile(Project $project, UploadedFile $file, ?string $description): ProjectFile
    {
        $path = $this->storeProjectFile($project, $file);

        try {
            return ProjectFile::query()->create([
                'project_id' => $project->id,
                'uploaded_by' => AdminAccess::currentUser()?->id,
                'original_name' => $this->originalName($file),
                'path' => $path,
                'mime_type' => $file->getMimeType() ?: $file->getClientMimeType(),
                'size' => $file->getSize(),
                'description' => filled($description) ? trim($description) : null,
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);

            throw $exception;
        }
    }

    public function downloadFile(ProjectFile $projectFile): BinaryFileResponse
    {
        return $this->fileResponse($projectFile, false);
    }

    public function previewFile(ProjectFile $projectFile): BinaryFileResponse
    {
        return $this->fileResponse($projectFile, true);
    }

    public function toggleShare(ProjectFile $projectFile): RedirectResponse
    {
        abort_unless($projectFile->hasStoredFile(), 404);

        $isSharing = ! $projectFile->is_shared;
        $projectFile->forceFill([
            'is_shared' => $isSharing,
            'share_token' => $isSharing ? Str::random(64) : $projectFile->share_token,
            'shared_at' => $isSharing ? now() : null,
        ])->save();

        return redirect()
            ->route('admin.projects.show', $projectFile->project_id)
            ->with('status', $isSharing
                ? 'A secure share link is ready for this file.'
                : 'The file share link has been revoked.');
    }

    public function destroyFile(ProjectFile $projectFile): RedirectResponse
    {
        $projectId = $projectFile->project_id;
        Storage::disk('local')->delete($projectFile->path);
        $projectFile->delete();

        return redirect()
            ->route('admin.projects.show', $projectId)
            ->with('status', 'File removed from the project workspace.');
    }

    public function sharedFile(ProjectFile $projectFile): View
    {
        $this->abortUnlessShared($projectFile);

        return view('project-files.share', [
            'projectFile' => $projectFile->load('project'),
        ]);
    }

    public function downloadSharedFile(ProjectFile $projectFile): BinaryFileResponse
    {
        $this->abortUnlessShared($projectFile);

        return $this->fileResponse($projectFile, false);
    }

    private function storeProjectFile(Project $project, UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'file');
        $extension = preg_replace('/[^a-z0-9]+/i', '', $extension) ?: 'file';
        $filename = Str::uuid().'.'.$extension;
        $path = $file->storeAs(self::PROJECT_FILES_DIRECTORY.'/'.$project->id, $filename, 'local');

        if (! is_string($path) || $path === '') {
            throw new \RuntimeException('The project file could not be stored.');
        }

        return $path;
    }

    private function originalName(UploadedFile $file): string
    {
        $name = trim((string) $file->getClientOriginalName());

        return Str::limit($name !== '' ? $name : 'Project file', 255, '');
    }

    private function fileResponse(ProjectFile $projectFile, bool $inline): BinaryFileResponse
    {
        abort_unless($projectFile->hasStoredFile(), 404);

        $path = Storage::disk('local')->path($projectFile->path);
        $headers = [
            'Content-Type' => $projectFile->mime_type ?: 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
        ];

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
