@extends('admin.layouts.app')

@php
    $defaults = array_merge([
        'document_type' => 'Official Letter',
        'greeting' => 'Dear Sir/Madam,',
        'closing' => 'Kind regards,',
        'signatory_name' => '',
        'signatory_title' => '',
    ], $defaults ?? []);
    $letterDate = old('date', now()->format('Y-m-d'));
    try {
        $letterDateLabel = \Illuminate\Support\Carbon::parse($letterDate)->format('F j, Y');
    } catch (\Throwable $exception) {
        $letterDateLabel = $letterDate;
    }
    $letter = [
        'document_type' => old('document_type', $defaults['document_type']),
        'date' => $letterDate,
        'date_label' => $letterDateLabel,
        'recipient_name' => old('recipient_name'),
        'recipient_role' => old('recipient_role'),
        'recipient_company' => old('recipient_company'),
        'recipient_address' => old('recipient_address'),
        'subject' => old('subject'),
        'greeting' => old('greeting', $defaults['greeting']),
        'body' => old('body'),
        'closing' => old('closing', $defaults['closing']),
        'signatory_name' => old('signatory_name', $defaults['signatory_name']),
        'signatory_title' => old('signatory_title', $defaults['signatory_title']),
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

        .letter-editor textarea[name="body"] {
            min-height: 225px;
        }

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
    </style>

    <div class="letter-generator">
        <section class="panel hero-banner">
            <div>
                <span class="eyebrow">Corporate document studio</span>
                <h1>Write it on the Turance letterhead.</h1>
                <p class="section-copy">
                    Create a polished letter, memo, or basic company document with the Turance corporate identity
                    already in place. Your changes appear in the preview as you write, then download the finished PDF.
                </p>
            </div>
            <div class="hero-callout">
                <div class="callout-card">
                    <span class="metric-label">Default background</span>
                    <strong>Corporate identity</strong>
                    <p>The supplied Turance letterhead stays fixed while your document content remains editable.</p>
                </div>
            </div>
        </section>

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
            <form class="panel panel-padded letter-editor" method="POST" action="{{ route('admin.letters.pdf') }}"
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
                            <label for="body">Body</label>
                            <textarea id="body" name="body" data-letter-field="body" required maxlength="20000"
                                placeholder="Write the main message here...">{{ $letter['body'] }}</textarea>
                            <p class="form-help">Plain text is kept deliberately clean for reliable PDF export.</p>
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
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <button class="ghost-button" type="reset">Clear edits</button>
                        <button class="button" type="submit">Download PDF</button>
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
            </section>
        </div>
    </div>

    <script>
        (() => {
            const form = document.querySelector('[data-letter-form]');

            if (!form) {
                return;
            }

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
                form.querySelectorAll('[data-letter-field]').forEach((field) => {
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
                    const value = form.querySelector('[data-letter-field="body"]')?.value.trim() || '';
                    body.textContent = value || 'Your letter content will appear here.';
                    body.classList.toggle('is-empty', !value);
                }
            };

            form.addEventListener('input', updatePreview);
            form.addEventListener('change', updatePreview);
            form.addEventListener('reset', () => window.requestAnimationFrame(updatePreview));
            updatePreview();
        })();
    </script>
@endsection
