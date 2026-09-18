@font-face { font-family: 'Urbanist'; font-style: normal; font-weight: 400; src: url('{{ \App\Support\DocumentTypography::urbanistFontUrl('Urbanist-Regular.ttf') }}') format('truetype'); }
@font-face { font-family: 'Urbanist'; font-style: normal; font-weight: 600; src: url('{{ \App\Support\DocumentTypography::urbanistFontUrl('Urbanist-SemiBold.ttf') }}') format('truetype'); }
@font-face { font-family: 'Urbanist'; font-style: normal; font-weight: 700; src: url('{{ \App\Support\DocumentTypography::urbanistFontUrl('Urbanist-Bold.ttf') }}') format('truetype'); }
.payment-document { position: relative; box-sizing: border-box; width: auto; min-height: 1070px; padding: 34px 38px 28px; background: #fff; color: #1e2431; font-family: 'Urbanist', sans-serif; font-size: 11px; line-height: 1.4; }
.payment-document * { box-sizing: border-box; }
.payment-document table { width: 100%; border-collapse: collapse; }
.payment-document .pd-kicker { color: #b98000; font-size: 10px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; }
.payment-document .pd-muted { color: #667085; }
.payment-document .pd-header { table-layout: fixed; }
.payment-document .pd-header td { width: 50%; vertical-align: top; }
.payment-document .pd-invoice-title { margin: 0 0 10px; font-size: 21px; font-weight: 600; letter-spacing: .02em; }
.payment-document .pd-number { color: #667085; font-size: 13px; }
.payment-document .pd-payment-label { margin-top: 7px; color: #b98000; font-size: 11px; font-weight: 600; text-transform: uppercase; }
.payment-document .pd-brand { text-align: right; overflow-wrap: break-word; }
.payment-document .pd-brand-logo { display: inline-block; width: 118px; height: 31px; margin-bottom: 3px; }
.payment-document .pd-brand-name { font-size: 16px; font-weight: 700; text-transform: uppercase; }
.payment-document .pd-brand-tagline { margin-top: 2px; color: #667085; font-size: 10px; letter-spacing: .07em; text-transform: uppercase; }
.payment-document .pd-brand-line { width: 190px; margin: 7px 0 7px auto; border-top: 1px solid #d19b19; }
.payment-document .pd-brand-contact { color: #667085; font-size: 9px; line-height: 1.7; text-transform: uppercase; }
.payment-document .pd-overview { margin-top: 26px; table-layout: fixed; }
.payment-document .pd-overview td { vertical-align: top; }
.payment-document .pd-overview .client { width: 46%; padding-right: 24px; }
.payment-document .pd-overview .dates { width: 25%; padding-right: 20px; }
.payment-document .pd-overview .amount { width: 29%; text-align: right; }
.payment-document .pd-label { margin-bottom: 10px; color: #667085; font-size: 9px; letter-spacing: .08em; text-transform: uppercase; }
.payment-document .pd-client-company { margin-bottom: 6px; font-size: 20px; font-weight: 600; text-transform: uppercase; }
.payment-document .pd-client-line { margin-top: 4px; color: #475467; }
.payment-document .pd-date-value { margin-bottom: 15px; font-size: 11px; font-weight: 600; }
.payment-document .pd-amount-box { padding: 14px 10px; background: #d2a128; color: #fff; font-size: 22px; font-weight: 600; text-align: center; }
.payment-document .pd-local-amount { margin-top: 15px; font-size: 15px; font-weight: 600; }
.payment-document .pd-progress-due { margin-top: 14px; color: #b98000; font-size: 10px; text-transform: uppercase; }
.payment-document .pd-project { margin-top: 22px; }
.payment-document .pd-project h1 { margin: 7px 0 10px; font-size: 23px; line-height: 1.15; }
.payment-document .pd-project p { margin: 0; color: #596274; font-size: 11px; line-height: 1.5; }
.payment-document .pd-line-items { margin-top: 17px; table-layout: fixed; }
.payment-document .pd-line-items th { padding: 12px 10px; border-top: 1px solid #d9dee7; border-bottom: 1px solid #d9dee7; color: #667085; font-size: 9px; font-weight: 400; letter-spacing: .06em; text-align: left; text-transform: uppercase; }
.payment-document .pd-line-items td { padding: 13px 10px; border-bottom: 1px solid #d9dee7; vertical-align: top; overflow-wrap: break-word; }
.payment-document .pd-line-items th:first-child, .payment-document .pd-line-items td:first-child { width: 9%; }
.payment-document .pd-line-items th:nth-child(3), .payment-document .pd-line-items td:nth-child(3) { width: 25%; }
.payment-document .pd-line-items th:last-child, .payment-document .pd-line-items td:last-child { width: 18%; text-align: right; }
.payment-document .pd-item-title { font-size: 13px; font-weight: 700; }
.payment-document .pd-item-note { margin-top: 5px; color: #667085; font-size: 10px; }
.payment-document .pd-lower { margin-top: 17px; table-layout: fixed; page-break-inside: avoid; }
.payment-document .pd-lower > tbody > tr > td { width: 50%; vertical-align: top; }
.payment-document .pd-lower > tbody > tr > td:first-child { padding-right: 34px; }
.payment-document .pd-section-title { margin-bottom: 15px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
.payment-document .pd-payment-intro { margin: 0 0 12px; color: #475467; }
.payment-document .pd-bank-table td { padding: 5px 0; }
.payment-document .pd-bank-table td:first-child { width: 40%; color: #667085; font-size: 9px; text-transform: uppercase; }
.payment-document .pd-bank-table td:last-child { font-weight: 600; }
.payment-document .pd-totals td { padding: 5px 0; }
.payment-document .pd-totals td:first-child { color: #667085; }
.payment-document .pd-totals td:last-child { text-align: right; }
.payment-document .pd-totals .total td { padding-top: 8px; border-top: 1px solid #cfd5df; font-size: 17px; color: #1e2431; }
.payment-document .pd-totals .total td:last-child { color: #b98000; font-size: 21px; }
.payment-document .pd-balance { margin-top: 10px; padding: 8px 13px; background: #f4f5f7; border-radius: 6px; page-break-inside: avoid; }
.payment-document .pd-balance table td { vertical-align: middle; }
.payment-document .pd-balance table td:last-child { color: #667085; text-align: right; }
.payment-document .pd-balance strong { display: block; margin-top: 3px; font-size: 12px; }
.payment-document .pd-footer { margin-top: 8px; padding-top: 6px; border-top: 1px solid #d9dee7; table-layout: fixed; }
.payment-document .pd-footer td { vertical-align: top; }
.payment-document .pd-footer td:nth-child(2) { text-align: center; }
.payment-document .pd-footer td:last-child { text-align: right; }
.payment-document .pd-footer span { display: block; margin-bottom: 5px; color: #667085; font-size: 8px; text-transform: uppercase; }
.payment-document .pd-paid-stamp { position: absolute; top: 282px; right: 54px; padding: 6px 14px; border: 2px solid #198754; color: #198754; font-size: 14px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; transform: rotate(-8deg); }
@media screen and (max-width: 820px) { .payment-document { min-height: 1020px; } }
