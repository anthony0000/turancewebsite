@include('admin.partials.document-fonts')

.staff-contract-document {
    width: 100%;
    color: #20242c;
    background: #ffffff;
    border: 1px solid #e4e0d5;
    font-family: 'Urbanist', sans-serif;
    font-size: 10.5px;
    line-height: 1.55;
}

.staff-contract-document * {
    box-sizing: border-box;
}

.staff-contract-header,
.staff-contract-body,
.staff-contract-footer {
    padding-left: 42px;
    padding-right: 42px;
}

.staff-contract-header {
    padding-top: 38px;
    padding-bottom: 30px;
    color: #ffffff;
    background: #28313a;
}

.staff-contract-header-table,
.staff-contract-meta-table,
.staff-contract-signature-table,
.staff-contract-footer-table {
    width: 100%;
    border-collapse: collapse;
}

.staff-contract-header-table td,
.staff-contract-meta-table td,
.staff-contract-signature-table td,
.staff-contract-footer-table td {
    vertical-align: top;
}

.staff-contract-brand {
    width: 58%;
}

.staff-contract-brand img {
    display: block;
    width: auto;
    max-width: 150px;
    max-height: 44px;
    margin-bottom: 12px;
}

.staff-contract-brand strong {
    display: block;
    font-size: 18px;
    letter-spacing: -0.03em;
}

.staff-contract-brand span,
.staff-contract-header-meta span,
.staff-contract-kicker,
.staff-contract-section-label,
.staff-contract-meta-label,
.staff-contract-signature-label,
.staff-contract-footer-label {
    display: block;
    color: #aeb6bd;
    font-size: 8px;
    font-weight: 700;
    letter-spacing: 0.16em;
    text-transform: uppercase;
}

.staff-contract-header-meta {
    width: 42%;
    text-align: right;
}

.staff-contract-header-meta strong {
    display: block;
    margin-top: 7px;
    font-size: 13px;
}

.staff-contract-title {
    margin: 42px 0 0;
    font-size: 29px;
    line-height: 1.08;
    letter-spacing: -0.04em;
}

.staff-contract-subtitle {
    max-width: 470px;
    margin: 10px 0 0;
    color: #d6dbe0;
    font-size: 11px;
}

.staff-contract-body {
    padding-top: 30px;
    padding-bottom: 12px;
}

.staff-contract-project {
    margin-bottom: 26px;
    padding: 18px 20px;
    border-left: 4px solid #b8860b;
    background: #f7f5f0;
}

.staff-contract-project h2 {
    margin: 5px 0 4px;
    color: #20242c;
    font-size: 20px;
    letter-spacing: -0.03em;
}

.staff-contract-project p,
.staff-contract-copy,
.staff-contract-note {
    margin: 0;
    color: #69717a;
}

.staff-contract-meta-table {
    margin-bottom: 28px;
}

.staff-contract-meta-table td {
    width: 25%;
    padding: 14px 12px;
    border: 1px solid #e5e7e9;
}

.staff-contract-meta-table td:first-child {
    padding-left: 0;
    border-left: 0;
}

.staff-contract-meta-table td:last-child {
    padding-right: 0;
    border-right: 0;
}

.staff-contract-meta-label {
    color: #8b9299;
}

.staff-contract-meta-value {
    display: block;
    margin-top: 6px;
    color: #20242c;
    font-size: 12px;
    font-weight: 700;
}

.staff-contract-section {
    padding: 22px 0;
    border-top: 1px solid #e5e7e9;
}

.staff-contract-section-label {
    color: #b8860b;
}

.staff-contract-section h2 {
    margin: 6px 0 10px;
    color: #20242c;
    font-size: 17px;
    letter-spacing: -0.02em;
}

.staff-contract-copy {
    white-space: pre-line;
}

.staff-contract-copy p,
.staff-contract-copy div {
    margin: 0 0 8px;
}

.staff-contract-copy p:last-child,
.staff-contract-copy div:last-child {
    margin-bottom: 0;
}

.staff-contract-copy ul,
.staff-contract-copy ol {
    margin: 0 0 8px;
    padding-left: 20px;
}

.staff-contract-copy li + li {
    margin-top: 4px;
}

.staff-contract-price {
    margin-top: 4px;
    color: #20242c;
    font-size: 25px;
    font-weight: 700;
    letter-spacing: -0.04em;
}

.staff-contract-price-note {
    margin-top: 4px;
    color: #69717a;
}

.staff-contract-signatures {
    margin-top: 12px;
    page-break-inside: avoid;
}

.staff-contract-signatures td {
    width: 50%;
    padding: 20px 22px 0 0;
}

.staff-contract-signatures td + td {
    padding-right: 0;
    padding-left: 22px;
}

.staff-contract-signature-line {
    height: 34px;
    margin-bottom: 8px;
    border-bottom: 1px solid #69717a;
}

.staff-contract-signature-value {
    min-height: 16px;
    color: #20242c;
    font-weight: 700;
}

.staff-contract-signature-label {
    margin-top: 4px;
    color: #8b9299;
}

.staff-contract-footer {
    padding-top: 22px;
    padding-bottom: 28px;
    border-top: 1px solid #e5e7e9;
}

.staff-contract-footer-table td:first-child {
    width: 65%;
}

.staff-contract-footer-table td:last-child {
    text-align: right;
}

.staff-contract-footer p {
    margin: 5px 0 0;
    color: #69717a;
    font-size: 9px;
}
