<?php

namespace App\Http\Controllers;

use App\Support\DocumentBranding;
use App\Support\DocumentTypography;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AdminLetterController extends Controller
{
    public function create(): View
    {
        return view('admin.letters.create', [
            'backgroundSrc' => DocumentBranding::logoSource(config('letters.background_path')),
            'defaults' => config('letters.defaults', []),
            'documentTypes' => config('letters.document_types', []),
        ]);
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
