@php
    $letter = array_merge([
        'document_type' => 'Official Letter',
        'date_label' => '',
        'recipient_name' => '',
        'recipient_role' => '',
        'recipient_company' => '',
        'recipient_address' => '',
        'subject' => '',
        'greeting' => 'Dear Sir/Madam,',
        'body' => '',
        'closing' => 'Kind regards,',
        'signatory_name' => '',
        'signatory_title' => '',
    ], $letter ?? []);
@endphp

<div class="letter-document" data-letter-document>
    @if ($backgroundSrc)
        <img class="letterhead-background" src="{{ $backgroundSrc }}" alt="">
    @endif

    <div class="letter-content">
        <header class="letter-document-header">
            <span class="letter-document-type" data-letter-preview="document_type">{{ $letter['document_type'] }}</span>
            <span class="letter-document-rule" aria-hidden="true"></span>
        </header>

        <div class="letter-date" data-letter-preview="date">{{ $letter['date_label'] }}</div>

        <section class="letter-recipient" aria-label="Recipient">
            <strong data-letter-preview="recipient_name">{{ $letter['recipient_name'] ?: 'Recipient name' }}</strong>
            <span class="letter-recipient-line {{ blank($letter['recipient_role']) ? 'is-empty' : '' }}"
                data-letter-preview-wrap="recipient_role">
                <span data-letter-preview="recipient_role">{{ $letter['recipient_role'] }}</span>
            </span>
            <span class="letter-recipient-line {{ blank($letter['recipient_company']) ? 'is-empty' : '' }}"
                data-letter-preview-wrap="recipient_company">
                <span data-letter-preview="recipient_company">{{ $letter['recipient_company'] }}</span>
            </span>
            <span class="letter-recipient-line letter-recipient-address {{ blank($letter['recipient_address']) ? 'is-empty' : '' }}"
                data-letter-preview-wrap="recipient_address">
                <span data-letter-preview="recipient_address">{{ $letter['recipient_address'] }}</span>
            </span>
        </section>

        <section class="letter-copy">
            <div class="letter-subject {{ blank($letter['subject']) ? 'is-empty' : '' }}"
                data-letter-preview-wrap="subject">
                <span class="letter-subject-label">Subject:</span>
                <strong data-letter-preview="subject">{{ $letter['subject'] }}</strong>
            </div>

            <p class="letter-greeting" data-letter-preview="greeting">{{ $letter['greeting'] }}</p>

            <div class="letter-body {{ blank($letter['body']) ? 'is-empty' : '' }}" data-letter-preview="body">
                {{ $letter['body'] ?: 'Your letter content will appear here.' }}
            </div>

            <div class="letter-signoff">
                <p data-letter-preview="closing">{{ $letter['closing'] }}</p>
                <strong data-letter-preview="signatory_name">{{ $letter['signatory_name'] ?: 'Your name' }}</strong>
                <span class="letter-signatory-title {{ blank($letter['signatory_title']) ? 'is-empty' : '' }}"
                    data-letter-preview-wrap="signatory_title">
                    <span data-letter-preview="signatory_title">{{ $letter['signatory_title'] }}</span>
                </span>
            </div>
        </section>
    </div>
</div>
