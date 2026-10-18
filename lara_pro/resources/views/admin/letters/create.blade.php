@extends('admin.layouts.app')

@php
    $defaults = array_merge([
        'document_type' => 'Official Letter',
        'greeting' => 'Dear Sir/Madam,',
        'closing' => 'Kind regards,',
        'signatory_name' => '',
        'signatory_title' => '',
    ], $defaults ?? []);
    $saved = $draft?->content ?? [];
    $savedValue = fn (string $key, string $default = '') => array_key_exists($key, $saved) ? ($saved[$key] ?? '') : $default;
    $letterDate = old('date', $savedValue('date', now()->format('Y-m-d')));
    try {
        $letterDateLabel = filled($letterDate) ? \Illuminate\Support\Carbon::parse($letterDate)->format('F j, Y') : '';
    } catch (\Throwable $exception) {
        $letterDateLabel = $letterDate;
    }
    $letter = [
        'document_type' => old('document_type', $savedValue('document_type', $defaults['document_type'])),
        'date' => $letterDate,
        'date_label' => $letterDateLabel,
        'recipient_name' => old('recipient_name', $savedValue('recipient_name')),
        'recipient_role' => old('recipient_role', $savedValue('recipient_role')),
        'recipient_company' => old('recipient_company', $savedValue('recipient_company')),
        'recipient_address' => old('recipient_address', $savedValue('recipient_address')),
        'subject' => old('subject', $savedValue('subject')),
        'greeting' => old('greeting', $savedValue('greeting', $defaults['greeting'])),
        'body' => old('body', $savedValue('body')),
        'closing' => old('closing', $savedValue('closing', $defaults['closing'])),
        'signatory_name' => old('signatory_name', $savedValue('signatory_name', $defaults['signatory_name'])),
        'signatory_title' => old('signatory_title', $savedValue('signatory_title', $defaults['signatory_title'])),
    ];
@endphp

@section('title', 'Letterhead Generator | Admin')

@section('content')
    <style>
        @include('admin.letters.partials.document-styles')

        .letter-generator {
            display: grid;
            gap: 24px;
        }

        .letter-generator .section-copy {
            max-width: 760px;
            margin: 0;
            color: var(--muted);
            line-height: 1.65;
        }

        .letter-workspace {
            display: grid;
            grid-template-columns: minmax(340px, 0.86fr) minmax(0, 1.14fr);
            gap: 24px;
            align-items: start;
        }

        .letter-editor {
            display: grid;
            gap: 22px;
        }

        .letter-editor .form-section {
            display: grid;
            gap: 17px;
        }

        .letter-editor .section-copy {
            margin-top: 5px;
            font-size: 13px;
        }

        .letter-editor textarea {
            min-height: 92px;
            resize: vertical;
        }

        .letter-rich-editor {
            min-height: 225px;
            padding: 14px;
            border: 1px solid var(--border, #d8dce0);
            border-radius: 10px;
            background: #fff;
            line-height: 1.6;
            outline: none;
            overflow-wrap: anywhere;
        }

        .letter-rich-editor:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(184, 134, 11, 0.12);
        }

        .letter-rich-editor:empty::before {
            content: attr(data-placeholder);
            color: var(--muted);
        }

        .letter-rich-editor p { margin: 0 0 0.7em; }
        .letter-rich-editor ul, .letter-rich-editor ol { margin: 0 0 0.7em 1.4em; padding: 0; }

        .letter-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin: 8px 0;
        }

        .letter-toolbar button {
            min-width: 36px;
            padding: 6px 10px;
            border: 1px solid var(--border, #d8dce0);
            border-radius: 7px;
            background: #fff;
            cursor: pointer;
        }

        .letter-toolbar button:hover { background: rgba(184, 134, 11, 0.1); }

        .letter-drafts { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
        .letter-drafts a { color: var(--accent-soft); }
        .letter-drafts select { max-width: 300px; }

        .letter-save-status { margin: 0; color: #34734e; font-weight: 700; }

        .letter-editor .form-help {
            margin: -7px 0 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.55;
        }

        .letter-preview-panel {
            position: sticky;
            top: 24px;
            padding: 18px;
            background: rgba(255, 255, 255, 0.66);
        }

        .letter-preview-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 14px;
        }

        .letter-preview-head h2 {
            margin: 0;
            font-size: 18px;
        }

        .letter-preview-head p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 12px;
        }

        .letter-preview-badge {
            flex: 0 0 auto;
            padding: 7px 9px;
            border: 1px solid rgba(184, 134, 11, 0.25);
            border-radius: 999px;
            color: var(--accent-soft);
            background: rgba(184, 134, 11, 0.08);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .letter-preview-stage {
            overflow: auto;
            padding: 24px;
            border: 1px solid rgba(184, 134, 11, 0.13);
            border-radius: 16px;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.76), rgba(246, 238, 220, 0.8)),
                #f5efe3;
        }

        .letter-preview-sheet {
            width: min(100%, 650px);
            margin: 0 auto;
            box-shadow: 0 18px 38px rgba(74, 57, 27, 0.18);
        }

        .letter-preview-sheet .letter-document {
            width: 100%;
            height: auto;
            min-height: 0;
            aspect-ratio: 210 / 297;
            font-size: 1.35vw;
        }

        .letter-preview-sheet .letter-content {
            padding: 22.5% 9.5% 15.5%;
        }

        .letter-preview-sheet .letter-date {
            margin-top: 4.2%;
            font-size: clamp(7px, 0.95vw, 10.5px);
        }

        .letter-preview-sheet .letter-recipient {
            margin-top: 5.2%;
        }

        .letter-preview-sheet .letter-recipient strong {
            font-size: clamp(8px, 1.05vw, 12px);
        }

        .letter-preview-sheet .letter-recipient-line,
        .letter-preview-sheet .letter-recipient-address,
        .letter-preview-sheet .letter-body,
        .letter-preview-sheet .letter-greeting,
        .letter-preview-sheet .letter-signoff,
        .letter-preview-sheet .letter-signoff strong,
        .letter-preview-sheet .letter-signatory-title {
            font-size: clamp(7px, 0.95vw, 11px);
        }

        .letter-preview-sheet .letter-copy {
            margin-top: 5.8%;
        }

        .letter-preview-sheet .letter-subject {
            margin-bottom: 4.8%;
            font-size: clamp(7px, 0.95vw, 11px);
        }

        .letter-preview-sheet .letter-greeting {
            margin-bottom: 3.8%;
        }

        .letter-preview-sheet .letter-body {
            min-height: 70px;
        }

        .letter-preview-sheet .letter-signoff {
            margin-top: 5.3%;
        }

        .letter-preview-sheet .letter-signoff p {
            margin-bottom: 4.2%;
        }

        .letter-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-top: 4px;
        }

        .letter-actions-note {
            max-width: 255px;
            margin: 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.5;
        }

        .validation-summary {
            padding: 16px 18px;
            border: 1px solid rgba(185, 74, 61, 0.3);
            border-radius: 16px;
            background: rgba(185, 74, 61, 0.08);
            color: var(--danger);
        }

        .validation-summary ul {
            margin: 8px 0 0 18px;
        }

        @media (max-width: 1100px) {
            .letter-workspace {
                grid-template-columns: 1fr;
            }

            .letter-preview-panel {
                position: static;
            }

            .letter-preview-sheet .letter-document {
                font-size: 1.8vw;
            }
        }

        @media (max-width: 620px) {
            .letter-preview-stage {
                padding: 12px;
            }

            .letter-preview-sheet {
                width: 100%;
            }

            .letter-preview-sheet .letter-document {
                font-size: 2.35vw;
            }
        }

        /* Letter studio: a focused workspace with document-first hierarchy. */
        body.is-admin .letter-generator {
            --studio-ink: #18232d;
            --studio-muted: #687583;
            --studio-gold: #bc850c;
            gap: 20px;
            color: var(--studio-ink);
        }

        body.is-admin .letter-generator .letter-hero {
            position: relative;
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(250px, .75fr);
            gap: 32px;
            overflow: hidden;
            padding: 34px 38px;
            border: 1px solid #202c37;
            border-radius: 18px;
            background: radial-gradient(circle at 85% 15%, #33485a 0, transparent 38%), #17232e;
            box-shadow: 0 20px 42px rgba(18, 31, 43, .15);
        }

        .letter-hero::after {
            content: '';
            position: absolute;
            right: 29%;
            bottom: -90px;
            width: 220px;
            height: 220px;
            border: 1px solid rgba(213, 168, 68, .18);
            border-radius: 50%;
            pointer-events: none;
        }

        body.is-admin .letter-generator .letter-hero .eyebrow {
            margin-bottom: 14px;
            color: #e4b84f;
        }

        body.is-admin .letter-generator .letter-hero h1 {
            max-width: 570px;
            margin: 0;
            color: #fff;
            font-family: var(--font-display);
            font-size: clamp(26px, 2.4vw, 38px) !important;
            font-weight: 700 !important;
            letter-spacing: -.045em;
            line-height: 1.12;
        }

        body.is-admin .letter-generator .letter-hero .section-copy {
            max-width: 540px;
            margin-top: 15px;
            color: #c7d0d8;
            font-size: 14px;
            line-height: 1.65;
        }

        .letter-hero-aside {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 24px;
            padding: 23px 26px;
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 12px;
            background: rgba(255, 255, 255, .07);
        }

        .letter-hero-aside .metric-label {
            color: #e4b84f;
            font-size: 10px;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .letter-hero-aside strong {
            display: block;
            margin-top: 8px;
            color: #fff;
            font-size: 18px;
        }

        .letter-hero-aside p {
            max-width: 260px;
            margin: 8px 0 0;
            color: #c7d0d8;
            font-size: 12px;
            line-height: 1.6;
        }

        .letter-hero-aside .letter-hero-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .letter-hero-meta span {
            padding: 6px 9px;
            border: 1px solid rgba(228, 184, 79, .3);
            border-radius: 4px;
            color: #f2d387;
            font-size: 10px;
            font-weight: 700;
        }

        body.is-admin .letter-generator .letter-drafts {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px 19px;
            border: 1px solid #e6e9eb;
            border-radius: 10px;
            background: #fff;
        }

        .letter-drafts-title { display: grid; gap: 3px; }
        .letter-drafts-title strong { font-size: 14px; }
        .letter-drafts-title span { color: var(--studio-muted); font-size: 12px; }
        .letter-drafts-controls { display: flex; align-items: center; flex-wrap: wrap; gap: 10px; }

        body.is-admin .letter-drafts .letter-new-link {
            display: inline-flex;
            align-items: center;
            min-height: 38px;
            padding: 0 14px;
            border: 1px solid #d8dfe3;
            border-radius: 6px;
            color: var(--studio-ink);
            font-size: 12px;
            font-weight: 700;
        }

        .letter-drafts label { color: var(--studio-muted); font-size: 12px; font-weight: 700; }
        body.is-admin .letter-drafts select { min-height: 38px; max-width: 320px; padding: 6px 10px; border: 1px solid #d8dfe3; border-radius: 6px; background: #fff; color: var(--studio-ink); font-size: 12px; }

        body.is-admin .letter-workspace {
            grid-template-columns: minmax(340px, .9fr) minmax(0, 1.1fr);
            gap: 20px;
        }

        body.is-admin .letter-editor,
        body.is-admin .letter-preview-panel {
            border: 1px solid #e6e9eb;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 10px 28px rgba(22, 34, 45, .05);
        }

        body.is-admin .letter-editor { gap: 0; padding: 0 24px; }
        body.is-admin .letter-editor .form-section { gap: 17px; padding: 27px 0; border-bottom: 1px solid #e9edef; }
        body.is-admin .letter-editor .form-section h2 { margin: 0; color: var(--studio-ink); font-size: 17px; font-weight: 700; letter-spacing: -.025em; }
        body.is-admin .letter-editor .form-section .eyebrow { margin-bottom: 6px; color: #a57308; font-size: 10px; }
        body.is-admin .letter-editor .section-copy { margin: 5px 0 0; color: var(--studio-muted); font-size: 12px; line-height: 1.55; }
        body.is-admin .letter-editor .form-grid { gap: 15px; }
        body.is-admin .letter-editor .field,
        body.is-admin .letter-editor .field-full { gap: 6px; }
        body.is-admin .letter-editor label { color: #45535f; font-size: 11px; font-weight: 700; letter-spacing: .02em; text-transform: none; }
        body.is-admin .letter-editor input,
        body.is-admin .letter-editor select,
        body.is-admin .letter-editor textarea,
        body.is-admin .letter-rich-editor { min-height: 42px; padding: 10px 12px; border: 1px solid #dce2e5; border-radius: 6px; background: #fff; color: var(--studio-ink); box-shadow: none; font-size: 13px; }
        body.is-admin .letter-editor textarea { min-height: 78px; }
        body.is-admin .letter-rich-editor { min-height: 225px; line-height: 1.65; }
        body.is-admin .letter-rich-editor:focus { border-color: #b8860b; box-shadow: 0 0 0 3px rgba(184, 134, 11, .12); }
        body.is-admin .letter-toolbar { margin: 5px 0 8px; }
        body.is-admin .letter-toolbar button { min-width: 34px; min-height: 32px; padding: 5px 9px; border-color: #dce2e5; border-radius: 5px; color: #45535f; font-size: 12px; }
        body.is-admin .letter-toolbar button:hover { border-color: #bc850c; color: #805a08; }

        body.is-admin .letter-preview-panel { top: 20px; padding: 20px; }
        body.is-admin .letter-preview-head { align-items: center; margin-bottom: 16px; }
        body.is-admin .letter-preview-head .eyebrow { margin-bottom: 4px; color: #a57308; }
        body.is-admin .letter-preview-head h2 { color: var(--studio-ink); font-size: 18px; font-weight: 700; }
        body.is-admin .letter-preview-head p { color: var(--studio-muted); font-size: 12px; }
        body.is-admin .letter-preview-badge { border-color: #e6d6a8; color: #805a08; background: #fff8e6; }
        body.is-admin .letter-preview-stage { padding: 22px; border-color: #e6e9eb; border-radius: 8px; background: #edf0f2; }
        .letter-preview-sheet { width: 100%; max-width: 650px; margin: 0 auto; }
        body.is-admin .letter-preview-sheet .letter-document { width: 210mm; height: 297mm; min-height: 297mm; aspect-ratio: auto; background: #fff; font-size: 11px; line-height: 1.55; transform-origin: top left; }
        body.is-admin .letter-preview-sheet .letter-content { top: 68mm; right: 20mm; bottom: 48mm; left: 20mm; padding: 0; }
        body.is-admin .letter-preview-sheet .letter-date { margin-top: 12px; font-size: 10.5px; }
        body.is-admin .letter-preview-sheet .letter-recipient { margin-top: 16px; }
        body.is-admin .letter-preview-sheet .letter-recipient strong,
        body.is-admin .letter-preview-sheet .letter-signoff strong { font-size: 12px; }
        body.is-admin .letter-preview-sheet .letter-recipient-line,
        body.is-admin .letter-preview-sheet .letter-recipient-address,
        body.is-admin .letter-preview-sheet .letter-body,
        body.is-admin .letter-preview-sheet .letter-greeting,
        body.is-admin .letter-preview-sheet .letter-signoff,
        body.is-admin .letter-preview-sheet .letter-signatory-title { font-size: 11px; }
        body.is-admin .letter-preview-sheet .letter-copy { margin-top: 18px; }
        body.is-admin .letter-preview-sheet .letter-subject { margin-bottom: 15px; font-size: 11px; }
        body.is-admin .letter-preview-sheet .letter-greeting { margin-bottom: 12px; }
        body.is-admin .letter-preview-sheet .letter-body { min-height: 100px; }
        body.is-admin .letter-preview-sheet .letter-signoff { margin-top: 16px; }
        body.is-admin .letter-preview-sheet .letter-signoff p { margin-bottom: 10px; }

        .letter-preview-guidance { display: flex; gap: 8px; align-items: flex-start; margin: 13px 2px 0; color: #60707d; font-size: 11px; line-height: 1.5; }
        .letter-preview-guidance::before { content: '●'; color: #b8860b; font-size: 9px; line-height: 1.8; }
        .letter-preview-overflow { display: none; margin: 12px 2px 0; padding: 10px 12px; border: 1px solid #e8d7a7; border-radius: 6px; background: #fffaeb; color: #684b10; font-size: 11px; line-height: 1.5; }
        .letter-preview-overflow.is-visible { display: block; }
        body.is-admin .letter-actions { padding: 20px 0 24px; }
        body.is-admin .letter-actions-note { color: var(--studio-muted); }
        .letter-action-buttons { display: flex; flex-wrap: wrap; gap: 9px; }

        @media (max-width: 1250px) {
            body.is-admin .letter-workspace { grid-template-columns: 1fr; }
            body.is-admin .letter-preview-panel { position: static; }
        }
        @media (max-width: 700px) {
            body.is-admin .letter-generator .letter-hero { grid-template-columns: 1fr; padding: 25px; }
            .letter-hero-aside { gap: 12px; }
            body.is-admin .letter-generator .letter-drafts { align-items: stretch; flex-direction: column; }
            .letter-drafts-controls { align-items: stretch; }
            body.is-admin .letter-drafts select { max-width: none; width: 100%; }
            body.is-admin .letter-editor { padding: 0 17px; }
            body.is-admin .letter-editor .form-grid { grid-template-columns: 1fr; }
            body.is-admin .letter-preview-stage { padding: 12px; }
        }
    </style>

    <div class="letter-generator">
        <section class="letter-hero">
            <div>
                <span class="eyebrow">Turance / Document studio</span>
                <h1>Write it on the Turance letterhead.</h1>
                <p class="section-copy">
                    Create a letter or memo with the company identity already in place. See every edit on the page,
                    then export a ready-to-send PDF.
                </p>
            </div>
            <div class="letter-hero-aside">
                <div>
                    <span class="metric-label">On brand, from the first line</span>
                    <strong>Corporate identity</strong>
                    <p>The official Turance letterhead is applied automatically to every page.</p>
                </div>
                <div class="letter-hero-meta"><span>Live preview</span><span>A4 export</span><span>Saved drafts</span></div>
            </div>
        </section>

        <div class="letter-drafts">
            <div class="letter-drafts-title"><strong>{{ $draft ? 'Editing saved draft' : 'New document' }}</strong><span>{{ $draft ? 'Changes can be saved to this draft.' : 'Start writing or open a previous draft.' }}</span></div>
            <div class="letter-drafts-controls">
                <a class="letter-new-link" href="{{ route('admin.letters.create') }}">+ New letter</a>
                @if ($drafts->isNotEmpty())
                    <label for="saved_letter">Saved drafts</label>
                    <select id="saved_letter" aria-label="Open a saved draft" data-draft-picker>
                        <option value="">Choose a draft</option>
                        @foreach ($drafts as $savedDraft)
                            <option value="{{ route('admin.letters.drafts.edit', $savedDraft) }}" @selected($draft?->is($savedDraft))>
                                {{ ($savedDraft->content['subject'] ?? null) ?: (($savedDraft->content['recipient_name'] ?? null) ?: 'Untitled letter') }} · {{ $savedDraft->updated_at->format('M j, Y') }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>

        @if (session('status'))
            <p class="letter-save-status" role="status">{{ session('status') }}</p>
        @endif

        @if ($errors->any())
            <div class="validation-summary">
                <strong>Complete the highlighted document details.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="letter-workspace">
            <form class="panel panel-padded letter-editor" method="POST" action="{{ $draft ? route('admin.letters.drafts.update', $draft) : route('admin.letters.drafts.store') }}"
                data-letter-form>
                @csrf

                <section class="form-section">
                    <div>
                        <span class="eyebrow">01 / Document setup</span>
                        <h2>Give the document its context</h2>
                        <p class="section-copy">Choose the document type and date that will appear on the finished letter.</p>
                    </div>
                    <div class="form-grid">
                        <div class="field">
                            <label for="document_type">Document type</label>
                            <select id="document_type" name="document_type" data-letter-field="document_type" required>
                                @foreach ($documentTypes as $documentType)
                                    <option value="{{ $documentType }}" @selected($letter['document_type'] === $documentType)>{{ $documentType }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="date">Date</label>
                            <input id="date" type="date" name="date" value="{{ $letter['date'] }}" data-letter-field="date" required>
                        </div>
                    </div>
                </section>

                <section class="form-section">
                    <div>
                        <span class="eyebrow">02 / Recipient</span>
                        <h2>Address the document</h2>
                        <p class="section-copy">Add only the recipient details you need. Blank optional lines stay out of the PDF.</p>
                    </div>
                    <div class="form-grid">
                        <div class="field">
                            <label for="recipient_name">Recipient name</label>
                            <input id="recipient_name" type="text" name="recipient_name" value="{{ $letter['recipient_name'] }}"
                                data-letter-field="recipient_name" required maxlength="255" placeholder="e.g. Amaka Okafor">
                        </div>
                        <div class="field">
                            <label for="recipient_role">Role or title <span class="field-optional">Optional</span></label>
                            <input id="recipient_role" type="text" name="recipient_role" value="{{ $letter['recipient_role'] }}"
                                data-letter-field="recipient_role" maxlength="255" placeholder="e.g. Managing Director">
                        </div>
                        <div class="field">
                            <label for="recipient_company">Company <span class="field-optional">Optional</span></label>
                            <input id="recipient_company" type="text" name="recipient_company" value="{{ $letter['recipient_company'] }}"
                                data-letter-field="recipient_company" maxlength="255" placeholder="e.g. Asterion Holdings">
                        </div>
                        <div class="field-full">
                            <label for="recipient_address">Address <span class="field-optional">Optional</span></label>
                            <textarea id="recipient_address" name="recipient_address" data-letter-field="recipient_address"
                                maxlength="1500" placeholder="Street, city, state, country">{{ $letter['recipient_address'] }}</textarea>
                        </div>
                    </div>
                </section>

                <section class="form-section">
                    <div>
                        <span class="eyebrow">03 / Message</span>
                        <h2>Write the document</h2>
                        <p class="section-copy">Use separate lines or paragraphs in the body. The preview keeps your spacing when the PDF is generated.</p>
                    </div>
                    <div class="form-grid">
                        <div class="field-full">
                            <label for="subject">Subject <span class="field-optional">Optional</span></label>
                            <input id="subject" type="text" name="subject" value="{{ $letter['subject'] }}" data-letter-field="subject"
                                maxlength="255" placeholder="e.g. Confirmation of project engagement">
                        </div>
                        <div class="field-full">
                            <label for="greeting">Greeting</label>
                            <input id="greeting" type="text" name="greeting" value="{{ $letter['greeting'] }}" data-letter-field="greeting"
                                required maxlength="255">
                        </div>
                        <div class="field-full">
                            <label id="body-label">Body</label>
                            <div class="letter-toolbar" role="toolbar" aria-label="Body formatting">
                                <button type="button" data-format="bold" aria-label="Bold"><strong>B</strong></button>
                                <button type="button" data-format="italic" aria-label="Italic"><em>I</em></button>
                                <button type="button" data-format="underline" aria-label="Underline"><u>U</u></button>
                                <button type="button" data-format="insertUnorderedList" aria-label="Bulleted list">• List</button>
                                <button type="button" data-format="insertOrderedList" aria-label="Numbered list">1. List</button>
                            </div>
                            <div id="body" class="letter-rich-editor" contenteditable="true" role="textbox" aria-multiline="true"
                                aria-labelledby="body-label" data-placeholder="Write the main message here..." data-body-editor>{!! \App\Support\LetterBody::clean($letter['body']) !!}</div>
                            <input type="hidden" name="body" value="{{ $letter['body'] }}" data-letter-field="body">
                            <p class="form-help">Format your message with bold, italic, underline, and lists.</p>
                        </div>
                    </div>
                </section>

                <section class="form-section">
                    <div>
                        <span class="eyebrow">04 / Sign-off</span>
                        <h2>Close with the authorised sender</h2>
                        <p class="section-copy">The signatory name is required for a downloadable document. The title is optional.</p>
                    </div>
                    <div class="form-grid">
                        <div class="field">
                            <label for="closing">Closing</label>
                            <input id="closing" type="text" name="closing" value="{{ $letter['closing'] }}" data-letter-field="closing"
                                required maxlength="120">
                        </div>
                        <div class="field">
                            <label for="signatory_name">Signatory name</label>
                            <input id="signatory_name" type="text" name="signatory_name" value="{{ $letter['signatory_name'] }}"
                                data-letter-field="signatory_name" required maxlength="255" placeholder="e.g. Tony Stark">
                        </div>
                        <div class="field-full">
                            <label for="signatory_title">Signatory title <span class="field-optional">Optional</span></label>
                            <input id="signatory_title" type="text" name="signatory_title" value="{{ $letter['signatory_title'] }}"
                                data-letter-field="signatory_title" maxlength="255" placeholder="e.g. Managing Director">
                        </div>
                    </div>
                </section>

                <div class="letter-actions">
                    <p class="letter-actions-note">The generated PDF uses the same letterhead background shown in the preview.</p>
                    <div class="letter-action-buttons">
                        <button class="ghost-button" type="reset">Clear edits</button>
                        <button class="ghost-button" type="submit" formnovalidate>Save draft</button>
                        <button class="button" type="submit" formaction="{{ route('admin.letters.pdf') }}" data-pdf-button>Download PDF</button>
                    </div>
                </div>
            </form>

            <section class="panel letter-preview-panel" aria-labelledby="letter-preview-title">
                <div class="letter-preview-head">
                    <div>
                        <span class="eyebrow">Live document</span>
                        <h2 id="letter-preview-title">Letterhead preview</h2>
                        <p>Review the content placement before downloading.</p>
                    </div>
                    <span class="letter-preview-badge">A4 PDF</span>
                </div>
                <div class="letter-preview-stage">
                    <div class="letter-preview-sheet">
                        @include('admin.letters.partials.document', [
                            'letter' => $letter,
                            'backgroundSrc' => $backgroundSrc,
                        ])
                    </div>
                </div>
                <p class="letter-preview-guidance">The page uses the same type size and writing area as the PDF.</p>
                <p class="letter-preview-overflow" data-preview-overflow role="status">This document continues onto another PDF page. The preview shows page one; the export keeps text clear of the letterhead footer.</p>
            </section>
        </div>
    </div>

    <script>
        (() => {
            const form = document.querySelector('[data-letter-form]');

            if (!form) {
                return;
            }

            const editor = form.querySelector('[data-body-editor]');
            const bodyInput = form.querySelector('[data-letter-field="body"]');
            const previewSheet = document.querySelector('.letter-preview-sheet');
            const previewDocument = previewSheet?.querySelector('.letter-document');
            const previewContent = previewDocument?.querySelector('.letter-content');
            const overflowNotice = document.querySelector('[data-preview-overflow]');
            const initialBody = editor.innerHTML;
            const allowed = new Set(['P', 'BR', 'STRONG', 'EM', 'U', 'UL', 'OL', 'LI']);

            const cleanNode = (node, destination) => {
                if (node.nodeType === Node.TEXT_NODE) {
                    destination.appendChild(document.createTextNode(node.textContent));
                    return;
                }
                if (node.nodeType !== Node.ELEMENT_NODE || ['SCRIPT', 'STYLE', 'IFRAME', 'SVG', 'OBJECT'].includes(node.tagName)) {
                    return;
                }
                const tag = { B: 'STRONG', I: 'EM', DIV: 'P' }[node.tagName] || node.tagName;
                const target = allowed.has(tag) ? document.createElement(tag.toLowerCase()) : destination;
                if (target !== destination) destination.appendChild(target);
                node.childNodes.forEach(child => cleanNode(child, target));
            };

            const bodyHtml = () => {
                const clean = document.createElement('div');
                editor.childNodes.forEach(node => cleanNode(node, clean));
                return clean.innerHTML.trim();
            };

            const formatDate = (value) => {
                if (!value) {
                    return '';
                }

                const [year, month, day] = value.split('-').map(Number);
                const date = new Date(year, month - 1, day);

                return Number.isNaN(date.getTime())
                    ? value
                    : date.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
            };

            const updatePreview = () => {
                form.querySelectorAll('[data-letter-field]:not([name="body"])').forEach((field) => {
                    const key = field.dataset.letterField;
                    const value = field.value.trim();
                    const previewValue = key === 'date' ? formatDate(value) : value;

                    document.querySelectorAll(`[data-letter-preview="${key}"]`).forEach((target) => {
                        target.textContent = previewValue;
                        target.classList.toggle('is-empty', !previewValue);
                    });

                    document.querySelectorAll(`[data-letter-preview-wrap="${key}"]`).forEach((wrapper) => {
                        wrapper.classList.toggle('is-empty', !previewValue);
                    });
                });

                const body = document.querySelector('[data-letter-preview="body"]');

                if (body) {
                    const value = bodyHtml();
                    bodyInput.value = value;
                    body.innerHTML = editor.textContent.trim() ? value : 'Your letter content will appear here.';
                    body.classList.toggle('is-empty', !editor.textContent.trim());
                }

                if (previewContent && overflowNotice) {
                    overflowNotice.classList.toggle('is-visible', previewContent.scrollHeight > previewContent.clientHeight + 2);
                }
            };

            const sizePreview = () => {
                if (!previewSheet || !previewDocument) return;
                const scale = Math.min(1, previewSheet.clientWidth / previewDocument.offsetWidth);
                previewDocument.style.transform = `scale(${scale})`;
                previewSheet.style.height = `${previewDocument.offsetHeight * scale}px`;
            };

            if (previewSheet) new ResizeObserver(sizePreview).observe(previewSheet);
            window.addEventListener('load', sizePreview);

            form.querySelectorAll('[data-format]').forEach(button => {
                button.addEventListener('mousedown', event => event.preventDefault());
                button.addEventListener('click', () => {
                    editor.focus();
                    document.execCommand(button.dataset.format, false);
                    updatePreview();
                });
            });
            editor.addEventListener('paste', event => {
                event.preventDefault();
                document.execCommand('insertText', false, event.clipboardData.getData('text/plain'));
            });
            editor.addEventListener('input', updatePreview);
            form.querySelector('[data-pdf-button]').addEventListener('click', event => {
                updatePreview();
                if (!editor.textContent.trim()) {
                    event.preventDefault();
                    editor.focus();
                    editor.setAttribute('aria-invalid', 'true');
                } else {
                    editor.removeAttribute('aria-invalid');
                }
            });
            document.querySelector('[data-draft-picker]')?.addEventListener('change', event => {
                if (event.target.value) window.location.assign(event.target.value);
            });
            form.addEventListener('input', updatePreview);
            form.addEventListener('change', updatePreview);
            form.addEventListener('reset', () => window.requestAnimationFrame(() => {
                editor.innerHTML = initialBody;
                updatePreview();
            }));
            form.addEventListener('submit', updatePreview);
            updatePreview();
            sizePreview();
        })();
    </script>
@endsection
