/* Final authenticated workspace system.
 * This layer intentionally sits after page-level styles so the admin feels
 * like one product rather than a collection of independently styled tools. */
body.is-admin {
    --bg: #f3f5f7;
    --surface: #ffffff;
    --surface-soft: #f7f8fa;
    --panel: #ffffff;
    --panel-soft: #f6f7f9;
    --text: #171a20;
    --muted: #727985;
    --muted-strong: #464d58;
    --line: #dfe3e8;
    --line-soft: #eceff2;
    --primary: #b98518;
    --primary-strong: #8e6410;
    --primary-soft: #fff5dc;
    --accent: #171a20;
    --accent-soft: #edf0f3;
    --shadow: 0 18px 46px rgba(20, 27, 38, .09);
    --shadow-soft: 0 8px 24px rgba(20, 27, 38, .06);
    --radius: 12px;
    background: var(--bg);
    color: var(--text);
}

body.is-admin .admin-workspace--with-sidebar {
    background: var(--bg);
}

body.is-admin .admin-workspace--with-sidebar .admin-main,
body.is-admin:not(.is-dashboard-overview) .admin-workspace--with-sidebar .admin-main {
    gap: 22px;
    min-height: 100vh;
    padding: 0 32px 48px;
    background:
        radial-gradient(circle at 92% 2%, rgba(185, 133, 24, .075), transparent 23rem),
        var(--bg);
}

/* Dark navigation rail gives the product a stable visual anchor. */
body.is-admin .admin-sidebar {
    border: 0;
    border-right: 1px solid #252a33;
    background: #15181e;
    box-shadow: 14px 0 34px rgba(14, 18, 25, .08);
}

body.is-admin .admin-sidebar-inner {
    gap: 12px;
    padding: 16px 13px;
}

body.is-admin .admin-sidebar-brand {
    min-height: 54px;
    padding: 8px;
    border: 1px solid rgba(255, 255, 255, .07);
    background: rgba(255, 255, 255, .035);
}

body.is-admin .admin-sidebar-brand:hover {
    border-color: rgba(222, 178, 78, .24);
    background: rgba(222, 178, 78, .08);
}

body.is-admin .admin-sidebar .admin-brand-mark {
    border-color: rgba(222, 178, 78, .28);
    background: #c7962b;
    box-shadow: 0 8px 20px rgba(0, 0, 0, .2);
}

body.is-admin .admin-sidebar .admin-brand-copy strong {
    color: #f7f8fa;
}

body.is-admin .admin-sidebar .admin-brand-copy span,
body.is-admin .admin-nav-label {
    color: #858d9a;
}

body.is-admin .admin-sidebar .admin-icon-button {
    border-color: rgba(255, 255, 255, .1);
    background: rgba(255, 255, 255, .04);
    color: #aeb5c0;
}

body.is-admin .admin-sidebar .admin-icon-button:hover {
    border-color: rgba(222, 178, 78, .28);
    background: rgba(222, 178, 78, .1);
    color: #e2b34d;
}

body.is-admin .admin-nav {
    gap: 3px;
}

body.is-admin .admin-nav-label {
    margin: 15px 10px 6px;
    font-size: 9px;
    letter-spacing: .15em;
}

body.is-admin .admin-nav-link {
    min-height: 46px;
    border: 1px solid transparent;
    border-radius: 9px;
    color: #bdc3cc;
}

body.is-admin .admin-nav-link strong {
    color: #e6e9ed;
    font-weight: 680;
}

body.is-admin .admin-nav-link span:not(.admin-nav-icon) {
    color: #7f8793;
}

body.is-admin .admin-nav-link:hover {
    border-color: rgba(255, 255, 255, .08);
    background: rgba(255, 255, 255, .055);
    color: #ffffff;
}

body.is-admin .admin-nav-link.active {
    border-color: rgba(222, 178, 78, .2);
    background: linear-gradient(90deg, rgba(205, 154, 45, .2), rgba(205, 154, 45, .07));
    color: #e7b84f;
}

body.is-admin .admin-nav-link.active strong,
body.is-admin .admin-nav-link:hover strong {
    color: #ffffff;
}

body.is-admin .admin-nav-link.active span:not(.admin-nav-icon) {
    color: #c9a95f;
}

body.is-admin .admin-nav-link.active::before {
    left: -13px;
    top: 8px;
    bottom: 8px;
    width: 3px;
    background: #d8aa42;
}

body.is-admin .admin-sidebar-meta {
    border-color: rgba(255, 255, 255, .08);
    background: rgba(255, 255, 255, .035);
}

body.is-admin .admin-sidebar-meta span { color: #858d9a; }
body.is-admin .admin-sidebar-meta strong { color: #eceef1; }

/* Compact application bar. */
body.is-admin .admin-pagebar,
body.is-admin:not(.is-dashboard-overview) .admin-pagebar {
    min-height: 70px;
    margin: 0 -32px 2px;
    padding: 12px 32px;
    border: 0;
    border-bottom: 1px solid rgba(212, 217, 224, .9);
    background: rgba(248, 249, 251, .92);
    box-shadow: 0 5px 18px rgba(30, 36, 46, .035);
    backdrop-filter: blur(18px);
}

body.is-admin .admin-pagebar h1,
body.is-admin:not(.is-dashboard-overview) .admin-pagebar h1 {
    color: #1b1f26;
    font-family: var(--font-display);
    font-size: 20px !important;
    font-weight: 720 !important;
    letter-spacing: -.025em;
}

body.is-admin .admin-pagebar .eyebrow,
body.is-admin:not(.is-dashboard-overview) .admin-pagebar .eyebrow {
    color: #8a919c;
    font-size: 9px;
    letter-spacing: .12em;
}

body.is-admin .admin-profile-menu summary,
body.is-admin .admin-search-trigger,
body.is-admin .admin-date-pill {
    border-color: #dfe3e8;
    border-radius: 9px;
    background: #ffffff;
    box-shadow: 0 3px 10px rgba(21, 27, 37, .035);
}

/* Major screens use a compact command header instead of a blank white band. */
body.is-admin .admin-main > .hero-banner,
body.is-admin:not(.is-dashboard-overview) .admin-main > .hero-banner,
body.is-admin .admin-main > .page-header,
body.is-admin:not(.is-dashboard-overview) .admin-main > .page-header {
    position: relative;
    isolation: isolate;
    display: grid;
    grid-template-columns: minmax(0, 1.35fr) minmax(300px, .72fr);
    min-height: 0;
    align-items: center;
    gap: 28px;
    overflow: hidden;
    padding: 26px 28px;
    border: 1px solid #282e38;
    border-radius: 16px;
    background:
        radial-gradient(circle at 87% 15%, rgba(211, 164, 61, .2), transparent 18rem),
        linear-gradient(130deg, #171a20 0%, #20252e 64%, #292d34 100%);
    box-shadow: 0 18px 42px rgba(17, 22, 31, .16);
}

body.is-admin .admin-main > .hero-banner::before,
body.is-admin .admin-main > .page-header::before {
    position: absolute;
    top: -74px;
    right: 19%;
    z-index: -1;
    width: 210px;
    height: 210px;
    border: 1px solid rgba(223, 181, 88, .13);
    border-radius: 50%;
    content: "";
}

body.is-admin .admin-main > .hero-banner h1,
body.is-admin:not(.is-dashboard-overview) .admin-main > .hero-banner h1,
body.is-admin:not(.is-dashboard-overview) .hero-banner h1,
body.is-admin .admin-main > .page-header h1,
body.is-admin:not(.is-dashboard-overview) .admin-main > .page-header h1,
body.is-admin:not(.is-dashboard-overview) .page-header h1 {
    max-width: 760px;
    color: #ffffff;
    font-family: var(--font-display);
    font-size: clamp(25px, 2.5vw, 34px) !important;
    font-weight: 720 !important;
    letter-spacing: -.045em;
    line-height: 1.08;
}

body.is-admin .admin-main > .hero-banner .eyebrow,
body.is-admin .admin-main > .page-header .eyebrow {
    color: #e1b653;
    font-size: 9px;
    letter-spacing: .16em;
}

body.is-admin .admin-main > .hero-banner p,
body.is-admin:not(.is-dashboard-overview) .admin-main > .hero-banner > div:first-child p,
body.is-admin:not(.is-dashboard-overview) .hero-banner p,
body.is-admin .admin-main > .page-header p,
body.is-admin:not(.is-dashboard-overview) .admin-main > .page-header > div:first-child p,
body.is-admin:not(.is-dashboard-overview) .page-header p {
    display: block !important;
    max-width: 680px;
    color: #afb6c1;
    font-size: 12px;
    line-height: 1.65;
}

body.is-admin .hero-actions {
    margin-top: 16px;
}

body.is-admin .hero-banner .ghost-button,
body.is-admin .page-header .ghost-button,
body.is-admin .pm-hero .ghost-button {
    border-color: rgba(255, 255, 255, .16);
    background: rgba(255, 255, 255, .07);
    color: #f2f4f6;
}

body.is-admin .hero-banner .ghost-button:hover,
body.is-admin .page-header .ghost-button:hover,
body.is-admin .pm-hero .ghost-button:hover {
    border-color: rgba(225, 182, 83, .45);
    background: rgba(225, 182, 83, .12);
    color: #f0c76b;
}

body.is-admin .hero-banner .button,
body.is-admin .page-header .button,
body.is-admin .pm-hero .button {
    border-color: #d6a83f;
    background: #d6a83f;
    color: #171a20;
    box-shadow: none;
}

body.is-admin .hero-callout,
body.is-admin:not(.is-dashboard-overview) .hero-callout {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}

body.is-admin .hero-callout .callout-card,
body.is-admin:not(.is-dashboard-overview) .hero-callout .callout-card,
body.is-admin:not(.is-dashboard-overview) .admin-main > .hero-banner .callout-card {
    min-height: 96px;
    padding: 14px 15px;
    border: 1px solid rgba(255, 255, 255, .12);
    border-radius: 11px;
    background: rgba(255, 255, 255, .07);
    box-shadow: none;
    backdrop-filter: blur(5px);
}

body.is-admin .hero-callout .metric-label {
    color: #c9cfd7;
    font-size: 8px;
    letter-spacing: .12em;
}

body.is-admin .hero-callout .callout-card strong {
    margin-top: 8px;
    color: #ffffff;
    font-size: 21px;
    font-weight: 680;
}

body.is-admin .hero-callout .callout-card p {
    margin-top: 6px;
    color: #aeb5bf;
    font-size: 10px;
    line-height: 1.45;
}

/* Content cards, metrics, and controls. */
body.is-admin .panel:not(.hero-banner):not(.page-header):not(.pm-hero),
body.is-admin:not(.is-dashboard-overview) .panel:not(.hero-banner):not(.page-header):not(.pm-hero),
body.is-admin:not(.is-dashboard-overview) .admin-main > .panel:not(.hero-banner):not(.page-header):not(.pm-hero),
body.is-admin:not(.is-dashboard-overview) .admin-main > .dashboard-grid > .panel,
body.is-admin:not(.is-dashboard-overview) .admin-main > .dashboard-grid > .tt-section {
    border: 1px solid #e1e5e9;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: var(--shadow-soft);
}

body.is-admin .panel-padded,
body.is-admin:not(.is-dashboard-overview) .panel-padded {
    padding: 22px;
}

body.is-admin .panel-head {
    margin-bottom: 18px;
}

body.is-admin .panel-head h2,
body.is-admin:not(.is-dashboard-overview) .panel-head h2,
body.is-admin .panel-title,
body.is-admin:not(.is-dashboard-overview) .panel-title {
    color: #20242b;
    font-family: var(--font-display);
    font-size: 18px;
    font-weight: 680;
    letter-spacing: -.03em;
}

body.is-admin .panel-head p,
body.is-admin:not(.is-dashboard-overview) .panel-head p {
    color: #7b828d;
    font-size: 11px;
}

body.is-admin .admin-pill {
    min-height: 28px;
    border-color: #ead9aa;
    border-radius: 7px;
    background: #fff8e8;
    color: #8b650e;
    font-size: 9px;
}

body.is-admin .button,
body.is-admin button.button,
body.is-admin:not(.is-dashboard-overview) .button {
    min-height: 38px;
    border: 1px solid #20242b;
    border-radius: 8px;
    background: #20242b;
    color: #ffffff;
    box-shadow: 0 7px 16px rgba(22, 27, 35, .12);
    font-size: 12px;
}

body.is-admin .button:hover,
body.is-admin button.button:hover,
body.is-admin:not(.is-dashboard-overview) .button:hover {
    border-color: #9d7113;
    background: #9d7113;
}

body.is-admin .ghost-button,
body.is-admin button.ghost-button,
body.is-admin:not(.is-dashboard-overview) .ghost-button {
    min-height: 38px;
    border-color: #dfe3e8;
    border-radius: 8px;
    background: #ffffff;
    color: #3e4651;
    font-size: 12px;
}

body.is-admin .ghost-button:hover,
body.is-admin button.ghost-button:hover,
body.is-admin:not(.is-dashboard-overview) .ghost-button:hover {
    border-color: #d9bd78;
    background: #fff9ea;
    color: #805c0e;
}

body.is-admin .callout-card,
body.is-admin .metric-card,
body.is-admin .mini-card,
body.is-admin .kpi-card,
body.is-admin .highlight-card,
body.is-admin .template-card {
    border-color: #e1e5e9;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 6px 18px rgba(20, 27, 38, .045);
}

body.is-admin .kpi-grid,
body.is-admin:not(.is-dashboard-overview) .kpi-grid {
    gap: 12px;
    border: 0;
}

body.is-admin .kpi-card,
body.is-admin:not(.is-dashboard-overview) .kpi-card,
body.is-admin:not(.is-dashboard-overview) .kpi-card:hover {
    min-height: 112px;
    padding: 17px;
    border: 1px solid #e1e5e9;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 6px 18px rgba(20, 27, 38, .045);
}

body.is-admin .kpi-card:first-child { padding-left: 17px; }
body.is-admin .kpi-value { color: #1d2128; font-size: 27px; font-weight: 720; }

body.is-admin .field input,
body.is-admin .field select,
body.is-admin .field textarea,
body.is-admin .field-full input,
body.is-admin .field-full select,
body.is-admin .field-full textarea,
body.is-admin:not(.is-dashboard-overview) .field input,
body.is-admin:not(.is-dashboard-overview) .field select,
body.is-admin:not(.is-dashboard-overview) .field textarea,
body.is-admin:not(.is-dashboard-overview) .field-full input,
body.is-admin:not(.is-dashboard-overview) .field-full select,
body.is-admin:not(.is-dashboard-overview) .field-full textarea {
    min-height: 42px;
    border: 1px solid #dfe3e8;
    border-radius: 8px;
    background: #f9fafb;
    color: #232831;
    box-shadow: none;
}

body.is-admin .field input:focus,
body.is-admin .field select:focus,
body.is-admin .field textarea:focus,
body.is-admin .field-full input:focus,
body.is-admin .field-full select:focus,
body.is-admin .field-full textarea:focus {
    border-color: #bd8a20;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(189, 138, 32, .12);
    outline: none;
}

body.is-admin .table-wrap,
body.is-admin:not(.is-dashboard-overview) .table-wrap {
    overflow: hidden;
    overflow-x: auto;
    border: 1px solid #e4e7eb;
    border-radius: 11px;
    background: #ffffff;
}

body.is-admin .quote-table th,
body.is-admin:not(.is-dashboard-overview) .quote-table th {
    padding: 12px 13px;
    border-bottom: 1px solid #e1e5e9;
    background: #f7f8fa;
    color: #747b86;
    font-size: 9px;
}

body.is-admin .quote-table td,
body.is-admin:not(.is-dashboard-overview) .quote-table td {
    padding: 13px;
    border-bottom-color: #eceff2;
    color: #525a66;
    font-size: 11px;
}

body.is-admin .quote-table tbody tr:hover,
body.is-admin:not(.is-dashboard-overview) .quote-table tbody tr:hover {
    background: #fbf8ef;
}

/* Project management joins the same visual language. */
body.is-admin .pm-subnav,
body.is-admin:not(.is-dashboard-overview) .pm-subnav {
    padding: 7px;
    border: 1px solid #e1e5e9;
    border-radius: 11px;
    background: #ffffff;
    box-shadow: 0 6px 18px rgba(20, 27, 38, .04);
}

body.is-admin .pm-subnav a,
body.is-admin .pm-subnav-more summary {
    min-height: 34px;
    border-radius: 7px;
}

body.is-admin .pm-subnav a.active,
body.is-admin .pm-subnav a:hover,
body.is-admin .pm-subnav-more summary:hover {
    background: #fff5dc;
    color: #855f0d;
}

body.is-admin .pm-hero,
body.is-admin:not(.is-dashboard-overview) .admin-main .pm-shell .pm-hero {
    position: relative;
    overflow: hidden;
    padding: 24px 26px;
    border: 1px solid #282e38;
    border-radius: 15px;
    background:
        radial-gradient(circle at 88% 10%, rgba(211, 164, 61, .18), transparent 15rem),
        linear-gradient(130deg, #171a20, #282d36);
    box-shadow: 0 16px 36px rgba(17, 22, 31, .14);
}

body.is-admin .pm-hero h2,
body.is-admin .pm-project-view .pm-hero h2,
body.is-admin:not(.is-dashboard-overview) .admin-main .pm-shell .pm-hero h2 {
    color: #ffffff !important;
    font-size: clamp(24px, 2.8vw, 32px) !important;
}

body.is-admin .pm-hero p,
body.is-admin .pm-project-view .pm-hero > div:first-child > p,
body.is-admin:not(.is-dashboard-overview) .admin-main .pm-shell .pm-hero > div:first-child > p,
body.is-admin .pm-hero .pm-project-meta {
    color: #aeb5bf;
}

body.is-admin .pm-hero > div:first-child > p,
body.is-admin:not(.is-dashboard-overview) .admin-main .pm-shell .pm-hero > div:first-child > p {
    display: block !important;
}

body.is-admin .pm-hero .eyebrow { color: #e1b653; }

body.is-admin .pm-panel,
body.is-admin .pm-column,
body.is-admin .pm-stat {
    border-color: #e1e5e9;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 6px 18px rgba(20, 27, 38, .045);
}

body.is-admin .pm-tabs a {
    border-radius: 8px;
}

body.is-admin .pm-tabs a.active,
body.is-admin .pm-tabs a.active:hover {
    border-color: #20242b;
    background: #20242b;
}

/* Dedicated feature cards inherit the system without losing their identity. */
body.is-admin .credential-project-card,
body.is-admin .credential-card,
body.is-admin .project-file-card,
body.is-admin .project-file-manager-row,
body.is-admin .proposal-builder-panel,
body.is-admin .proposal-template-card {
    border-color: #e1e5e9;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 6px 18px rgba(20, 27, 38, .045);
}

body.is-admin .credential-create,
body.is-admin .project-files-empty,
body.is-admin .pm-empty {
    border-color: #dfe3e8;
    border-radius: 11px;
    background: #f8f9fa;
}

body.is-admin .credential-card__marker,
body.is-admin .credential-project-card__mark,
body.is-admin .project-file-card__icon {
    background: #fff3d5;
    color: #8a620c;
}

body.is-admin .admin-alert-stack .alert {
    border-radius: 10px;
    box-shadow: 0 16px 34px rgba(18, 24, 33, .13);
}

@media (max-width: 1100px) {
    body.is-admin .admin-workspace--with-sidebar .admin-main,
    body.is-admin:not(.is-dashboard-overview) .admin-workspace--with-sidebar .admin-main {
        padding-inline: 20px;
    }

    body.is-admin .admin-pagebar,
    body.is-admin:not(.is-dashboard-overview) .admin-pagebar {
        margin-inline: -20px;
        padding-inline: 20px;
    }
}

@media (max-width: 820px) {
    body.is-admin .admin-main > .hero-banner,
    body.is-admin:not(.is-dashboard-overview) .admin-main > .hero-banner,
    body.is-admin .admin-main > .page-header,
    body.is-admin:not(.is-dashboard-overview) .admin-main > .page-header {
        grid-template-columns: 1fr;
        gap: 18px;
        padding: 22px;
    }

    body.is-admin .hero-callout,
    body.is-admin:not(.is-dashboard-overview) .hero-callout {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 560px) {
    body.is-admin .admin-workspace--with-sidebar .admin-main,
    body.is-admin:not(.is-dashboard-overview) .admin-workspace--with-sidebar .admin-main {
        padding: 0 14px 30px;
    }

    body.is-admin .admin-pagebar,
    body.is-admin:not(.is-dashboard-overview) .admin-pagebar {
        margin-inline: -14px;
        padding-inline: 14px;
    }

    body.is-admin .admin-pagebar-actions .admin-date-pill,
    body.is-admin .admin-search-trigger span {
        display: none;
    }

    body.is-admin .hero-callout,
    body.is-admin:not(.is-dashboard-overview) .hero-callout {
        grid-template-columns: 1fr;
    }

    body.is-admin .hero-callout .callout-card,
    body.is-admin:not(.is-dashboard-overview) .hero-callout .callout-card {
        min-height: 0;
    }

    body.is-admin .panel-padded,
    body.is-admin:not(.is-dashboard-overview) .panel-padded {
        padding: 17px;
    }
}
