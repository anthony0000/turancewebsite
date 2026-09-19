<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCredential;
use App\Support\AdminAccess;
use App\Support\DocumentBranding;
use App\Support\DocumentTypography;
use App\Support\ProjectManagementAccess;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class AdminProjectCredentialController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->ensureFullAdmin();
        $validated = $this->validateCredential($request, true);
        $userId = AdminAccess::currentUser()?->id;

        $credential = $project->credentials()->create([
            ...$validated,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        ProjectManagementAccess::log(
            $project,
            'project-credential.created',
            ProjectCredential::class,
            $credential->id,
            null,
            ['service_name' => $credential->service_name, 'credential_type' => $credential->credential_type],
        );

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('status', 'Project credential stored securely.');
    }

    public function update(Request $request, Project $project, ProjectCredential $projectCredential): RedirectResponse
    {
        $this->ensureFullAdmin();
        $this->ensureBelongsToProject($project, $projectCredential);
        $validated = $this->validateCredential($request, false);

        if (blank($validated['secret'] ?? null)) {
            unset($validated['secret']);
        }

        $oldMetadata = [
            'service_name' => $projectCredential->service_name,
            'credential_type' => $projectCredential->credential_type,
        ];

        $projectCredential->update([
            ...$validated,
            'updated_by' => AdminAccess::currentUser()?->id,
        ]);

        ProjectManagementAccess::log(
            $project,
            'project-credential.updated',
            ProjectCredential::class,
            $projectCredential->id,
            $oldMetadata,
            [
                'service_name' => $projectCredential->service_name,
                'credential_type' => $projectCredential->credential_type,
            ],
        );

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('status', 'Project credential updated.');
    }

    public function destroy(Project $project, ProjectCredential $projectCredential): RedirectResponse
    {
        $this->ensureFullAdmin();
        $this->ensureBelongsToProject($project, $projectCredential);
        $metadata = [
            'service_name' => $projectCredential->service_name,
            'credential_type' => $projectCredential->credential_type,
        ];
        $credentialId = $projectCredential->id;

        $projectCredential->delete();

        ProjectManagementAccess::log(
            $project,
            'project-credential.deleted',
            ProjectCredential::class,
            $credentialId,
            $metadata,
            null,
        );

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('status', 'Project credential removed.');
    }

    public function reveal(Project $project, ProjectCredential $projectCredential): JsonResponse
    {
        $this->ensureFullAdmin();
        $this->ensureBelongsToProject($project, $projectCredential);

        ProjectManagementAccess::log(
            $project,
            'project-credential.revealed',
            ProjectCredential::class,
            $projectCredential->id,
            metadata: ['service_name' => $projectCredential->service_name],
        );

        return response()
            ->json(['secret' => $projectCredential->secret])
            ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    public function downloadPdf(Project $project): Response
    {
        $this->ensureFullAdmin();
        $credentials = $project->credentials()->orderBy('service_name')->orderBy('id')->get();
        abort_if($credentials->isEmpty(), 404, 'This project has no credentials to collate.');

        ProjectManagementAccess::log(
            $project,
            'project-credential.pdf-downloaded',
            ProjectCredential::class,
            metadata: ['credential_count' => $credentials->count()],
        );

        $pdf = Pdf::loadView('admin.projects.credentials-pdf', [
            'project' => $project,
            'credentials' => $credentials,
            'backgroundSrc' => DocumentBranding::logoSource(config('letters.background_path')),
            'generatedBy' => AdminAccess::displayName(),
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        DocumentTypography::registerDompdfFonts($pdf->getDomPDF());

        $response = $pdf->download(Str::slug($project->project_number.' credential collation').'.pdf');
        $response->headers->set('Cache-Control', 'private, no-store, no-cache, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }

    /** @return array<string, mixed> */
    private function validateCredential(Request $request, bool $secretRequired): array
    {
        return $request->validate([
            'service_name' => ['required', 'string', 'max:255'],
            'credential_type' => ['required', 'string', 'max:80'],
            'access_url' => ['nullable', 'string', 'max:2048', 'url:http,https'],
            'username' => ['nullable', 'string', 'max:1000'],
            'secret' => [$secretRequired ? 'required' : 'nullable', 'string', 'max:10000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function ensureFullAdmin(): void
    {
        abort_unless(AdminAccess::isFullAdmin(), 403);
    }

    private function ensureBelongsToProject(Project $project, ProjectCredential $projectCredential): void
    {
        abort_unless((int) $projectCredential->project_id === (int) $project->id, 404);
    }
}
