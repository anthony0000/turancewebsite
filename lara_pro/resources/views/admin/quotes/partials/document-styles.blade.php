.quote-document {
    width: 100%;
    background: #ffffff;
    color: #20242c;
    border: 1px solid #e4e0d5;
    border-radius: 3px;
    overflow: hidden;
    font-family: "Plus Jakarta Sans", "Segoe UI", Arial, sans-serif;
    font-size: 11px;
    line-height: 1.45;
    box-shadow: 0 22px 58px rgba(21, 26, 36, 0.13);
}

.quote-document * {
    box-sizing: border-box;
}

.quote-header-band,
.quote-body {
    padding-left: 40px;
    padding-right: 40px;
}

.quote-header-band {
    padding-top: 36px;
    padding-bottom: 10px;
    background: #ffffff;
}

.quote-body {
    padding-top: 4px;
    padding-bottom: 24px;
    background: #ffffff;
}

.quote-header-table,
.quote-items-table,
.quote-summary-table,
.quote-payment-meta,
.quote-totals-table,
.quote-compact-table,
.quote-footer-table {
    width: 100%;
    border-collapse: collapse;
}

.quote-header-table td,
.quote-summary-table td,
.quote-payment-meta td,
.quote-compact-table td,
.quote-footer-table td {
    vertical-align: top;
}

.quote-header-left {
    width: 42%;
}

.quote-header-center {
    width: 25%;
    padding: 0 18px;
}

.quote-header-right {
    width: 33%;
    text-align: right;
}

.quote-document-type,
.quote-document-ref,
.quote-brand-detail,
.quote-to-label,
.quote-project-kicker,
.quote-total-label,
.quote-footer-label,
.quote-meta-line span,
.quote-payment-cell h3,
.quote-compact-table h3 {
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.quote-document-type {
    display: block;
    color: #141b25;
    font-size: 12px;
    font-weight: 800;
}

.quote-document-ref {
    display: block;
    margin-top: 4px;
    color: #777f8b;
    font-size: 9px;
    font-weight: 600;
}

.quote-brand-name {
    display: block;
    color: #141b25;
    font-size: 13px;
    line-height: 1.25;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.quote-brand-logo {
    display: inline-block;
    width: 118px;
    height: 31px;
    margin: 0 0 8px auto;
    object-fit: contain;
    object-position: right center;
}

.quote-brand-detail {
    display: block;
    margin-top: 3px;
    color: #646b76;
    font-size: 8.5px;
    line-height: 1.35;
}

.quote-header-meta td {
    padding-top: 28px;
}

.quote-meta-line {
    margin-bottom: 10px;
}

.quote-meta-line span,
.quote-to-label,
.quote-project-kicker,
.quote-total-label,
.quote-footer-label {
    display: block;
    color: #7a818c;
    font-size: 8.5px;
    font-weight: 800;
}

.quote-meta-line strong {
    display: block;
    margin-top: 4px;
    color: #20242c;
    font-size: 11px;
    font-weight: 700;
}

.quote-to-cell strong {
    display: block;
    margin-top: 8px;
    color: #141b25;
    font-size: 16px;
    line-height: 1.14;
    font-weight: 800;
}

.quote-to-role,
.quote-to-detail {
    display: block;
    color: #5f6672;
    font-size: 10px;
    line-height: 1.35;
}

.quote-to-role {
    margin-top: 4px;
    font-weight: 700;
}

.quote-to-detail {
    margin-top: 2px;
}

.quote-total-cell {
    padding-top: 24px;
}

.quote-total-box {
    display: inline-block;
    min-width: 142px;
    padding: 12px 18px;
    background: #c89b2e;
    color: #ffffff;
    font-size: 21px;
    line-height: 1;
    font-weight: 800;
    text-align: center;
}

.quote-total-label {
    margin-top: 7px;
    color: #9a7320;
}

.quote-total-converted {
    display: block;
    margin-top: 7px;
    color: #2a2114;
    font-size: 11px;
    font-weight: 800;
}

.quote-header-secondary td {
    padding-top: 18px;
}

.quote-project-cell {
    padding-bottom: 12px;
    border-bottom: 1px solid #e8e8e8;
}

.quote-project-title {
    display: block;
    margin-top: 7px;
    color: #141b25;
    font-size: 15px;
    line-height: 1.2;
    font-weight: 800;
}

.quote-project-copy {
    max-width: 620px;
    margin: 6px 0 0;
    color: #606873;
    font-size: 10px;
    line-height: 1.45;
}

.quote-items-table {
    margin-top: 10px;
    table-layout: fixed;
}

.quote-items-table thead th {
    padding: 8px 8px;
    border-bottom: 1px solid #e3e5ea;
    color: #777f8b;
    font-size: 8.5px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-align: left;
    text-transform: uppercase;
}

.quote-items-table th:nth-child(1) {
    width: 9%;
}

.quote-items-table th:nth-child(2) {
    width: 52%;
}

.quote-items-table th:nth-child(3) {
    width: 17%;
}

.quote-items-table th:nth-child(4) {
    width: 22%;
    text-align: right;
}

.quote-items-table tbody td {
    padding: 9px 8px;
    border-bottom: 1px solid #edf0f4;
    color: #343b48;
    font-size: 10px;
    vertical-align: top;
}

.quote-col-qty {
    width: 9%;
    color: #7b828e;
}

.quote-col-description {
    width: 52%;
}

.quote-col-description strong {
    display: block;
    color: #141b25;
    font-size: 10.5px;
    line-height: 1.3;
}

.quote-col-description span {
    display: block;
    margin-top: 2px;
    color: #7b828e;
    font-size: 9px;
    line-height: 1.3;
}

.quote-col-time {
    width: 17%;
    color: #5d6470;
}

.quote-col-amount {
    width: 22%;
    color: #141b25;
    font-weight: 800;
    text-align: right;
}

.quote-summary-table {
    margin-top: 14px;
}

.quote-payment-cell {
    width: 58%;
    padding-right: 24px;
}

.quote-totals-cell {
    width: 42%;
}

.quote-payment-cell h3,
.quote-compact-table h3 {
    margin: 0 0 6px;
    color: #141b25;
    font-size: 8.5px;
    font-weight: 800;
}

.quote-payment-cell p {
    margin: 0;
    color: #5f6672;
    font-size: 9.5px;
    line-height: 1.45;
}

.quote-payment-meta {
    margin-top: 10px;
}

.quote-payment-meta td {
    width: 50%;
    padding-right: 14px;
}

.quote-payment-meta span {
    display: block;
    color: #7b828e;
    font-size: 8px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.quote-payment-meta strong {
    display: block;
    margin-top: 4px;
    color: #20242c;
    font-size: 9px;
    font-weight: 700;
    line-height: 1.35;
}

.quote-totals-table {
    margin-left: auto;
}

.quote-totals-table td {
    padding: 4px 0;
    color: #5f6672;
    font-size: 10px;
}

.quote-totals-table td:last-child {
    color: #141b25;
    font-weight: 800;
    text-align: right;
}

.quote-totals-table tr.quote-total-final td {
    padding-top: 8px;
    border-top: 1px solid #e2e2e2;
    color: #141b25;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

.quote-totals-table tr.quote-total-final td:last-child {
    color: #c89b2e;
    font-size: 16px;
    letter-spacing: 0;
}

.quote-compact-table {
    margin-top: 13px;
    border-top: 1px solid #e4e0d5;
}

.quote-compact-table td {
    padding-top: 11px;
    padding-bottom: 2px;
}

.quote-compact-cell {
    width: 50%;
    padding-right: 20px;
}

.quote-compact-cell-last {
    padding-right: 0;
    padding-left: 20px;
}

.quote-compact-cell-full {
    padding-right: 0;
}

.quote-clean-list {
    margin: 0;
    padding-left: 13px;
}

.quote-clean-list li {
    margin-bottom: 3px;
    color: #5f6672;
    font-size: 9.5px;
    line-height: 1.35;
}

.quote-footer-table {
    margin-top: 12px;
    border-top: 1px solid #e1e4e8;
}

.quote-footer-table td {
    width: 33.333%;
    padding-top: 9px;
    padding-right: 10px;
}

.quote-footer-label {
    color: #7b828e;
}

.quote-footer-table strong {
    display: block;
    margin-top: 3px;
    color: #20242c;
    font-size: 9px;
    font-weight: 700;
    line-height: 1.3;
}

@media (max-width: 560px) {
    .quote-header-band,
    .quote-body {
        padding-left: 18px;
        padding-right: 18px;
    }

    .quote-header-table,
    .quote-header-table tbody,
    .quote-header-table tr,
    .quote-header-table td,
    .quote-summary-table,
    .quote-summary-table tbody,
    .quote-summary-table tr,
    .quote-summary-table td,
    .quote-compact-table,
    .quote-compact-table tbody,
    .quote-compact-table tr,
    .quote-compact-table td,
    .quote-footer-table,
    .quote-footer-table tbody,
    .quote-footer-table tr,
    .quote-footer-table td {
        display: block;
        width: 100%;
        padding-left: 0;
        padding-right: 0;
        text-align: left;
    }

    .quote-header-right {
        margin-top: 16px;
        text-align: left;
    }

    .quote-header-center {
        padding: 14px 0 0;
    }

    .quote-total-box {
        min-width: 0;
    }

    .quote-payment-cell {
        margin-bottom: 14px;
    }

    .quote-compact-cell,
    .quote-compact-cell-last,
    .quote-compact-cell-full {
        width: 100%;
        padding-left: 0;
        padding-right: 0;
    }
}
