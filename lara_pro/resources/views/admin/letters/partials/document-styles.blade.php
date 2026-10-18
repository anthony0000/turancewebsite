@include('admin.partials.document-fonts')

.letter-document {
    position: relative;
    width: 210mm;
    height: 297mm;
    min-height: 297mm;
    overflow: hidden;
    color: #17202b;
    background: transparent;
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
    display: block;
    object-fit: contain;
    object-position: center top;
}

.letter-content {
    position: absolute;
    top: 68mm;
    right: 20mm;
    bottom: 48mm;
    left: 20mm;
    z-index: 1;
    overflow: hidden;
}

.letter-date {
    margin-top: 12px;
    color: #56606c;
    font-size: 10.5px;
}

.letter-recipient {
    display: block;
    margin-top: 16px;
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
}

.letter-recipient-address > span {
    white-space: pre-line;
}

.letter-copy {
    margin-top: 18px;
}

.letter-subject {
    display: block;
    margin-bottom: 15px;
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
    margin-bottom: 12px;
}

.letter-body {
    min-height: 100px;
    color: #303a46;
    overflow-wrap: anywhere;
}

.letter-body p {
    margin: 0 0 0.5em;
}

.letter-body ul,
.letter-body ol {
    margin: 0 0 0.7em 1.3em;
    padding: 0;
}

.letter-body li {
    margin-bottom: 0.25em;
}

.letter-signoff {
    margin-top: 16px;
}

.letter-signoff p {
    margin-bottom: 10px;
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
