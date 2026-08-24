@include('admin.partials.document-fonts')

.letter-document {
    position: relative;
    width: 210mm;
    min-height: 297mm;
    overflow: hidden;
    color: #17202b;
    background: #ffffff;
    font-family: 'Urbanist', sans-serif;
    font-size: 11px;
    line-height: 1.55;
}

.letter-document *,
.letter-document *::before,
.letter-document *::after {
    box-sizing: border-box;
}

.letterhead-background {
    position: absolute;
    z-index: 0;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: fill;
}

.letter-content {
    position: relative;
    z-index: 1;
    padding: 68mm 20mm 48mm;
}

.letter-document-header {
    display: flex;
    align-items: center;
    gap: 10px;
    min-height: 13px;
}

.letter-document-type {
    color: #aa7600;
    font-size: 8px;
    font-weight: 700;
    letter-spacing: 0.18em;
    text-transform: uppercase;
}

.letter-document-rule {
    display: block;
    width: 28px;
    height: 1px;
    background: #c79617;
}

.letter-date {
    margin-top: 19px;
    color: #56606c;
    font-size: 10.5px;
}

.letter-recipient {
    display: block;
    margin-top: 22px;
    color: #17202b;
}

.letter-recipient strong,
.letter-recipient-line,
.letter-recipient-address {
    display: block;
}

.letter-recipient strong {
    font-size: 12px;
    font-weight: 700;
}

.letter-recipient-line,
.letter-recipient-address {
    color: #56606c;
    white-space: pre-line;
}

.letter-copy {
    margin-top: 25px;
}

.letter-subject {
    display: block;
    margin-bottom: 21px;
    color: #17202b;
}

.letter-subject-label {
    margin-right: 5px;
    color: #aa7600;
    font-weight: 700;
}

.letter-greeting,
.letter-body,
.letter-signoff p {
    margin: 0;
}

.letter-greeting {
    margin-bottom: 16px;
}

.letter-body {
    min-height: 100px;
    color: #303a46;
    white-space: pre-line;
}

.letter-signoff {
    margin-top: 24px;
}

.letter-signoff p {
    margin-bottom: 18px;
}

.letter-signoff strong,
.letter-signatory-title {
    display: block;
}

.letter-signoff strong {
    font-size: 12px;
}

.letter-signatory-title {
    margin-top: 2px;
    color: #56606c;
}

.letter-preview-placeholder,
.letter-body.is-empty {
    color: #929aa2;
}

.letter-body.is-empty {
    display: block !important;
}

.is-empty {
    display: none !important;
}
