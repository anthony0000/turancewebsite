<?php

namespace App\Http\Controllers;

use App\Models\LetterDraft;
use App\Support\DocumentBranding;
use App\Support\DocumentTypography;
use App\Support\LetterBody;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminLetterController extends Controller
{
    public function create(): View
    {
        return $this->editor();
    }

    public function edit(LetterDraft $draft): View
    {
        return $this->editor($draft);
    }

    private function editor(?LetterDraft $draft = null): View
    {
        return view('admin.letters.create', [
            'backgroundSrc' => DocumentBranding::logoSource(config('letters.background_path')),
            'defaults' => config('letters.defaults', []),
            'documentTypes' => config('letters.document_types', []),
            'draft' => $draft,
            'drafts' => LetterDraft::query()->latest('updated_at')->get(),
        ]);
    }

    public function storeDraft(Request $request): RedirectResponse
    {
        $draft = LetterDraft::query()->create(['content' => $this->draftFields($request)]);

        return redirect()->route('admin.letters.drafts.edit', $draft)->with('status', 'Letter draft saved.');
    }

    public function updateDraft(Request $request, LetterDraft $draft): RedirectResponse
    {
        $draft->update(['content' => $this->draftFields($request)]);

        return redirect()->route('admin.letters.drafts.edit', $draft)->with('status', 'Letter draft updated.');
    }

    private function draftFields(Request $request): array
    {
        $fields = $request->validate([
            'document_type' => ['nullable', 'string', 'max:80'],
            'date' => ['nullable', 'date'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'recipient_role' => ['nullable', 'string', 'max:255'],
            'recipient_company' => ['nullable', 'string', 'max:255'],
            'recipient_address' => ['nullable', 'string', 'max:1500'],
            'subject' => ['nullable', 'string', 'max:255'],
            'greeting' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:20000'],
            'closing' => ['nullable', 'string', 'max:120'],
            'signatory_name' => ['nullable', 'string', 'max:255'],
            'signatory_title' => ['nullable', 'string', 'max:255'],
        ]);

        $fields['body'] = LetterBody::clean($fields['body'] ?? '');

        return $fields;
    }

    public function downloadPdf(Request $request): Response
    {
        $validated = $request->validate([
            'document_type' => ['required', 'string', 'max:80'],
            'date' => ['required', 'date'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_role' => ['nullable', 'string', 'max:255'],
            'recipient_company' => ['nullable', 'string', 'max:255'],
            'recipient_address' => ['nullable', 'string', 'max:1500'],
            'subject' => ['nullable', 'string', 'max:255'],
            'greeting' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:20000'],
            'closing' => ['required', 'string', 'max:120'],
            'signatory_name' => ['required', 'string', 'max:255'],
            'signatory_title' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['body'] = LetterBody::clean($validated['body']);
        if (trim(strip_tags($validated['body'])) === '') {
            throw ValidationException::withMessages(['body' => 'The body field is required.']);
        }

        $validated['date_label'] = Carbon::parse($validated['date'])->format('F j, Y');

        $pdf = Pdf::loadView('admin.letters.pdf', [
            'backgroundSrc' => DocumentBranding::logoSource(config('letters.background_path')),
            'letter' => $validated,
        ])->setPaper('a4', 'portrait');

        DocumentTypography::registerDompdfFonts($pdf->getDomPDF());

        return $pdf->download(Str::slug(
            ($validated['document_type'] ?: 'letter').' '.$validated['recipient_name']
        ).'.pdf');
    }
}
