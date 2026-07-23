@php
    $settings = $proposal->settings;
    $templatePalette = $proposal->template?->palette ?? [];
    $primary = $settings?->primary_color ?? ($templatePalette['primary'] ?? '#111111');
    $secondary = $settings?->secondary_color ?? ($templatePalette['secondary'] ?? '#f3f4f0');
    $accent = $settings?->accent_color ?? ($templatePalette['accent'] ?? '#e8b51f');
    $fontFamily = $settings?->font_family ?? ($proposal->template?->settings['font_family'] ?? 'Aptos');
    $themeKey = $proposal->theme_key ?: ($proposal->template?->theme_key ?? 'gold');
    $isDarkTheme = in_array($themeKey, ['green', 'dark'], true);
    $paper = $isDarkTheme ? '#f7f8f4' : '#ffffff';
    $ink = $isDarkTheme ? '#17231f' : '#171717';
    $muted = $isDarkTheme ? '#58645f' : '#5f6368';
    $soft = $isDarkTheme ? '#eef3e8' : '#f5f5f2';
@endphp

.proposal-document {
    width: 210mm;
    max-width: 100%;
    margin: 0 auto;
    color: {{ $ink }};
    font-family: "{{ $fontFamily }}", "Segoe UI", Arial, sans-serif;
    font-size: 11px;
    line-height: 1.45;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

.proposal-document *,
.proposal-document *::before,
.proposal-document *::after {
    box-sizing: border-box;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

.proposal-page {
    position: relative;
    width: 210mm;
    min-height: 297mm;
    height: 297mm;
    margin: 0 auto 24px;
    padding: 34px 38px 54px;
    overflow: hidden;
    background: {{ $paper }};
    color: {{ $ink }};
    border: 1px solid rgba(20, 20, 20, 0.08);
    box-shadow: 0 18px 48px rgba(16, 24, 40, 0.11);
    break-after: page;
    page-break-after: always;
    break-inside: avoid;
    page-break-inside: avoid;
}

.proposal-page:last-child {
    break-after: auto;
    page-break-after: auto;
}

.proposal-page--cover {
    position: relative;
    display: block;
    min-height: 297mm;
    height: 297mm;
    padding: 0;
    background: {{ $isDarkTheme ? $primary : '#ffffff' }};
    color: {{ $isDarkTheme ? '#ffffff' : '#111111' }};
}

.proposal-cover-layout {
    display: block;
    position: relative;
    width: 100%;
    height: 297mm;
}

.proposal-cover-main,
.proposal-cover-media {
    display: block;
}

.proposal-cover-main {
    position: relative;
    width: 100%;
    min-height: 297mm;
    height: 297mm;
    padding: 44px 42px;
    background: {{ $isDarkTheme ? $primary : $accent }};
}

.proposal-cover-media {
    position: absolute;
    top: 44px;
    right: 34px;
    width: 176px;
    padding: 0;
    background: transparent;
    z-index: 2;
}

.proposal-brand-lockup {
    display: table;
    width: 100%;
}

.proposal-logo-mark,
.proposal-brand-copy {
    display: table-cell;
    vertical-align: middle;
}

.proposal-logo {
    padding: 4px 5px;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.94);
}

.proposal-logo-mark {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: {{ $accent }};
    color: {{ $isDarkTheme ? $primary : '#111111' }};
    font-size: 12px;
    font-weight: 800;
    line-height: 36px;
    text-align: center;
    text-transform: uppercase;
}

.proposal-brand-copy {
    padding-left: 10px;
}

.proposal-brand-copy strong,
.proposal-brand-copy span,
.proposal-cover-meta-item span,
.proposal-page-kicker,
.proposal-section-eyebrow,
.proposal-footer span,
.proposal-stat span,
.proposal-table th,
.proposal-toc-number,
.proposal-signature-label {
    letter-spacing: 0.09em;
    text-transform: uppercase;
}

.proposal-brand-copy strong {
    display: block;
    font-size: 12px;
    line-height: 1.25;
}

.proposal-brand-copy span {
    display: block;
    margin-top: 3px;
    color: {{ $isDarkTheme ? 'rgba(255,255,255,0.72)' : 'rgba(0,0,0,0.55)' }};
    font-size: 8px;
    font-weight: 800;
}

.proposal-cover-title {
    position: absolute;
    left: 42px;
    right: 245px;
    bottom: 210px;
    z-index: 3;
}

.proposal-cover-year {
    display: block;
    margin-top: 46px;
    color: {{ $isDarkTheme ? $accent : '#111111' }};
    font-size: 24px;
    font-weight: 900;
}

.proposal-cover-title h1 {
    margin: 0;
    max-width: 400px;
    font-size: 38px;
    line-height: 1.04;
    font-weight: 900;
    text-transform: uppercase;
    word-break: normal;
}

.proposal-cover-title h1 span {
    display: block;
    color: {{ $isDarkTheme ? $accent : '#ffffff' }};
    font-weight: 500;
}

.proposal-cover-meta {
    position: absolute;
    left: 42px;
    bottom: 88px;
    display: table;
    width: 460px;
    table-layout: fixed;
    color: {{ $isDarkTheme ? 'rgba(255,255,255,0.86)' : 'rgba(0,0,0,0.76)' }};
    font-size: 9px;
}

.proposal-cover-meta-item {
    display: table-cell;
    padding-right: 12px;
    vertical-align: top;
}

.proposal-cover-meta-item span {
    display: block;
    font-size: 7.5px;
    font-weight: 800;
}

.proposal-cover-meta-item strong,
.proposal-cover-meta-item small {
    display: block;
    max-width: 150px;
    overflow-wrap: break-word;
    word-wrap: break-word;
}

.proposal-cover-meta-item strong {
    margin-top: 5px;
    font-size: 10px;
    line-height: 1.35;
}

.proposal-cover-meta-item small {
    margin-top: 3px;
    color: {{ $isDarkTheme ? 'rgba(255,255,255,0.62)' : 'rgba(0,0,0,0.54)' }};
    font-size: 8px;
    line-height: 1.35;
}

.proposal-cover-image {
    width: 100%;
    height: 380px;
    overflow: hidden;
    background: #141414;
}

.proposal-image {
    max-width: 100%;
    height: auto;
}

.proposal-logo {
    object-fit: contain;
}

.proposal-cover-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: {{ $isDarkTheme ? 'grayscale(1) contrast(1.08)' : 'none' }};
}

.proposal-cover-image--placeholder {
    background:
        linear-gradient(90deg, rgba(255,255,255,0.05) 0 12%, transparent 12% 100%),
        linear-gradient(135deg, rgba(255,255,255,0.18), transparent 44%),
        repeating-linear-gradient(90deg, #111 0 15px, #222 15px 18px, #0d0d0d 18px 31px);
}

.proposal-cover-contact {
    position: absolute;
    left: 42px;
    bottom: 30px;
    display: table;
    width: 460px;
    color: {{ $isDarkTheme ? 'rgba(255,255,255,0.78)' : 'rgba(0,0,0,0.64)' }};
    font-size: 8px;
}

.proposal-cover-contact span {
    display: table-cell;
    width: 33.333%;
}

.proposal-page-header,
.proposal-footer {
    display: table;
    width: 100%;
}

.proposal-page-header {
    margin-bottom: 22px;
    border-bottom: 1px solid rgba(20, 20, 20, 0.1);
}

.proposal-page-kicker,
.proposal-page-number {
    display: table-cell;
    padding-bottom: 9px;
    color: {{ $muted }};
    font-size: 8px;
    font-weight: 900;
    vertical-align: bottom;
}

.proposal-page-number {
    text-align: right;
}

.proposal-section-eyebrow {
    display: block;
    margin-bottom: 7px;
    color: {{ $accent }};
    font-size: 9px;
    font-weight: 900;
}

.proposal-section-title {
    margin: 0 0 12px;
    max-width: 690px;
    color: {{ $ink }};
    font-size: 30px;
    line-height: 1.06;
    font-weight: 900;
}

.proposal-section-body {
    max-width: 720px;
    margin: 0;
    color: {{ $muted }};
    font-size: 12px;
    line-height: 1.5;
    overflow-wrap: break-word;
    word-wrap: break-word;
}

.proposal-section-grid {
    display: block;
    width: 100%;
}

.proposal-stat-grid,
.proposal-team-grid,
.proposal-split-grid {
    display: table;
    width: 100%;
    table-layout: fixed;
    border-collapse: collapse;
}

.proposal-stat,
.proposal-team-card,
.proposal-split-card {
    display: table-cell;
    vertical-align: top;
}

.proposal-section-main {
    display: block;
    width: 100%;
    padding-right: 0;
}

.proposal-section-aside {
    display: block;
    width: 100%;
    margin-top: 14px;
}

.proposal-aside-panel,
.proposal-stat,
.proposal-team-card,
.proposal-split-card,
.proposal-timeline-item {
    border: 1px solid rgba(20, 20, 20, 0.1);
    background: {{ $soft }};
}

.proposal-aside-panel {
    padding: 12px 16px;
    border-left: 4px solid {{ $accent }};
}

.proposal-aside-panel strong {
    display: inline-block;
    width: 44px;
    color: {{ $ink }};
    font-size: 20px;
    line-height: 1;
    vertical-align: top;
}

.proposal-aside-panel p {
    display: inline-block;
    width: 500px;
    margin: 2px 0 0;
    color: {{ $muted }};
    font-size: 10px;
    line-height: 1.4;
    vertical-align: top;
}

.proposal-stat-grid {
    margin-top: 18px;
}

.proposal-stat {
    width: 33.333%;
    padding: 11px 12px;
}

.proposal-stat strong {
    display: block;
    margin-top: 3px;
    color: {{ $ink }};
    font-size: 19px;
    line-height: 1;
}

.proposal-stat span {
    color: {{ $muted }};
    font-size: 8px;
    font-weight: 900;
}

.proposal-toc-columns {
    display: table;
    width: 100%;
    margin-top: 14px;
    table-layout: fixed;
    border-collapse: collapse;
}

.proposal-toc-column {
    display: table-cell;
    width: 50%;
    padding-right: 10px;
    vertical-align: top;
}

.proposal-toc-column--right {
    padding-right: 0;
    padding-left: 10px;
}

.proposal-toc-list {
    margin: 0;
    padding: 0;
    list-style: none;
}

.proposal-toc-list li {
    display: table;
    width: 100%;
    padding: 7px 0;
    border-bottom: 1px solid rgba(20, 20, 20, 0.08);
}

.proposal-toc-number,
.proposal-toc-title {
    display: table-cell;
    vertical-align: middle;
}

.proposal-toc-number {
    width: 44px;
    color: {{ $accent }};
    font-size: 9px;
    font-weight: 900;
}

.proposal-toc-title {
    color: {{ $ink }};
    font-size: 12px;
    font-weight: 800;
}

.proposal-table {
    width: 100%;
    margin-top: 14px;
    border-collapse: collapse;
    table-layout: fixed;
    border-top: 3px solid {{ $accent }};
    break-inside: avoid;
    page-break-inside: avoid;
}

.proposal-table th {
    padding: 8px 8px;
    border-bottom: 1px solid rgba(20, 20, 20, 0.12);
    background: {{ $soft }};
    color: {{ $ink }};
    font-size: 8px;
    font-weight: 900;
    text-align: left;
}

.proposal-table td {
    padding: 9px 8px;
    border-bottom: 1px solid rgba(20, 20, 20, 0.08);
    color: {{ $muted }};
    font-size: 10px;
    vertical-align: top;
    break-inside: avoid;
    page-break-inside: avoid;
}

.proposal-table tr,
.proposal-table th {
    break-inside: avoid;
    page-break-inside: avoid;
}

.proposal-table td strong {
    display: block;
    color: {{ $ink }};
    font-size: 10.5px;
    line-height: 1.25;
}

.proposal-service-description {
    display: block;
    margin-top: 3px;
    color: {{ $muted }};
    font-size: 9px;
    line-height: 1.32;
}

.proposal-col-package {
    width: 15%;
}

.proposal-col-service {
    width: 52%;
}

.proposal-col-qty {
    width: 10%;
    text-align: center;
}

.proposal-col-total {
    width: 23%;
}

.proposal-table .amount {
    color: {{ $ink }};
    font-weight: 900;
    text-align: right;
    white-space: nowrap;
}

.proposal-total-table {
    width: 310px;
    margin: 14px 0 0 auto;
    border-collapse: collapse;
    background: {{ $soft }};
    break-inside: avoid;
    page-break-inside: avoid;
}

.proposal-total-table td {
    padding: 7px 11px;
    border-bottom: 1px solid rgba(20, 20, 20, 0.07);
    color: {{ $muted }};
}

.proposal-total-table td:last-child {
    color: {{ $ink }};
    font-weight: 900;
    text-align: right;
}

.proposal-total-table tr:last-child td {
    padding-top: 9px;
    padding-bottom: 9px;
    border-top: 2px solid {{ $ink }};
    border-bottom: 0;
    color: {{ $accent }};
    font-size: 14px;
    background: {{ $isDarkTheme ? $primary : '#111111' }};
}

.proposal-timeline {
    margin-top: 14px;
}

.proposal-timeline-item {
    position: relative;
    margin-bottom: 8px;
    padding: 11px 14px 11px 48px;
    break-inside: avoid;
    page-break-inside: avoid;
}

.proposal-timeline-index {
    position: absolute;
    left: 14px;
    top: 12px;
    width: 24px;
    height: 24px;
    background: {{ $accent }};
    color: {{ $isDarkTheme ? $primary : '#111111' }};
    font-weight: 900;
    line-height: 24px;
    text-align: center;
}

.proposal-timeline-item h3 {
    margin: 0 0 3px;
    color: {{ $ink }};
    font-size: 13px;
}

.proposal-timeline-item p {
    margin: 0;
    color: {{ $muted }};
    font-size: 10px;
    line-height: 1.35;
}

.proposal-timeline-meta {
    margin-top: 5px;
    color: {{ $accent }};
    font-size: 8px;
    font-weight: 900;
    text-transform: uppercase;
}

.proposal-team-grid,
.proposal-split-grid {
    margin-top: 14px;
}

.proposal-team-card,
.proposal-split-card {
    width: 33.333%;
    padding: 12px 11px;
}

.proposal-split-card {
    width: 50%;
}

.proposal-team-avatar {
    width: 42px;
    height: 42px;
    margin-bottom: 10px;
    border-radius: 50%;
    background: {{ $primary }};
    color: #ffffff;
    font-size: 14px;
    font-weight: 900;
    line-height: 42px;
    text-align: center;
}

.proposal-team-card h3,
.proposal-split-card h3 {
    margin: 0 0 4px;
    color: {{ $ink }};
    font-size: 12px;
}

.proposal-team-card span,
.proposal-split-card span {
    display: block;
    color: {{ $accent }};
    font-size: 8.5px;
    font-weight: 900;
}

.proposal-team-card p,
.proposal-split-card p {
    margin: 7px 0 0;
    color: {{ $muted }};
    font-size: 9px;
    line-height: 1.35;
}

.proposal-signature-grid {
    display: table;
    width: 100%;
    margin-top: 28px;
    table-layout: fixed;
    border-collapse: collapse;
}

.proposal-signature {
    display: table-cell;
    width: 33.333%;
    padding-top: 32px;
    padding-right: 12px;
    border-top: 1px solid {{ $ink }};
}

.proposal-signature-label {
    display: block;
    color: {{ $muted }};
    font-size: 8px;
    font-weight: 900;
}

.proposal-signature strong {
    display: block;
    margin-top: 7px;
    color: {{ $ink }};
}

.proposal-footer {
    position: absolute;
    left: 38px;
    bottom: 22px;
    display: table;
    width: 490px;
    table-layout: fixed;
    border-top: 1px solid rgba(20, 20, 20, 0.1);
}

.proposal-footer span {
    display: block;
    width: 100%;
    padding-top: 8px;
    color: {{ $muted }};
    font-size: 7.5px;
    line-height: 1.35;
    word-wrap: break-word;
    overflow-wrap: break-word;
    white-space: normal;
}

.proposal-watermark {
    position: absolute;
    right: -20px;
    bottom: 130px;
    color: rgba(20, 20, 20, 0.035);
    font-size: 74px;
    font-weight: 900;
    line-height: 1;
    text-transform: uppercase;
    transform: rotate(-90deg);
    transform-origin: right bottom;
}

@media print {
    .proposal-page {
        margin: 0;
        border: 0;
        box-shadow: none;
    }
}
