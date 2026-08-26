:root {
    --font-sans: "Manrope", "Aptos", "Segoe UI Variable Text", "Segoe UI", ui-sans-serif, system-ui, sans-serif;
    --font-display: "Sora", "Manrope", "Aptos Display", "Segoe UI Variable Display", ui-sans-serif, system-ui, sans-serif;
    --bg: #fffdf8;
    --surface: #ffffff;
    --surface-soft: #fffaf0;
    --panel: #ffffff;
    --panel-soft: #fff6dc;
    --text: #111111;
    --muted: #6f6658;
    --muted-strong: #3b3327;
    --line: rgba(184, 134, 11, 0.24);
    --line-soft: rgba(184, 134, 11, 0.12);
    --primary: #b8860b;
    --primary-strong: #8f6508;
    --primary-soft: #fff4d6;
    --accent: #111111;
    --accent-soft: #f7f1e6;
    --success: #168556;
    --warning: #b7791f;
    --danger: #c24155;
    --info: #111111;
    --traffic: #111111;
    --quote: #b8860b;
    --lead: #9a5b13;
    --pipeline: #2a2419;
    --shadow: 0 14px 34px rgba(15, 23, 42, 0.07);
    --shadow-soft: 0 8px 20px rgba(15, 23, 42, 0.05);
    --radius: 8px;
    --sidebar-width: 272px;
    --sidebar-collapsed-width: 76px;
}

html {
    background: var(--bg);
}

body {
    background: var(--bg);
    color: var(--text);
    font-family: var(--font-sans);
    font-size: 14px;
    font-feature-settings: "cv02", "cv03", "cv04", "cv11";
    line-height: 1.52;
}

body.is-auth {
    background:
        linear-gradient(180deg, rgba(184, 134, 11, 0.06), transparent 34%),
        var(--bg);
}

a,
button,
input,
select,
textarea,
summary {
    letter-spacing: 0;
}

summary {
    list-style: none;
}

summary::-webkit-details-marker {
    display: none;
}

.admin-shell {
    max-width: none;
    padding: 0;
}

.admin-shell--auth {
    display: grid;
    min-height: 100vh;
    max-width: 1120px;
    margin: 0 auto;
    padding: 32px;
    place-items: center;
}

.admin-workspace {
    display: grid;
    gap: 0;
}

.admin-workspace--with-sidebar {
    grid-template-columns: var(--sidebar-width) minmax(0, 1fr);
    min-height: 100vh;
    background: var(--bg);
}

.admin-main {
    min-width: 0;
    display: grid;
    gap: 20px;
}

.admin-workspace--with-sidebar .admin-main {
    align-content: start;
    min-height: 100vh;
    padding: 0 28px 36px;
    background: var(--bg);
}

.admin-sidebar {
    position: sticky;
    top: 0;
    z-index: 40;
    width: auto;
    height: 100vh;
    padding: 0;
    border: 0;
    border-right: 1px solid var(--line);
    border-radius: 0;
    background: rgba(255, 255, 255, 0.96);
    box-shadow: none;
    overflow: hidden;
}

.admin-sidebar-inner {
    display: flex;
    flex-direction: column;
    gap: 14px;
    height: 100%;
    padding: 14px 12px;
}

.admin-sidebar-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.admin-sidebar-brand,
.admin-brand {
    display: flex;
    min-width: 0;
    align-items: center;
    gap: 10px;
}

.admin-sidebar-brand {
    flex: 1;
    min-height: 48px;
    padding: 7px;
    border: 0;
    border-radius: var(--radius);
    background: transparent;
    box-shadow: none;
}

.admin-sidebar-brand:hover {
    background: var(--surface-soft);
}

.admin-brand-mark {
    display: grid;
    width: 34px;
    height: 34px;
    flex: 0 0 34px;
    place-items: center;
    border: 1px solid rgba(184, 134, 11, 0.2);
    border-radius: var(--radius);
    background: var(--primary);
    color: #ffffff;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.08em;
}

.admin-brand-mark img {
    display: block;
    width: 76%;
    height: 76%;
    object-fit: contain;
}

.admin-brand-copy {
    min-width: 0;
}

/*
 * The full studio name does not fit the sidebar column on one line, so it
 * wraps rather than truncating. Selector depth matches the base layer's
 * `.admin-sidebar .admin-brand-copy strong`, which sets nowrap + ellipsis.
 */
.admin-brand-copy strong,
.admin-sidebar .admin-brand-copy strong {
    display: block;
    overflow: visible;
    color: var(--text);
    font-size: 12.5px;
    font-weight: 800;
    letter-spacing: -0.01em;
    line-height: 1.18;
    overflow-wrap: break-word;
    text-overflow: clip;
    white-space: normal;
}

.admin-brand-copy span {
    display: block;
    margin-top: 2px;
    overflow: hidden;
    color: var(--muted);
    font-size: 11px;
    font-weight: 600;
    line-height: 1.2;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.admin-icon-button {
    display: inline-grid;
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    place-items: center;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--surface);
    color: var(--muted-strong);
    cursor: pointer;
    transition: background 0.16s ease, border-color 0.16s ease, color 0.16s ease;
}

.admin-icon-button:hover {
    border-color: rgba(184, 134, 11, 0.32);
    background: var(--primary-soft);
    color: var(--primary);
}

.admin-icon-button svg,
.admin-nav-icon svg {
    width: 18px;
    height: 18px;
    stroke: currentColor;
    stroke-width: 1.9;
    stroke-linecap: round;
    stroke-linejoin: round;
    fill: none;
}

.admin-nav {
    display: grid;
    gap: 4px;
    margin: 0;
    padding: 0;
}

.admin-nav-label {
    margin: 14px 10px 5px;
    color: var(--muted);
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.admin-nav-link {
    position: relative;
    display: grid;
    grid-template-columns: 34px minmax(0, 1fr);
    align-items: center;
    gap: 10px;
    min-height: 46px;
    padding: 6px 8px;
    border: 1px solid transparent;
    border-radius: var(--radius);
    background: transparent;
    color: var(--muted-strong);
    transition: background 0.16s ease, border-color 0.16s ease, color 0.16s ease;
}

.admin-nav-link:hover,
.admin-nav-link.active {
    border-color: rgba(184, 134, 11, 0.14);
    background: var(--primary-soft);
    color: var(--primary);
    transform: none;
    box-shadow: none;
}

.admin-nav-link.active::before {
    content: "";
    position: absolute;
    left: -12px;
    top: 10px;
    bottom: 10px;
    width: 3px;
    border-radius: 999px;
    background: var(--primary);
}

.admin-nav-icon {
    display: inline-grid;
    width: 34px;
    height: 34px;
    place-items: center;
    border-radius: var(--radius);
    background: transparent;
    color: inherit;
}

.admin-nav-link strong {
    display: block;
    overflow: hidden;
    color: inherit;
    font-size: 13px;
    font-weight: 760;
    line-height: 1.2;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.admin-nav-link span:not(.admin-nav-icon) {
    display: block;
    margin-top: 2px;
    overflow: hidden;
    color: var(--muted);
    font-size: 11px;
    font-weight: 560;
    line-height: 1.2;
    text-overflow: ellipsis;
    text-transform: none;
    white-space: nowrap;
}

.admin-nav-link:hover span:not(.admin-nav-icon),
.admin-nav-link.active span:not(.admin-nav-icon) {
    color: var(--primary-strong);
}

.admin-sidebar-meta {
    display: grid;
    gap: 4px;
    margin-top: auto;
    padding: 10px;
    border: 1px solid var(--line-soft);
    border-radius: var(--radius);
    background: var(--surface-soft);
}

.admin-sidebar-meta span {
    color: var(--muted);
    font-size: 11px;
    font-weight: 650;
}

.admin-sidebar-meta strong {
    overflow: hidden;
    font-size: 13px;
    font-weight: 740;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.admin-sidebar-note,
.admin-sidebar-account {
    display: none;
}

.admin-sidebar-overlay {
    display: none;
}

.admin-pagebar {
    position: sticky;
    top: 0;
    z-index: 30;
    display: flex;
    min-height: 72px;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin: 0 -28px 4px;
    padding: 13px 28px;
    border: 0;
    border-bottom: 1px solid rgba(217, 224, 234, 0.82);
    border-radius: 0;
    background: rgba(246, 248, 251, 0.9);
    backdrop-filter: blur(18px);
}

.admin-pagebar-title {
    display: flex;
    min-width: 0;
    align-items: center;
    gap: 12px;
}

.admin-pagebar .eyebrow {
    margin: 0 0 1px;
    color: var(--muted);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
}

.admin-pagebar h1 {
    margin: 0;
    color: var(--text);
    font-family: var(--font-display);
    font-size: 22px;
    font-weight: 820;
    line-height: 1.15;
}

.admin-pagebar-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.admin-mobile-nav-button {
    display: none;
}

.admin-date-pill,
.admin-pill,
.trend-pill {
    display: inline-flex;
    min-height: 30px;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 0 10px;
    border: 1px solid var(--line);
    border-radius: 999px;
    background: var(--surface);
    color: var(--muted-strong);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0;
    text-transform: none;
}

.admin-pill {
    color: var(--primary);
    border-color: rgba(184, 134, 11, 0.18);
    background: var(--primary-soft);
}

.button,
button.button,
.ghost-button,
button.ghost-button {
    appearance: none;
    display: inline-flex;
    min-height: 38px;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 0 13px;
    border-radius: var(--radius);
    cursor: pointer;
    font-size: 13px;
    font-weight: 760;
    text-decoration: none;
    transition: background 0.16s ease, border-color 0.16s ease, color 0.16s ease, box-shadow 0.16s ease;
}

.button {
    border: 1px solid var(--primary);
    background: var(--primary);
    color: #ffffff;
    box-shadow: 0 8px 18px rgba(184, 134, 11, 0.18);
}

.button:hover {
    border-color: var(--primary-strong);
    background: var(--primary-strong);
}

.ghost-button {
    border: 1px solid var(--line);
    background: var(--surface);
    color: var(--text);
    box-shadow: none;
}

.ghost-button:hover {
    border-color: rgba(184, 134, 11, 0.26);
    background: var(--primary-soft);
    color: var(--primary);
}

.admin-profile-menu {
    position: relative;
}

.admin-profile-menu summary {
    display: flex;
    min-height: 40px;
    align-items: center;
    gap: 9px;
    padding: 4px 8px 4px 4px;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--surface);
    cursor: pointer;
}

.admin-avatar {
    display: grid;
    width: 30px;
    height: 30px;
    place-items: center;
    border-radius: var(--radius);
    background: var(--accent-soft);
    color: var(--accent);
    font-size: 11px;
    font-weight: 800;
}

.admin-profile-copy {
    display: grid;
    min-width: 0;
}

.admin-profile-copy strong {
    font-size: 12px;
    font-weight: 740;
    line-height: 1.1;
}

.admin-profile-copy span {
    max-width: 180px;
    overflow: hidden;
    color: var(--muted);
    font-size: 11px;
    line-height: 1.2;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.admin-profile-panel {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    z-index: 60;
    display: grid;
    width: 230px;
    gap: 10px;
    padding: 12px;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--surface);
    box-shadow: var(--shadow);
}

.admin-profile-panel p {
    margin: 0;
    color: var(--muted);
    font-size: 12px;
}

.admin-profile-panel .ghost-button {
    width: 100%;
}

/* Profile menu follows the compact admin control language rather than the
 * larger legacy form-button treatment. */
body.is-admin .admin-profile-panel {
    width: min(250px, calc(100vw - 32px));
    gap: 8px;
    padding: 14px;
    border-color: #e1e5e9;
    border-radius: 10px;
    background: #fff;
    box-shadow: 0 14px 30px rgba(31, 38, 48, 0.12);
}

body.is-admin .admin-profile-panel > strong {
    color: #25292f;
    font-size: 14px;
    line-height: 1.2;
}

body.is-admin .admin-profile-panel > p {
    margin: -2px 0 4px;
    color: #858b94;
    font-size: 11px;
    overflow-wrap: anywhere;
}

body.is-admin .admin-profile-panel .ghost-button,
body.is-admin .admin-profile-panel form .ghost-button {
    min-height: 36px;
    justify-content: flex-start;
    padding-inline: 11px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

body.is-admin .admin-profile-panel form { margin: 0; }

@media (max-width: 760px) {
    body.is-admin .admin-profile-panel {
        position: fixed;
        top: 76px;
        right: 14px;
        width: min(250px, calc(100vw - 28px));
    }
}

.panel {
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--panel);
    box-shadow: var(--shadow-soft);
    backdrop-filter: none;
}

.panel-padded {
    padding: 18px;
}

.panel-head {
    display: grid;
    gap: 5px;
    margin: 0 0 16px;
}

.panel-head--tight {
    gap: 3px;
    margin-bottom: 14px;
}

.panel-head--row {
    display: flex;
    flex-wrap: wrap;
    align-items: start;
    justify-content: space-between;
    gap: 12px 18px;
}

.panel-head--row > div:first-child {
    display: grid;
    gap: 5px;
}

.panel-head__link {
    color: var(--primary);
    font-size: 12px;
    font-weight: 740;
    white-space: nowrap;
}

.panel-head__link:hover {
    text-decoration: underline;
}

.panel-head h2,
.panel-title,
.section-heading h2,
.page-header h1,
.hero-banner h1 {
    margin: 0;
    color: var(--text);
    font-family: var(--font-display);
    font-weight: 820;
    letter-spacing: 0;
}

.panel-head h2 {
    font-size: 20px;
    line-height: 1.2;
}

.panel-title {
    font-size: 16px;
    line-height: 1.25;
}

.panel-copy,
.panel-head p,
.section-heading p,
.page-header p,
.hero-banner p,
.field-hint {
    margin: 0;
    color: var(--muted);
    font-size: 13px;
    line-height: 1.55;
}

.eyebrow,
.metric-label {
    display: inline-flex;
    margin: 0;
    color: var(--muted);
    font-size: 11px;
    font-weight: 760;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

/*
 * Overview topline: a single status band replacing the old full-height hero,
 * so the KPI row sits within the first screen instead of below it.
 */
.dash-topline {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px 16px;
    padding: 4px 0 0;
}

.status-chip-row {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.status-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 32px;
    padding: 0 12px;
    border: 1px solid var(--line);
    border-radius: 999px;
    background: var(--surface);
    color: var(--muted-strong);
    font-size: 12px;
    font-weight: 700;
}

.status-chip i {
    width: 7px;
    height: 7px;
    flex: 0 0 auto;
    border-radius: 999px;
    background: var(--muted);
}

.status-chip small {
    color: var(--muted);
    font-size: 11px;
    font-weight: 600;
}

.status-chip--ok i {
    background: var(--success);
    box-shadow: 0 0 0 3px rgba(22, 133, 86, 0.16);
}

.status-chip--warn i {
    background: var(--warning);
    box-shadow: 0 0 0 3px rgba(183, 121, 31, 0.16);
}

.status-chip--promo {
    border-color: rgba(184, 134, 11, 0.28);
    background: var(--primary-soft);
    color: var(--primary-strong);
}

.status-chip--promo i {
    background: var(--primary);
    box-shadow: 0 0 0 3px rgba(184, 134, 11, 0.18);
}

.status-chip--promo small {
    color: var(--primary-strong);
    opacity: 0.78;
}

/* Still used by the proposal studio hero. */
.dashboard-command {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(300px, 0.7fr);
    gap: 14px;
    align-items: stretch;
    padding-top: 4px;
}

.dashboard-command-main {
    display: grid;
    gap: 12px;
    align-content: center;
    padding: 20px;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--surface);
    box-shadow: var(--shadow-soft);
}

.dashboard-command-main h2 {
    max-width: 760px;
    margin: 0;
    color: var(--text);
    font-size: 25px;
    font-weight: 840;
    line-height: 1.12;
}

.dashboard-command-main p {
    max-width: 620px;
    margin: 0;
    color: var(--muted);
    font-size: 13.5px;
}

.dash-topline__actions,
.dashboard-command-actions,
.hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.dashboard-status-grid,
.page-header-aside {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
}

.status-card {
    display: grid;
    gap: 7px;
    min-height: 0;
    padding: 16px;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--surface);
    box-shadow: var(--shadow-soft);
}

.status-card strong {
    color: var(--text);
    font-size: 17px;
    font-weight: 760;
}

.status-card p {
    margin: 0;
    color: var(--muted);
    font-size: 13px;
}

.section-heading {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 16px;
    margin: 6px 0 -4px;
    padding: 0;
    border: 0;
}

.section-heading > div {
    display: grid;
    gap: 5px;
}

.section-heading h2 {
    font-size: 26px;
    line-height: 1.12;
}

.kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
}

/*
 * Fixed row track: the label sits on its own line and the figure row is the
 * second track in every card, so the numbers share a baseline even when one
 * label wraps.
 */
.kpi-card {
    display: grid;
    grid-template-rows: auto auto 1fr auto;
    gap: 10px;
    padding: 16px;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--surface);
    box-shadow: var(--shadow-soft);
    transition: border-color 0.16s ease, box-shadow 0.16s ease;
}

.kpi-card:hover {
    border-color: rgba(184, 134, 11, 0.3);
    box-shadow: var(--shadow);
}

.kpi-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.kpi-figure {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    justify-content: space-between;
    gap: 6px 10px;
}

.kpi-value {
    color: var(--text);
    font-family: var(--font-display);
    font-size: 30px;
    font-weight: 840;
    font-variant-numeric: tabular-nums;
    line-height: 1;
}

.kpi-hint {
    margin: 0;
    color: var(--muted);
    font-size: 12.5px;
    line-height: 1.5;
}

.kpi-context {
    color: var(--muted);
    font-size: 11px;
    font-weight: 650;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.kpi-grid--compact .kpi-card {
    grid-template-rows: auto auto auto;
    gap: 8px;
    min-height: 0;
}

.kpi-grid--compact .kpi-context {
    align-self: end;
}

.trend-pill--up,
.trend-pill--positive {
    color: var(--success);
    border-color: rgba(22, 133, 86, 0.18);
    background: #eaf7f1;
}

.trend-pill--down,
.trend-pill--negative {
    color: var(--danger);
    border-color: rgba(194, 65, 85, 0.18);
    background: #fff0f3;
}

.trend-pill--neutral {
    color: var(--muted-strong);
    background: var(--surface-soft);
}

.analytics-grid,
.dashboard-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(300px, 0.72fr);
    gap: 16px;
    align-items: start;
}

.dashboard-overview-chart-panel {
    min-width: 0;
}

.dashboard-overview-chart-panel .line-chart-shell {
    min-height: 280px;
}

.dashboard-signal-strip {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 8px;
    margin-top: 12px;
}

.dashboard-signal {
    display: grid;
    gap: 5px;
    min-width: 0;
    padding: 11px 12px;
    border: 1px solid var(--line-soft);
    border-radius: var(--radius);
    background: var(--surface-soft);
}

.dashboard-signal strong {
    overflow: hidden;
    color: var(--text);
    font-size: 14px;
    font-weight: 700;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.mini-chart--dashboard {
    min-height: 190px;
}

/* Panels size to their content instead of stretching to the tallest sibling.
   The third column holds the monthly bar chart, so it gets the extra room. */
.insight-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(0, 1.45fr);
    gap: 16px;
    align-items: start;
}

.project-analytics-grid,
.project-detail-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    gap: 16px;
    align-items: start;
}

.project-status-chart-wrap {
    display: grid;
    grid-template-columns: minmax(170px, 0.8fr) minmax(0, 1fr);
    align-items: center;
    gap: 24px;
    min-height: 238px;
}

.project-status-chart {
    display: grid;
    width: min(210px, 100%);
    aspect-ratio: 1;
    place-items: center;
    margin: 0 auto;
    border-radius: 50%;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.7);
}

.project-status-chart__centre {
    display: grid;
    width: 56%;
    aspect-ratio: 1;
    place-content: center;
    border: 1px solid var(--line-soft);
    border-radius: 50%;
    background: var(--surface);
    text-align: center;
}

.project-status-chart__centre strong {
    color: var(--text);
    font-family: var(--font-display);
    font-size: 27px;
    line-height: 1;
}

.project-status-chart__centre span {
    margin-top: 5px;
    color: var(--muted);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.project-status-legend {
    display: grid;
    gap: 13px;
}

.project-status-legend__item {
    display: flex;
    align-items: flex-start;
    gap: 9px;
}

.project-status-legend__dot {
    width: 9px;
    height: 9px;
    flex: 0 0 9px;
    margin-top: 4px;
    border-radius: 50%;
}

.project-status-legend__dot--0 { background: #b8860b; }
.project-status-legend__dot--1 { background: #2f8054; }
.project-status-legend__dot--2 { background: #6f5015; }
.project-status-legend__dot--3 { background: #c08a4a; }
.project-status-legend__dot--4 { background: #343b48; }
.project-status-legend__dot--5 { background: #b94a3d; }

.project-status-legend__item strong,
.project-status-legend__item small {
    display: block;
}

.project-status-legend__item strong {
    color: var(--muted-strong);
    font-size: 12px;
}

.project-status-legend__item small {
    margin-top: 2px;
    color: var(--muted);
    font-size: 11px;
}

.project-file-bars {
    display: grid;
    gap: 17px;
    padding-top: 8px;
}

.project-file-bar {
    display: grid;
    gap: 8px;
}

.project-file-bar .bar-header {
    margin: 0;
}

.project-empty-chart,
.project-files-empty {
    display: grid;
    justify-items: start;
    align-content: center;
    min-height: 218px;
    padding: 24px;
    border: 1px dashed var(--line);
    border-radius: var(--radius);
    background: var(--surface-soft);
}

.project-empty-chart__icon,
.project-files-empty__icon {
    display: grid;
    width: 38px;
    height: 38px;
    place-items: center;
    border-radius: 12px;
    background: var(--primary-soft);
    color: var(--primary-strong);
    font-size: 20px;
    font-weight: 500;
}

.project-empty-chart strong,
.project-files-empty h3 {
    margin-top: 14px;
    color: var(--text);
    font-size: 15px;
}

.project-empty-chart p,
.project-files-empty p {
    max-width: 400px;
    margin: 5px 0 0;
    color: var(--muted);
    font-size: 12px;
    line-height: 1.6;
}

.project-status-badge,
.file-share-badge,
.file-private-badge {
    display: inline-flex;
    align-items: center;
    min-height: 26px;
    padding: 4px 9px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.07em;
    text-transform: uppercase;
}

.project-status-badge,
.file-private-badge {
    background: var(--surface-soft);
    color: var(--muted-strong);
}

.file-share-badge {
    background: #eaf7f1;
    color: var(--success);
}

.project-detail-grid {
    grid-template-columns: minmax(0, 1.55fr) minmax(300px, 0.7fr);
}

.project-file-list {
    display: grid;
    gap: 12px;
}

.project-file-card {
    display: grid;
    grid-template-columns: 42px minmax(0, 1fr);
    gap: 13px;
    padding: 15px;
    border: 1px solid var(--line-soft);
    border-radius: var(--radius);
    background: linear-gradient(135deg, var(--surface), var(--surface-soft));
}

.project-file-card__icon {
    display: grid;
    width: 42px;
    height: 42px;
    place-items: center;
    border-radius: 12px;
    background: var(--primary-soft);
    color: var(--primary-strong);
    font-size: 11px;
    font-weight: 800;
}

.project-file-card__body,
.project-file-card__heading,
.project-file-card__heading > div {
    min-width: 0;
}

.project-file-card__heading {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}

.project-file-card h3 {
    margin: 0;
    overflow-wrap: anywhere;
    color: var(--text);
    font-size: 14px;
    line-height: 1.3;
}

.project-file-card__heading p,
.project-file-card__description {
    margin: 5px 0 0;
    color: var(--muted);
    font-size: 11px;
    line-height: 1.5;
}

.project-file-card__description {
    padding: 9px 10px;
    border-left: 2px solid var(--line);
    background: rgba(255, 255, 255, 0.55);
}

.project-file-card__actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    margin-top: 13px;
}

.project-file-card__actions .button,
.project-file-card__actions .ghost-button {
    min-height: 34px;
    padding: 0 11px;
    border-radius: 9px;
    font-size: 11px;
}

.project-file-card__actions form {
    margin: 0;
}

.file-delete-button {
    min-height: 34px;
    padding: 0 5px;
    border: 0;
    background: transparent;
    color: var(--danger);
    cursor: pointer;
    font-size: 11px;
    font-weight: 700;
}

.project-file-edit {
    position: relative;
}

.project-file-edit > summary {
    min-height: 34px;
    padding: 0 11px;
    border-radius: 9px;
    cursor: pointer;
    font-size: 11px;
    list-style: none;
}

.project-file-edit > summary::-webkit-details-marker {
    display: none;
}

.project-file-edit > summary:hover,
.project-file-edit[open] > summary {
    border-color: var(--primary);
    color: var(--primary-strong);
}

.project-file-edit__form {
    display: grid;
    min-width: min(330px, calc(100vw - 48px));
    gap: 8px;
    margin-top: 8px;
    padding: 13px;
    border: 1px solid var(--line-soft);
    border-radius: 9px;
    background: var(--surface);
    box-shadow: 0 10px 25px rgba(24, 29, 35, .08);
}

.project-file-edit__form label {
    color: var(--muted-strong);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
}

.project-file-edit__form input,
.project-file-edit__form textarea {
    width: 100%;
    border: 1px solid var(--line);
    border-radius: 7px;
    background: var(--surface-soft);
    color: var(--text);
    font: inherit;
    font-size: 11px;
}

.project-file-edit__form input {
    min-height: 38px;
    padding: 8px;
}

.project-file-edit__form textarea {
    min-height: 68px;
    padding: 9px;
    resize: vertical;
}

.project-file-edit__form small {
    color: var(--muted);
    font-size: 10px;
    line-height: 1.45;
}

.project-file-edit__form .button {
    min-height: 34px;
    margin-top: 3px;
    border-radius: 7px;
    font-size: 11px;
}

.project-share-link {
    display: grid;
    gap: 6px;
    margin-top: 13px;
    padding: 11px;
    border: 1px solid rgba(47, 128, 84, 0.18);
    border-radius: 10px;
    background: #f4fbf7;
}

.project-share-link label,
.project-share-link small {
    color: var(--muted);
    font-size: 10px;
}

.project-share-link label {
    font-weight: 800;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.project-share-link > div {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 7px;
}

.project-share-link input {
    min-width: 0;
    height: 34px;
    padding: 0 9px;
    border: 1px solid rgba(47, 128, 84, 0.2);
    border-radius: 8px;
    background: #fff;
    color: var(--muted-strong);
    font-size: 11px;
}

.project-share-link .ghost-button {
    min-height: 34px;
    padding-inline: 11px;
    border-color: rgba(47, 128, 84, 0.22);
    color: var(--success);
}

.project-file-upload-form {
    display: grid;
    gap: 9px;
    margin-top: 18px;
}

.project-file-upload-form label {
    margin-top: 5px;
}

.project-file-upload-form label span {
    color: var(--muted);
    font-weight: 500;
}

.project-file-upload-form input[type="file"] {
    width: 100%;
    padding: 9px;
    border: 1px dashed var(--line);
    border-radius: 10px;
    background: var(--surface-soft);
    color: var(--muted-strong);
    font-size: 12px;
}

.project-file-upload-form textarea {
    min-height: 92px;
}

.project-index-upload-form {
    display: grid;
    grid-template-columns: minmax(180px, 0.8fr) minmax(220px, 1fr);
    gap: 14px 18px;
    margin-top: 20px;
}

.project-index-upload-form .field-full,
.project-index-upload-form__actions {
    grid-column: 1 / -1;
}

.project-index-upload-form input[type="file"] {
    width: 100%;
    padding: 9px;
    border: 1px dashed var(--line);
    border-radius: 10px;
    background: var(--surface-soft);
    color: var(--muted-strong);
    font-size: 12px;
}

.project-index-upload-form textarea {
    min-height: 76px;
}

.project-index-upload-form__actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding-top: 2px;
}

.project-index-upload-form__actions .form-help {
    margin: 0;
}

.project-upload-empty {
    display: grid;
    justify-items: start;
    gap: 7px;
    margin-top: 20px;
    padding: 18px;
    border: 1px dashed var(--line);
    border-radius: var(--radius);
    background: var(--surface-soft);
}

.project-upload-empty strong {
    color: var(--text);
    font-size: 15px;
}

.project-upload-empty p {
    max-width: 760px;
    margin: 0 0 7px;
    color: var(--muted);
    font-size: 12px;
    line-height: 1.6;
}

.form-error {
    margin: 0;
    color: var(--danger);
    font-size: 12px;
}

.sticky-stack {
    display: grid;
    gap: 16px;
}

.sticky-stack .panel:first-child {
    position: sticky;
    top: 92px;
}

.chart-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 12px;
}

/* Sitting in the panel head, the legend must stay on one line. */
.panel-head--row .chart-legend {
    display: flex;
    flex: 0 0 auto;
    flex-wrap: nowrap;
    margin-bottom: 0;
}

.panel-head--row .legend-item {
    white-space: nowrap;
}

.legend-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--muted-strong);
    font-size: 12px;
    font-weight: 650;
}

.legend-swatch {
    width: 8px;
    height: 8px;
    border-radius: 999px;
}

.legend-swatch--visits {
    background: var(--traffic);
}

.legend-swatch--quotes {
    background: var(--quote);
}

.legend-swatch--messages {
    background: var(--lead);
}

.line-chart-shell {
    position: relative;
    overflow: hidden;
    padding: 6px 4px 2px;
    border: 1px solid var(--line-soft);
    border-radius: var(--radius);
    background: linear-gradient(180deg, #ffffff, #fffdf8);
}

.line-chart {
    display: block;
    width: 100%;
    height: auto;
    overflow: visible;
}

.chart-grid-line {
    stroke: rgba(184, 134, 11, 0.14);
    stroke-width: 1;
}

.chart-axis-label {
    fill: var(--muted);
    font-family: var(--font-sans);
    font-size: 10px;
    font-weight: 650;
    font-variant-numeric: tabular-nums;
}

.chart-area {
    stroke: none;
}

.chart-line {
    fill: none;
    stroke-width: 2.4;
    stroke-linecap: round;
    stroke-linejoin: round;
    vector-effect: non-scaling-stroke;
}

.chart-dot {
    stroke: #ffffff;
    stroke-width: 1.6;
}

.chart-dot--visits {
    fill: var(--traffic);
}

.chart-dot--quotes {
    fill: var(--quote);
}

.chart-dot--messages {
    fill: var(--lead);
}

.chart-empty {
    position: absolute;
    top: 50%;
    left: 50%;
    margin: 0;
    padding: 7px 14px;
    transform: translate(-50%, -50%);
    border: 1px solid var(--line-soft);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.92);
    color: var(--muted);
    font-size: 12px;
    font-weight: 650;
}

.chart-line--visits {
    stroke: var(--traffic);
}

.chart-line--quotes {
    stroke: var(--quote);
}

.chart-line--messages {
    stroke: var(--lead);
}

.chart-summary-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;
    margin-top: 12px;
}

.chart-summary-grid + .data-note,
.activity-feed + .data-note {
    margin-top: 14px;
}

/*
 * Keyboard focus was previously invisible on every control in the admin.
 * One ring definition, applied wherever focus can land.
 */
.admin-nav-link:focus-visible,
.admin-icon-button:focus-visible,
.button:focus-visible,
.ghost-button:focus-visible,
.shortcut-grid a:focus-visible,
.record-list > li > a:focus-visible,
.workspace-link-grid a:focus-visible,
.panel-head__link:focus-visible,
.action-menu summary:focus-visible,
.admin-profile-menu summary:focus-visible,
.wizard-progress-button:focus-visible,
.template-card:focus-within,
.quote-table a:focus-visible,
.rich-editor-toolbar button:focus-visible {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}

.field input:focus-visible,
.field select:focus-visible,
.field textarea:focus-visible,
.field-full input:focus-visible,
.field-full select:focus-visible,
.field-full textarea:focus-visible {
    outline: none;
}

.mini-card,
.meta-item,
.bar-row,
.activity-item,
.data-note,
.review-card,
.wizard-note,
.stack-list li,
.mini-list li {
    border: 1px solid var(--line-soft);
    border-radius: var(--radius);
    background: var(--surface-soft);
    box-shadow: none;
}

.mini-card {
    display: grid;
    gap: 4px;
    padding: 12px;
}

.mini-card strong {
    color: var(--text);
    font-size: 17px;
    font-weight: 760;
}

.mini-card p {
    margin: 0;
    color: var(--muted);
    font-size: 12px;
}

.data-note {
    padding: 12px;
    color: var(--muted-strong);
    font-size: 13px;
}

.bar-list,
.meta-list,
.mini-list,
.activity-feed {
    display: grid;
    gap: 10px;
}

.bar-row {
    display: grid;
    gap: 9px;
    padding: 12px;
}

.bar-header {
    display: flex;
    align-items: start;
    justify-content: space-between;
    gap: 12px;
}

.bar-row strong,
.meta-item strong {
    color: var(--text);
    font-size: 14px;
    font-weight: 760;
}

.bar-meta {
    display: block;
    margin-top: 2px;
    color: var(--muted);
    font-size: 12px;
}

.bar-track {
    height: 7px;
    overflow: hidden;
    border-radius: 999px;
    background: #e8edf4;
}

.bar-fill {
    height: 100%;
    border-radius: inherit;
    background: var(--primary);
}

.bar-fill--quote {
    background: var(--accent);
}

.bar-fill--lead {
    background: var(--lead);
}

.bar-count {
    font-variant-numeric: tabular-nums;
}

/*
 * Stat rows read label -> value -> context. The old markup led with the value,
 * which put the answer before the question it belonged to.
 */
.stat-list {
    display: grid;
    gap: 8px;
}

.stat-row {
    display: grid;
    gap: 2px;
    padding: 11px 12px;
    border: 1px solid var(--line-soft);
    border-radius: var(--radius);
    background: var(--surface-soft);
}

.stat-row__label {
    color: var(--muted);
    font-size: 11px;
    font-weight: 780;
    letter-spacing: 0.07em;
    text-transform: uppercase;
}

.stat-row__value {
    color: var(--text);
    font-size: 17px;
    font-weight: 780;
    line-height: 1.25;
}

.stat-row__meta {
    color: var(--muted);
    font-size: 12px;
    line-height: 1.45;
}

/* Two-up on the wide overview panel so four rows fill the column evenly. */
.stat-list--split {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.shortcut-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
}

.shortcut-grid a {
    display: grid;
    gap: 2px;
    padding: 12px;
    border: 1px solid var(--line-soft);
    border-radius: var(--radius);
    background: var(--surface-soft);
    color: var(--text);
    transition: background 0.16s ease, border-color 0.16s ease, color 0.16s ease;
}

.shortcut-grid a:hover {
    border-color: rgba(184, 134, 11, 0.3);
    background: var(--primary-soft);
    color: var(--primary);
}

.shortcut-icon {
    display: inline-grid;
    width: 30px;
    height: 30px;
    margin-bottom: 6px;
    place-items: center;
    border-radius: var(--radius);
    background: var(--surface);
    color: var(--primary);
    box-shadow: inset 0 0 0 1px var(--line-soft);
}

.shortcut-icon svg {
    width: 16px;
    height: 16px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.9;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.shortcut-grid strong {
    font-size: 13px;
    font-weight: 780;
    line-height: 1.2;
}

.shortcut-grid span:not(.shortcut-icon) {
    color: var(--muted);
    font-size: 11.5px;
    line-height: 1.35;
}

.shortcut-grid a:hover span:not(.shortcut-icon) {
    color: var(--primary-strong);
}

.record-list {
    display: grid;
    gap: 6px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.record-list > li > a {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 10px 12px;
    border: 1px solid var(--line-soft);
    border-radius: var(--radius);
    background: var(--surface-soft);
    color: var(--text);
    transition: background 0.16s ease, border-color 0.16s ease;
}

.record-list > li > a:hover {
    border-color: rgba(184, 134, 11, 0.3);
    background: var(--primary-soft);
}

.record-list__main {
    display: grid;
    min-width: 0;
    gap: 1px;
}

.record-list__main strong {
    overflow: hidden;
    font-size: 13px;
    font-weight: 760;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.record-list__main span {
    color: var(--muted);
    font-size: 11.5px;
}

.record-list__amount {
    flex: 0 0 auto;
    color: var(--primary-strong);
    font-size: 13px;
    font-weight: 800;
    font-variant-numeric: tabular-nums;
}

.record-list__empty {
    display: grid;
    gap: 3px;
    padding: 12px;
    border: 1px dashed var(--line);
    border-radius: var(--radius);
    background: var(--surface-soft);
}

.record-list__empty strong {
    font-size: 13px;
    font-weight: 760;
}

.record-list__empty span {
    color: var(--muted);
    font-size: 12px;
}

/* Six month columns overflowed the panel; they now shrink first and scroll
   only when the column is genuinely too narrow. */
.mini-chart {
    display: grid;
    grid-template-columns: repeat(6, minmax(42px, 1fr));
    align-items: stretch;
    gap: 8px;
    min-height: 220px;
    overflow-x: auto;
    padding-bottom: 4px;
}

.mini-chart.mini-chart--dashboard {
    min-height: 190px;
}

/* Fixed row tracks keep the month names and counts on a shared baseline
   regardless of how tall each bar is. */
.month-bar {
    display: grid;
    grid-template-rows: auto minmax(0, 1fr) auto auto;
    min-width: 0;
    justify-items: center;
    gap: 6px;
    color: var(--muted);
    font-size: 10.5px;
    line-height: 1.3;
    text-align: center;
}

.month-bar-column {
    width: 100%;
    max-width: 36px;
    align-self: end;
    border-radius: 7px 7px 0 0;
    background: linear-gradient(180deg, var(--primary), var(--accent));
}

.month-bar strong {
    color: var(--text);
    font-size: 12px;
}

.quote-wizard {
    display: grid;
    gap: 18px;
}

.wizard-progress {
    display: grid;
    grid-template-columns: repeat(4, minmax(145px, 1fr));
    gap: 8px;
    margin-bottom: 2px;
    overflow-x: auto;
    padding-bottom: 3px;
}

.wizard-progress-button {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr);
    gap: 9px;
    align-items: center;
    min-height: 56px;
    padding: 10px;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--surface);
    color: var(--muted-strong);
    text-align: left;
    cursor: pointer;
}

.wizard-progress-button.is-active,
.wizard-progress-button.is-complete {
    border-color: rgba(184, 134, 11, 0.22);
    background: var(--primary-soft);
    color: var(--primary);
}

.wizard-progress-index {
    display: grid;
    width: 28px;
    height: 28px;
    place-items: center;
    border-radius: 999px;
    background: var(--surface-soft);
    color: inherit;
    font-size: 11px;
    font-weight: 800;
}

.wizard-progress-copy {
    min-width: 0;
}

.wizard-progress-copy strong {
    display: block;
    color: inherit;
    font-size: 13px;
    font-weight: 760;
    line-height: 1.15;
}

.wizard-progress-copy span {
    display: block;
    overflow: hidden;
    color: var(--muted);
    font-size: 11px;
    line-height: 1.25;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.wizard-pane {
    display: none;
}

.wizard-pane.is-active {
    display: block;
}

.wizard-pane-grid,
.form-grid,
.review-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}

.field,
.field-full {
    display: grid;
    gap: 7px;
}

.field-full {
    grid-column: 1 / -1;
}

.field label,
.field-full > label {
    color: var(--text);
    font-size: 12px;
    font-weight: 740;
}

.field input,
.field select,
.field textarea,
.field-full input,
.field-full select,
.field-full textarea {
    width: 100%;
    min-height: 42px;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--surface);
    color: var(--text);
    padding: 9px 11px;
    outline: none;
    transition: border-color 0.16s ease, box-shadow 0.16s ease;
}

.field textarea,
.field-full textarea {
    min-height: 126px;
    resize: vertical;
}

.field input:focus,
.field select:focus,
.field textarea:focus,
.field-full input:focus,
.field-full select:focus,
.field-full textarea:focus {
    border-color: rgba(184, 134, 11, 0.54);
    box-shadow: 0 0 0 3px rgba(184, 134, 11, 0.12);
}

.template-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
}

.template-card {
    position: relative;
    display: grid;
    gap: 10px;
    min-height: 132px;
    padding: 13px;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--surface);
    cursor: pointer;
}

.template-card:hover,
.template-card:has(input:checked) {
    border-color: rgba(184, 134, 11, 0.34);
    background: var(--primary-soft);
}

.template-card input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.template-card strong {
    color: var(--text);
    font-size: 14px;
    font-weight: 760;
    line-height: 1.2;
}

.template-card p {
    display: none;
}

.swatch-row {
    display: flex;
    gap: 5px;
    margin-top: auto;
}

.swatch {
    width: 22px;
    height: 22px;
    border: 1px solid rgba(17, 24, 39, 0.12);
    border-radius: 999px;
}

.wizard-note,
.review-card {
    display: grid;
    gap: 7px;
    padding: 13px;
}

.wizard-note strong,
.review-card strong {
    color: var(--text);
    font-size: 14px;
    font-weight: 760;
}

.wizard-note p,
.review-card span,
.review-card li {
    color: var(--muted);
    font-size: 13px;
}

.review-list {
    margin: 0;
    padding-left: 18px;
}

.wizard-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: 18px;
    padding-top: 14px;
    border-top: 1px solid var(--line-soft);
}

.wizard-actions-group {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.line-items-editor {
    display: grid;
    gap: 12px;
    padding: 14px;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--surface-soft);
}

.line-items-editor-head,
.line-items-total {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.line-item-rows {
    display: grid;
    gap: 10px;
}

.line-item-row {
    display: grid;
    grid-template-columns: 34px minmax(0, 1.45fr) minmax(150px, 0.55fr) auto;
    gap: 10px;
    align-items: end;
    padding: 12px;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--surface);
}

.line-item-index {
    display: grid;
    width: 30px;
    height: 30px;
    place-items: center;
    align-self: center;
    border-radius: 999px;
    background: var(--primary-soft);
    color: var(--primary);
    font-size: 11px;
    font-weight: 800;
}

.line-items-total {
    padding: 12px;
    border: 1px solid rgba(184, 134, 11, 0.16);
    border-radius: var(--radius);
    background: var(--primary-soft);
}

.line-items-total span,
.naira-total-card span {
    color: var(--muted);
    font-size: 12px;
    font-weight: 700;
}

.line-items-total strong,
.naira-total-card strong {
    color: var(--primary);
    font-size: 20px;
    font-weight: 800;
}

.line-items-currency-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(220px, 0.55fr);
    gap: 12px;
}

.naira-total-card {
    display: grid;
    gap: 4px;
    padding: 12px;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--surface);
}

.naira-total-card small {
    color: var(--muted);
}

.meta-item {
    display: grid;
    gap: 5px;
    padding: 12px;
}

.meta-item span,
.mini-list span,
.activity-item span {
    color: var(--muted);
    font-size: 12px;
}

.mini-list {
    margin: 0;
    padding: 0;
    list-style: none;
}

.workspace-link-grid {
    display: grid;
    gap: 9px;
}

.workspace-link-grid a {
    display: grid;
    gap: 3px;
    padding: 12px;
    border: 1px solid var(--line-soft);
    border-radius: var(--radius);
    background: var(--surface-soft);
    color: var(--text);
    font-size: 13px;
    font-weight: 780;
    transition: background 0.16s ease, border-color 0.16s ease, color 0.16s ease;
}

.workspace-link-grid a:hover {
    border-color: rgba(184, 134, 11, 0.24);
    background: var(--primary-soft);
    color: var(--primary);
}

.workspace-link-grid span {
    color: var(--muted);
    font-size: 12px;
    font-weight: 560;
}

.mini-list li,
.activity-item {
    display: grid;
    gap: 4px;
    padding: 12px;
}

.mini-list strong,
.activity-item strong {
    color: var(--text);
    font-size: 13px;
    font-weight: 750;
}

.activity-item-header {
    display: flex;
    align-items: start;
    justify-content: space-between;
    gap: 12px;
}

.activity-item p {
    margin: 0;
    color: var(--muted);
    font-size: 12px;
}

.table-wrap {
    overflow-x: auto;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--surface);
}

.quote-table {
    width: 100%;
    min-width: 860px;
    border-collapse: collapse;
}

.quote-table th,
.quote-table td {
    padding: 13px 14px;
    border-bottom: 1px solid var(--line-soft);
    text-align: left;
    vertical-align: middle;
}

.quote-table th {
    color: var(--muted);
    background: var(--surface-soft);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.quote-table td strong {
    display: block;
    color: var(--text);
    font-size: 13px;
    font-weight: 750;
}

.quote-table td span {
    display: block;
    margin-top: 2px;
    color: var(--muted);
    font-size: 12px;
}

.quote-table tr:last-child td {
    border-bottom: 0;
}

.table-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 7px;
}

.action-menu {
    position: relative;
    display: inline-block;
}

.action-menu summary {
    display: inline-flex;
    min-height: 34px;
    align-items: center;
    justify-content: center;
    padding: 0 12px;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--surface);
    color: var(--text);
    font-size: 12px;
    font-weight: 730;
    cursor: pointer;
}

.action-menu[open] summary,
.action-menu summary:hover {
    border-color: rgba(184, 134, 11, 0.28);
    background: var(--primary-soft);
    color: var(--primary);
}

.action-menu-panel {
    position: absolute;
    top: calc(100% + 6px);
    right: 0;
    z-index: 20;
    display: grid;
    min-width: 154px;
    padding: 6px;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--surface);
    box-shadow: var(--shadow);
}

.action-menu-panel.is-floating {
    position: fixed;
    top: 0;
    right: auto;
    z-index: 1000;
}

.action-menu-panel form {
    margin: 0;
}

.action-menu-panel a,
.action-menu-panel button {
    display: flex;
    width: 100%;
    min-height: 32px;
    align-items: center;
    padding: 0 9px;
    border: 0;
    border-radius: 6px;
    background: transparent;
    color: var(--text);
    font-size: 12px;
    font-weight: 680;
    text-align: left;
}

.action-menu-panel a:hover,
.action-menu-panel button:hover {
    background: var(--surface-soft);
    color: var(--primary);
}

.alert {
    padding: 13px 14px;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--surface);
    color: var(--muted-strong);
    box-shadow: none;
}

.alert-success {
    border-color: rgba(22, 133, 86, 0.2);
    background: #ecfdf5;
    color: #166534;
}

.alert-warning {
    border-color: rgba(183, 121, 31, 0.22);
    background: #fffbeb;
    color: #92400e;
}

.alert-danger {
    border-color: rgba(194, 65, 85, 0.22);
    background: #fff1f2;
    color: #9f1239;
}

.rich-editor {
    overflow: hidden;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--surface);
}

.rich-editor:focus-within {
    border-color: rgba(184, 134, 11, 0.54);
    box-shadow: 0 0 0 3px rgba(184, 134, 11, 0.12);
}

.rich-editor-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 8px;
    border-bottom: 1px solid var(--line-soft);
    background: var(--surface-soft);
}

.rich-editor-toolbar button {
    min-height: 30px;
    border: 1px solid var(--line);
    border-radius: 6px;
    background: var(--surface);
    color: var(--text);
    font-size: 12px;
    cursor: pointer;
}

.rich-editor-body {
    min-height: 128px;
    padding: 10px 11px;
    outline: none;
}

.rich-editor-feedback {
    display: none;
    color: var(--danger);
    font-size: 12px;
}

.rich-editor.is-invalid + .rich-editor-feedback {
    display: block;
}

.page-header,
.hero-banner {
    grid-template-columns: minmax(0, 1fr) minmax(280px, 0.6fr);
    padding: 22px;
    background: var(--surface);
}

.page-header h1,
.hero-banner h1 {
    font-size: 30px;
    line-height: 1.1;
}

.auth-grid,
.auth-card,
.auth-brand-panel,
.auth-hero {
    border-radius: var(--radius);
}

@media (min-width: 1101px) {
    body.is-sidebar-collapsed .admin-workspace--with-sidebar {
        grid-template-columns: var(--sidebar-collapsed-width) minmax(0, 1fr);
    }

    body.is-sidebar-collapsed .admin-sidebar-brand {
        justify-content: center;
        padding-inline: 4px;
    }

    body.is-sidebar-collapsed .admin-brand-copy,
    body.is-sidebar-collapsed .admin-nav-label,
    body.is-sidebar-collapsed .admin-nav-link div,
    body.is-sidebar-collapsed .admin-sidebar-meta {
        display: none;
    }

    body.is-sidebar-collapsed .admin-sidebar-inner {
        padding-inline: 10px;
    }

    body.is-sidebar-collapsed .admin-sidebar-top {
        flex-direction: column;
    }

    body.is-sidebar-collapsed .admin-nav-link {
        grid-template-columns: 1fr;
        justify-items: center;
        padding-inline: 4px;
    }

    body.is-sidebar-collapsed .admin-nav-link.active::before {
        left: -10px;
    }
}

/* Shared workspace treatment for every authenticated page outside the
 * dashboard canvas. It keeps documents, registers, builders, and settings in
 * the same calm product family as the redesigned overview. */
body.is-admin:not(.is-dashboard-overview) .admin-workspace--with-sidebar .admin-main {
    padding: 0 42px 56px;
    background: #fff;
}

body.is-admin:not(.is-dashboard-overview) .admin-pagebar {
    min-height: 96px;
    margin: 0 -42px 28px;
    padding: 24px 42px 18px;
    border-bottom: 1px solid #edf0f2;
    background: #fff;
    box-shadow: none;
}

body.is-admin:not(.is-dashboard-overview) .admin-pagebar h1 {
    font-family: var(--font-display);
    font-size: 24px;
    font-weight: 650;
    letter-spacing: -0.035em;
}

body.is-admin:not(.is-dashboard-overview) .admin-pagebar .eyebrow {
    color: #9298a0;
    font-size: 9px;
}

body.is-admin:not(.is-dashboard-overview) .admin-pagebar-actions .button {
    min-height: 34px;
    border-radius: 5px;
    background: #1c1e22;
    box-shadow: none;
    font-size: 11px;
}

body.is-admin:not(.is-dashboard-overview) .admin-pagebar-actions .button:hover {
    background: var(--primary-strong);
}

body.is-admin:not(.is-dashboard-overview) .admin-main > .hero-banner,
body.is-admin:not(.is-dashboard-overview) .admin-main > .page-header,
body.is-admin:not(.is-dashboard-overview) .admin-main > .section-heading {
    border: 0;
    border-bottom: 1px solid #edf0f2;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
}

body.is-admin:not(.is-dashboard-overview) .admin-main > .hero-banner,
body.is-admin:not(.is-dashboard-overview) .admin-main > .page-header {
    padding: 0 0 28px;
}

body.is-admin:not(.is-dashboard-overview) .hero-banner h1,
body.is-admin:not(.is-dashboard-overview) .page-header h1,
body.is-admin:not(.is-dashboard-overview) .section-heading h2 {
    max-width: 780px;
    color: #1b1d21;
    font-family: var(--font-display);
    font-size: 28px;
    font-weight: 620;
    letter-spacing: -0.04em;
    line-height: 1.08;
}

body.is-admin:not(.is-dashboard-overview) .hero-banner p,
body.is-admin:not(.is-dashboard-overview) .page-header p,
body.is-admin:not(.is-dashboard-overview) .section-heading p {
    max-width: 660px;
    color: #858b94;
    font-size: 12px;
}

body.is-admin:not(.is-dashboard-overview) .hero-callout {
    gap: 10px;
}

body.is-admin:not(.is-dashboard-overview) .hero-callout .callout-card {
    border: 0;
    border-left: 2px solid #ead9a2;
    border-radius: 0;
    background: #fffbed;
    box-shadow: none;
}

body.is-admin:not(.is-dashboard-overview) .panel {
    border-color: #edf0f2;
    border-radius: 9px;
    background: #fff;
    box-shadow: none;
}

body.is-admin:not(.is-dashboard-overview) .panel-padded {
    padding: 22px;
}

body.is-admin:not(.is-dashboard-overview) .panel-head h2,
body.is-admin:not(.is-dashboard-overview) .panel-title {
    color: #25292f;
    font-family: var(--font-display);
    font-size: 17px;
    font-weight: 620;
    letter-spacing: -0.025em;
}

body.is-admin:not(.is-dashboard-overview) .panel-head p,
body.is-admin:not(.is-dashboard-overview) .section-copy {
    color: #858b94;
    font-size: 11px;
}

body.is-admin:not(.is-dashboard-overview) .kpi-grid {
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0;
    border-top: 1px solid #edf0f2;
    border-bottom: 1px solid #edf0f2;
}

body.is-admin:not(.is-dashboard-overview) .kpi-card,
body.is-admin:not(.is-dashboard-overview) .kpi-card:hover {
    min-height: 96px;
    padding: 16px 20px;
    border: 0;
    border-right: 1px solid #edf0f2;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
    transform: none;
}

body.is-admin:not(.is-dashboard-overview) .kpi-card:first-child { padding-left: 0; }
body.is-admin:not(.is-dashboard-overview) .kpi-card:last-child { border-right: 0; }
body.is-admin:not(.is-dashboard-overview) .kpi-value { font-size: 25px; font-weight: 620; }
body.is-admin:not(.is-dashboard-overview) .kpi-context,
body.is-admin:not(.is-dashboard-overview) .kpi-meta { color: #9298a0; font-size: 10px; }
body.is-admin:not(.is-dashboard-overview) .trend-pill { min-height: 22px; padding-inline: 7px; border: 0; border-radius: 4px; font-size: 9px; }

body.is-admin:not(.is-dashboard-overview) .dashboard-grid,
body.is-admin:not(.is-dashboard-overview) .analytics-grid,
body.is-admin:not(.is-dashboard-overview) .insight-grid,
body.is-admin:not(.is-dashboard-overview) .project-analytics-grid,
body.is-admin:not(.is-dashboard-overview) .project-detail-grid {
    gap: 24px;
}

body.is-admin:not(.is-dashboard-overview) .table-wrap {
    overflow-x: auto;
    border: 0;
    border-top: 1px solid #edf0f2;
    border-radius: 0;
    background: transparent;
}

body.is-admin:not(.is-dashboard-overview) .quote-table {
    min-width: 760px;
}

body.is-admin:not(.is-dashboard-overview) .quote-table th,
body.is-admin:not(.is-dashboard-overview) .quote-table td {
    padding: 13px 10px;
    border-bottom-color: #edf0f2;
}

body.is-admin:not(.is-dashboard-overview) .quote-table th {
    background: transparent;
    color: #9a9fa7;
    font-size: 9px;
    letter-spacing: .1em;
}

body.is-admin:not(.is-dashboard-overview) .quote-table td {
    color: #69717b;
    font-size: 11px;
}

body.is-admin:not(.is-dashboard-overview) .quote-table tbody tr {
    transition: background 160ms ease;
}

body.is-admin:not(.is-dashboard-overview) .quote-table tbody tr:hover {
    background: #fbfcfc;
}

body.is-admin:not(.is-dashboard-overview) .form-grid {
    gap: 20px;
}

body.is-admin:not(.is-dashboard-overview) .field input,
body.is-admin:not(.is-dashboard-overview) .field select,
body.is-admin:not(.is-dashboard-overview) .field textarea,
body.is-admin:not(.is-dashboard-overview) .field-full input,
body.is-admin:not(.is-dashboard-overview) .field-full select,
body.is-admin:not(.is-dashboard-overview) .field-full textarea {
    border-color: #e4e7ea;
    border-radius: 6px;
    background: #fbfcfc;
    box-shadow: none;
}

body.is-admin:not(.is-dashboard-overview) .field input:focus,
body.is-admin:not(.is-dashboard-overview) .field select:focus,
body.is-admin:not(.is-dashboard-overview) .field textarea:focus,
body.is-admin:not(.is-dashboard-overview) .field-full input:focus,
body.is-admin:not(.is-dashboard-overview) .field-full select:focus,
body.is-admin:not(.is-dashboard-overview) .field-full textarea:focus {
    border-color: rgba(195, 141, 10, .65);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(195, 141, 10, .09);
}

body.is-admin:not(.is-dashboard-overview) .button,
body.is-admin:not(.is-dashboard-overview) .ghost-button {
    border-radius: 5px;
    box-shadow: none;
}

body.is-admin:not(.is-dashboard-overview) .button {
    background: #1c1e22;
    border-color: #1c1e22;
}

body.is-admin:not(.is-dashboard-overview) .button:hover {
    background: var(--primary-strong);
    border-color: var(--primary-strong);
}

body.is-admin:not(.is-dashboard-overview) .ghost-button {
    border-color: #e4e7ea;
    background: #fff;
}

body.is-admin:not(.is-dashboard-overview) .ghost-button:hover {
    border-color: rgba(195, 141, 10, .38);
    background: #fffbed;
}

body.is-admin:not(.is-dashboard-overview) .pm-subnav {
    padding: 0 0 10px;
    border: 0;
    border-bottom: 1px solid #edf0f2;
    border-radius: 0;
    background: transparent;
}

body.is-admin:not(.is-dashboard-overview) .pm-subnav a,
body.is-admin:not(.is-dashboard-overview) .pm-subnav-more summary {
    min-height: 30px;
    padding-inline: 8px;
    border-radius: 4px;
    font-size: 11px;
}

body.is-admin:not(.is-dashboard-overview) .pm-subnav a.active,
body.is-admin:not(.is-dashboard-overview) .pm-subnav a:hover,
body.is-admin:not(.is-dashboard-overview) .pm-subnav-more summary:hover {
    background: #fffbed;
    color: var(--primary-strong);
}

body.is-admin:not(.is-dashboard-overview) .proposal-builder-panel,
body.is-admin:not(.is-dashboard-overview) .proposal-template-card,
body.is-admin:not(.is-dashboard-overview) .project-file-card,
body.is-admin:not(.is-dashboard-overview) .pm-column {
    border-color: #edf0f2;
    border-radius: 9px;
    box-shadow: none;
}

body.is-admin:not(.is-dashboard-overview) .proposal-builder-panel .panel-head {
    border-bottom-color: #edf0f2;
}

@media (max-width: 900px) {
    body.is-admin:not(.is-dashboard-overview) .admin-workspace--with-sidebar .admin-main { padding-inline: 28px; }
    body.is-admin:not(.is-dashboard-overview) .admin-pagebar { margin-inline: -28px; padding-inline: 28px; }
    body.is-admin:not(.is-dashboard-overview) .kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    body.is-admin:not(.is-dashboard-overview) .kpi-card:first-child { padding-left: 20px; }
    body.is-admin:not(.is-dashboard-overview) .kpi-card:nth-child(2) { border-right: 0; }
    body.is-admin:not(.is-dashboard-overview) .kpi-card:nth-child(-n+2) { border-bottom: 1px solid #edf0f2; }
}

@media (max-width: 640px) {
    body.is-admin:not(.is-dashboard-overview) .admin-workspace--with-sidebar .admin-main { padding-inline: 18px; }
    body.is-admin:not(.is-dashboard-overview) .admin-pagebar { margin-inline: -18px; padding: 18px; }
    body.is-admin:not(.is-dashboard-overview) .admin-pagebar-actions { width: 100%; justify-content: flex-start; }
    body.is-admin:not(.is-dashboard-overview) .admin-pagebar-actions .admin-profile-menu { margin-left: auto; }
    body.is-admin:not(.is-dashboard-overview) .admin-main > .hero-banner,
    body.is-admin:not(.is-dashboard-overview) .admin-main > .page-header { padding-bottom: 22px; }
    body.is-admin:not(.is-dashboard-overview) .hero-banner h1,
    body.is-admin:not(.is-dashboard-overview) .page-header h1,
    body.is-admin:not(.is-dashboard-overview) .section-heading h2 { font-size: 24px; }
}

/* Activity uses the same three-zone canvas as the dashboard. */
.tt-page {
    display: grid;
    width: 100%;
    max-width: none;
    margin: 0;
    gap: 0;
    animation: tt-dashboard-in 260ms ease both;
}

.tt-subpage-head {
    display: flex;
    min-height: 104px;
    align-items: end;
    justify-content: space-between;
    gap: 24px;
    padding-bottom: 18px;
    border-bottom: 1px solid #edf0f2;
}

.tt-subpage-head h1 { margin: 2px 0 0; color: #17191d; font-family: var(--font-display); font-size: 28px; font-weight: 650; letter-spacing: -0.04em; line-height: 1.05; }
.tt-subpage-head p { margin: 6px 0 0; color: #858b94; font-size: 12px; }
.tt-page-badge { display: inline-flex; align-items: center; gap: 7px; padding-bottom: 4px; color: #858b94; font-size: 11px; }
.tt-page-badge i { width: 6px; height: 6px; border-radius: 50%; background: #4b9a6a; box-shadow: 0 0 0 3px rgba(75,154,106,.12); }
.tt-activity-analytics { margin-top: 30px; grid-template-columns: minmax(0, 1.72fr) minmax(225px, .68fr) minmax(215px, .62fr); }
.tt-activity-middle { padding: 0 26px; border-right: 1px solid #edf0f2; }
.tt-activity-rail { padding-left: 26px; }
.tt-activity-middle .tt-section-head, .tt-activity-rail .tt-section-head { margin-bottom: 18px; }
.tt-insight-list { display: grid; gap: 10px; }
.tt-insight-item { display: grid; gap: 4px; padding: 13px; border-radius: 8px; background: #f7f8f9; }
.tt-insight-item span, .tt-insight-item small { color: #858b94; font-size: 10px; }
.tt-insight-item strong { color: #25292f; font-size: 17px; font-weight: 650; }
.tt-insight-item small { line-height: 1.4; }
.tt-rail-period { color: #9a9fa7; font-size: 10px; }
.tt-page-list { display: grid; gap: 15px; }
.tt-page-row { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 4px 10px; }
.tt-page-row div:first-child { min-width: 0; }
.tt-page-row strong { overflow: hidden; color: #353a41; font-size: 11px; font-weight: 650; text-overflow: ellipsis; white-space: nowrap; }
.tt-page-row span { display: block; color: #9a9fa7; font-size: 9px; }
.tt-page-track { grid-column: 1 / -1; height: 4px; margin-top: 3px; overflow: hidden; border-radius: 99px; background: #edf0f2; }
.tt-page-track span { display: block; height: 100%; border-radius: inherit; background: var(--primary); }
.tt-detail-strip { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); margin-top: 26px; border-block: 1px solid #edf0f2; }
.tt-detail-item { display: grid; gap: 6px; padding: 15px 20px; border-right: 1px solid #edf0f2; }
.tt-detail-item:first-child { padding-left: 0; }
.tt-detail-item:last-child { border-right: 0; }
.tt-detail-item span { color: #858b94; font-size: 10px; }
.tt-detail-item strong { color: #25292f; font-family: var(--font-display); font-size: 17px; font-weight: 620; }

/* The icon and label are one flex row. Explicit dimensions prevent the old
 * nested grid/min-height rules from visually lifting the icon above its text. */
body.is-admin .admin-sidebar .admin-nav-link { display: flex !important; min-height: 40px; align-items: center !important; gap: 16px; }
body.is-admin .admin-sidebar .admin-nav-link .admin-nav-icon { display: grid !important; width: 34px !important; height: 34px !important; flex: 0 0 34px; align-self: center !important; margin: 0 !important; place-items: center; transform: translateY(0) !important; }
body.is-admin .admin-sidebar .admin-nav-link > div { display: flex !important; height: 34px; min-height: 34px; align-items: center !important; margin: 0; transform: translateY(0) !important; }
body.is-admin .admin-sidebar .admin-nav-link > div > strong { display: block; margin: 0; line-height: 1 !important; }
body.is-admin .admin-sidebar .admin-nav-icon svg { display: block; width: 16px; height: 16px; margin: 0; }

@media (max-width: 1220px) {
    .tt-activity-analytics { grid-template-columns: minmax(0, 1.6fr) minmax(220px, .7fr); }
    .tt-activity-rail { grid-column: 1 / -1; padding: 22px 0 0; border-top: 1px solid #edf0f2; }
    .tt-page-list { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}

@media (max-width: 680px) {
    .tt-subpage-head { align-items: start; flex-direction: column; padding-top: 24px; }
    .tt-activity-analytics { grid-template-columns: 1fr; margin-top: 24px; }
    .tt-performance, .tt-activity-middle { padding: 0 0 24px; border-right: 0; }
    .tt-activity-middle { border-top: 1px solid #edf0f2; padding-top: 24px; }
    .tt-activity-rail { padding-top: 24px; }
    .tt-page-list, .tt-detail-strip { grid-template-columns: 1fr; }
    .tt-detail-item, .tt-detail-item:first-child { padding: 13px 0; border-right: 0; border-bottom: 1px solid #edf0f2; }
    .tt-detail-item:last-child { border-bottom: 0; }
}

/* Insights follows the same editorial data canvas rather than three generic
 * statistic cards. */
.tt-insights-grid { display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(0, 1.15fr) minmax(235px, .72fr); gap: 0; margin-top: 30px; border-bottom: 1px solid #edf0f2; }
.tt-insights-grid > .tt-section { min-width: 0; padding: 0 28px 28px; border-right: 1px solid #edf0f2; }
.tt-insights-grid > .tt-section:first-child { padding-left: 0; }
.tt-insights-grid > .tt-section:last-child { border-right: 0; padding-right: 0; }
.tt-ranking-list { display: grid; gap: 16px; }
.tt-ranking-row { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 5px 14px; }
.tt-ranking-row div:first-child { min-width: 0; }
.tt-ranking-row strong { overflow: hidden; color: #353a41; font-size: 11px; font-weight: 650; text-overflow: ellipsis; white-space: nowrap; }
.tt-ranking-row span { display: block; color: #9a9fa7; font-size: 9px; }
.tt-ranking-track { grid-column: 1 / -1; height: 5px; overflow: hidden; border-radius: 99px; background: #f0f1f2; }
.tt-ranking-track span { display: block; height: 100%; border-radius: inherit; background: var(--primary); }
.tt-ranking-track--lead span { background: #d37b47; }
.tt-insight-pipeline .tt-pipeline-ring { width: min(168px, 82%); margin-top: 28px; }
@media (max-width: 1220px) {
    .tt-insights-grid { grid-template-columns: minmax(0, 1fr) minmax(235px, .7fr); }
    .tt-insights-grid > .tt-section:nth-child(2) { border-right: 0; }
    .tt-insights-grid > .tt-section:last-child { grid-column: 1 / -1; padding: 24px 0 28px; border-top: 1px solid #edf0f2; }
}
@media (max-width: 680px) {
    .tt-insights-grid { grid-template-columns: 1fr; margin-top: 24px; }
    .tt-insights-grid > .tt-section,
    .tt-insights-grid > .tt-section:first-child,
    .tt-insights-grid > .tt-section:last-child { padding: 0 0 24px; border-right: 0; border-bottom: 1px solid #edf0f2; }
    .tt-insights-grid > .tt-section + .tt-section { padding-top: 24px; }
    .tt-insights-grid > .tt-section:last-child { border-bottom: 0; }
}

/* Overview rebuild: a quiet canvas with an asymmetric analytical
 * workspace. The legacy overview markup remains available to other sections,
 * while this surface has its own proportions and interaction language. */
body.is-dashboard-overview .admin-workspace--with-sidebar .admin-main {
    padding: 0 42px 56px;
    background: #fff;
}

.tt-dashboard {
    display: grid;
    width: 100%;
    max-width: none;
    margin: 0;
    gap: 0;
    color: #1b1d21;
    animation: tt-dashboard-in 260ms ease both;
}

@keyframes tt-dashboard-in {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}

.tt-dashboard-intro {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 20px;
    padding: 34px 0 20px;
}

.tt-dashboard-intro h2 {
    margin: 6px 0 0;
    font-family: var(--font-display);
    font-size: 18px;
    font-weight: 620;
    letter-spacing: -0.025em;
}

.tt-dashboard-status {
    display: flex;
    align-items: center;
    gap: 7px;
    color: #7d838c;
    font-size: 11px;
}

.status-dot { width: 6px; height: 6px; border-radius: 50%; }
.status-dot--live { background: #4b9a6a; box-shadow: 0 0 0 3px rgba(75,154,106,.12); }
.status-dot--pending { background: #bf8e26; box-shadow: 0 0 0 3px rgba(191,142,38,.12); }
.tt-status-divider { width: 1px; height: 13px; margin-inline: 4px; background: #e2e5e8; }

.tt-metric-band {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    border-top: 1px solid #edf0f2;
    border-bottom: 1px solid #edf0f2;
}

.tt-metric {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 6px 12px;
    min-height: 88px;
    align-content: center;
    padding: 16px 22px;
    border-right: 1px solid #edf0f2;
}

.tt-metric:first-child { padding-left: 0; }
.tt-metric:last-child { border-right: 0; }
.tt-metric strong { grid-column: 1; color: #17191d; font-family: var(--font-display); font-size: 24px; font-weight: 620; line-height: 1; }
.tt-metric .metric-label { grid-column: 1 / -1; color: #858b94; font-size: 10px; }
.tt-metric-trend { align-self: end; color: #7e858e; font-size: 10px; }
.tt-metric-trend--up, .tt-metric-trend--positive { color: #4f956a; }
.tt-metric-trend--down, .tt-metric-trend--negative { color: #b56555; }

.tt-analytics-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(235px, .72fr) minmax(205px, .58fr);
    gap: 0;
    align-items: stretch;
    margin-top: 30px;
    border-bottom: 1px solid #edf0f2;
}

.tt-section { min-width: 0; }
.tt-performance { padding: 0 28px 26px 0; border-right: 1px solid #edf0f2; }
.tt-pipeline { padding: 0 28px 26px; border-right: 1px solid #edf0f2; }
.tt-snapshot { padding: 0 0 26px 28px; }

.tt-section-head { display: flex; align-items: start; justify-content: space-between; gap: 14px; margin-bottom: 18px; }
.tt-section-head h2 { margin: 4px 0 0; color: #1c1f24; font-family: var(--font-display); font-size: 17px; font-weight: 620; letter-spacing: -0.025em; }
.tt-section-head .eyebrow { color: #9298a0; font-size: 9px; }
.tt-chart-tools { display: grid; justify-items: end; gap: 9px; color: #9298a0; font-size: 10px; }
.tt-chart-tools .dashboard-chart-periods { transform: scale(.9); transform-origin: right top; }
.tt-chart-legend { display: flex; gap: 17px; margin: -3px 0 3px; color: #7d838c; font-size: 10px; }
.tt-chart-legend span { display: inline-flex; align-items: center; gap: 6px; }
.tt-legend-dot { width: 6px; height: 6px; border-radius: 50%; }
.tt-legend-dot--visits { background: #c38d0a; }
.tt-legend-dot--quotes { background: #454c55; }
.tt-legend-dot--messages { background: #d37b47; }
.tt-performance .line-chart-shell { min-height: 222px; padding: 0; border: 0; background: transparent; }
.tt-performance .line-chart { height: 226px; }
.tt-performance .chart-empty { padding-top: 75px; }

.tt-pipeline-ring {
    display: grid;
    width: min(178px, 80%);
    aspect-ratio: 1;
    margin: 22px auto 24px;
    place-items: center;
    border-radius: 50%;
    background: conic-gradient(var(--primary) 0 var(--pipeline-progress), #eef0f1 var(--pipeline-progress) 100%);
    animation: tt-ring-in 500ms 120ms ease both;
}

@keyframes tt-ring-in { from { transform: scale(.86) rotate(-20deg); opacity: .3; } to { transform: scale(1) rotate(0); opacity: 1; } }
.tt-pipeline-ring__inner { display: grid; width: 75%; aspect-ratio: 1; place-content: center; border-radius: 50%; background: #fff; text-align: center; }
.tt-pipeline-ring__inner strong { color: #1b1d21; font-family: var(--font-display); font-size: 22px; font-weight: 650; line-height: 1; }
.tt-pipeline-ring__inner span { margin-top: 7px; color: #9298a0; font-size: 10px; }
.tt-pipeline-breakdown { display: grid; gap: 11px; }
.tt-pipeline-breakdown div { display: flex; justify-content: space-between; gap: 10px; color: #858b94; font-size: 10px; }
.tt-pipeline-breakdown strong { color: #30343a; font-size: 11px; font-weight: 700; }

.tt-snapshot-list { display: grid; gap: 9px; }
.tt-snapshot-item { display: grid; grid-template-columns: 1fr auto; gap: 4px 10px; padding: 14px 13px; border-radius: 8px; }
.tt-snapshot-item--gold { background: #fffbed; }
.tt-snapshot-item--cream { background: #fbf2ec; }
.tt-snapshot-item--gray { background: #f1f4f1; }
.tt-snapshot-item span { color: #7e858e; font-size: 10px; }
.tt-snapshot-item strong { color: #1d2024; font-family: var(--font-display); font-size: 18px; font-weight: 620; line-height: 1; }
.tt-snapshot-item small { grid-column: 1 / -1; color: #969ba2; font-size: 9px; }

.tt-lower-grid { display: grid; grid-template-columns: minmax(0, 1.7fr) minmax(250px, .58fr); gap: 0; margin-top: 30px; }
.tt-invoices { padding-right: 28px; }
.tt-projects { padding-left: 28px; border-left: 1px solid #edf0f2; }
.tt-invoice-table-wrap { overflow-x: auto; }
.tt-invoice-table { width: 100%; min-width: 760px; border-collapse: collapse; }
.tt-invoice-table th, .tt-invoice-table td { padding: 12px 10px; border-bottom: 1px solid #edf0f2; text-align: left; vertical-align: middle; }
.tt-invoice-table th { padding-top: 0; color: #9a9fa7; font-size: 9px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
.tt-invoice-table td { color: #69717b; font-size: 11px; }
.tt-invoice-table td:first-child, .tt-invoice-table th:first-child { padding-left: 0; }
.tt-invoice-table td:last-child, .tt-invoice-table th:last-child { padding-right: 0; text-align: right; }
.tt-invoice-table tbody tr { transition: background 160ms ease; }
.tt-invoice-table tbody tr:hover { background: #fbfcfc; }
.tt-invoice-table td strong, .tt-invoice-table td span { display: block; }
.tt-invoice-table td strong { color: #31353b; font-size: 11px; font-weight: 650; }
.tt-invoice-table td span { margin-top: 2px; color: #9a9fa7; font-size: 10px; }
.tt-invoice-number { color: var(--primary-strong); font-weight: 750; }
.tt-amount { color: #292d33 !important; font-weight: 700; font-variant-numeric: tabular-nums; }
.tt-status-label { display: inline-block !important; width: fit-content; padding: 4px 7px; border-radius: 4px; background: #f1f5ef; color: #528060 !important; font-size: 9px !important; font-weight: 700; }
.tt-row-menu { position: relative; display: inline-block; }
.tt-row-menu summary { color: #9ba1a8; cursor: pointer; font-size: 12px; list-style: none; }
.tt-row-menu summary::-webkit-details-marker { display: none; }
.tt-row-menu > div { position: absolute; top: calc(100% + 4px); right: 0; z-index: 5; display: grid; min-width: 80px; padding: 4px; border: 1px solid #e6e8eb; border-radius: 6px; background: #fff; box-shadow: 0 8px 18px rgba(31,38,48,.1); }
.tt-row-menu a { padding: 6px 8px; color: #69717b; font-size: 10px; text-align: left; }
.tt-row-menu a:hover { background: #f8f9fb; color: var(--primary-strong); }
.tt-empty { display: grid; gap: 5px; padding: 26px 0; color: #838a93; }
.tt-empty strong { color: #353a41; font-size: 12px; }
.tt-empty span { font-size: 11px; }
.tt-empty .panel-head__link { margin-top: 6px; width: fit-content; }
.tt-empty--small { padding: 16px 0; }
.tt-project-list { display: grid; gap: 16px; }
.tt-project { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 4px 12px; color: inherit; }
.tt-project:hover strong:first-child { color: var(--primary-strong); }
.tt-project > div:first-child { min-width: 0; }
.tt-project strong { color: #31353b; font-size: 12px; font-weight: 650; }
.tt-project div span, .tt-project small { display: block; color: #9298a0; font-size: 10px; }
.tt-project-progress { grid-column: 1 / -1; height: 4px; margin-top: 5px; overflow: hidden; border-radius: 99px; background: #edf0f1; }
.tt-project-progress span { display: block; height: 100%; border-radius: inherit; background: var(--primary); }
.tt-project small { grid-column: 1 / -1; }
.tt-rail-divider { height: 1px; margin: 24px 0 18px; background: #edf0f2; }
.tt-rail-utility { display: grid; gap: 9px; }
.tt-rail-utility a { display: flex; justify-content: space-between; color: #69717b; font-size: 11px; }
.tt-rail-utility a:hover { color: var(--primary-strong); }
.tt-rail-utility a span { color: #a0a6ad; }

.sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }

@media (max-width: 1220px) {
    body.is-dashboard-overview .admin-workspace--with-sidebar .admin-main { padding-inline: 28px; }
    .tt-analytics-grid { grid-template-columns: minmax(0, 1.55fr) minmax(220px, .7fr); }
    .tt-snapshot { grid-column: 1 / -1; padding: 22px 0 0; border-top: 1px solid #edf0f2; border-right: 0; }
    .tt-snapshot-list { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}

@media (max-width: 900px) {
    .tt-lower-grid { grid-template-columns: 1fr; }
    .tt-invoices { padding-right: 0; }
    .tt-projects { padding: 24px 0 0; border-top: 1px solid #edf0f2; border-left: 0; }
}

@media (max-width: 680px) {
    body.is-dashboard-overview .admin-workspace--with-sidebar .admin-main { padding: 0 18px 36px; }
    .tt-dashboard-intro { align-items: flex-start; flex-direction: column; padding-top: 26px; }
    .tt-metric-band { grid-template-columns: repeat(2, minmax(150px, 1fr)); }
    .tt-metric, .tt-metric:first-child { padding: 15px 12px; }
    .tt-metric:nth-child(2) { border-right: 0; }
    .tt-metric:nth-child(-n+2) { border-bottom: 1px solid #edf0f2; }
    .tt-analytics-grid { grid-template-columns: 1fr; margin-top: 24px; }
    .tt-performance, .tt-pipeline { padding: 0 0 24px; border-right: 0; }
    .tt-pipeline { border-top: 1px solid #edf0f2; padding-top: 24px; }
    .tt-snapshot { padding-top: 24px; }
    .tt-snapshot-list { grid-template-columns: 1fr; }
    .tt-chart-tools { justify-items: start; }
}

@media (prefers-reduced-motion: reduce) {
    .tt-dashboard, .tt-pipeline-ring { animation: none; }
}

@media (max-width: 1240px) {
    .kpi-grid,
    .insight-grid,
    .chart-summary-grid,
    .template-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .analytics-grid,
    .dashboard-grid,
    .project-analytics-grid,
    .project-detail-grid,
    .dashboard-command {
        grid-template-columns: 1fr;
    }

    /* Side panels sit beside each other once they stop being a rail. */
    .sticky-stack {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        align-items: start;
    }

    .sticky-stack .panel:first-child {
        position: static;
    }
}

@media (max-width: 1100px) {
    .admin-workspace--with-sidebar {
        grid-template-columns: 1fr;
    }

    .admin-workspace--with-sidebar .admin-main {
        padding: 0 18px 28px;
    }

    .admin-pagebar {
        margin: 0 -18px 4px;
        padding-inline: 18px;
    }

    .admin-mobile-nav-button {
        display: inline-grid;
    }

    .admin-sidebar-collapse {
        display: none;
    }

    .admin-sidebar {
        position: fixed;
        left: 0;
        top: 0;
        transform: translateX(-102%);
        width: min(86vw, 300px);
        border-right: 1px solid var(--line);
        box-shadow: var(--shadow);
        transition: transform 0.2s ease;
    }

    body.is-mobile-nav-open .admin-sidebar {
        transform: translateX(0);
    }

    .admin-sidebar-overlay {
        position: fixed;
        inset: 0;
        z-index: 35;
        display: block;
        background: rgba(17, 24, 39, 0.36);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease;
    }

    body.is-mobile-nav-open .admin-sidebar-overlay {
        opacity: 1;
        pointer-events: auto;
    }
}

@media (max-width: 760px) {
    .project-index-upload-form {
        grid-template-columns: 1fr;
    }

    .project-index-upload-form .field-full,
    .project-index-upload-form__actions {
        grid-column: auto;
    }

    .project-index-upload-form__actions {
        align-items: stretch;
        flex-direction: column;
    }

    .admin-workspace--with-sidebar .admin-main {
        gap: 16px;
        padding: 0 14px 24px;
    }

    .admin-pagebar {
        margin: 0 -14px 2px;
        padding: 10px 14px;
    }

    .admin-pagebar-actions {
        gap: 8px;
    }

    .admin-date-pill,
    .admin-profile-copy {
        display: none;
    }

    .admin-profile-menu summary {
        padding-right: 4px;
    }

    .dashboard-command-main {
        padding: 18px;
    }

    .dashboard-command-main h2 {
        font-size: 24px;
    }

    .section-heading,
    .wizard-actions,
    .line-items-editor-head,
    .line-items-total {
        align-items: stretch;
        flex-direction: column;
    }

    .project-status-chart-wrap {
        grid-template-columns: minmax(150px, 0.75fr) minmax(0, 1fr);
    }

    .section-heading {
        display: grid;
    }

    .kpi-grid,
    .insight-grid,
    .chart-summary-grid,
    .dashboard-signal-strip,
    .template-grid,
    .wizard-pane-grid,
    .form-grid,
    .review-grid,
    .line-items-currency-grid,
    .sticky-stack,
    .stat-list--split {
        grid-template-columns: 1fr;
    }

    .dashboard-signal-strip {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dash-topline {
        align-items: stretch;
        flex-direction: column;
    }

    .dash-topline__actions .ghost-button {
        flex: 1;
    }

    .wizard-progress {
        grid-template-columns: repeat(4, minmax(172px, 1fr));
    }

    .line-item-row {
        grid-template-columns: 1fr;
        align-items: stretch;
    }

    .line-item-index {
        align-self: start;
    }

    .mini-chart {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .project-status-chart-wrap {
        grid-template-columns: 1fr;
    }

    .project-status-chart {
        width: min(180px, 60%);
    }

    .panel-padded {
        padding: 15px;
    }
}

@media (max-width: 520px) {
    .admin-pagebar h1 {
        font-size: 18px;
    }

    .admin-pagebar .eyebrow {
        display: none;
    }

    .admin-pagebar-actions > .button {
        max-width: 128px;
        padding-inline: 10px;
        white-space: nowrap;
    }

    .dashboard-command-actions .button,
    .dashboard-command-actions .ghost-button,
    .hero-actions .button,
    .hero-actions .ghost-button {
        width: 100%;
    }

    .quote-table {
        min-width: 760px;
    }
}

/* Auth redesign */
body.is-auth {
    min-height: 100dvh;
    overflow-x: hidden;
    background:
        linear-gradient(90deg, rgba(184, 134, 11, 0.07) 1px, transparent 1px),
        linear-gradient(180deg, rgba(17, 17, 17, 0.05) 1px, transparent 1px),
        linear-gradient(135deg, #fffdf8 0%, #fff8e8 52%, #fffaf0 100%);
    background-size: 42px 42px, 42px 42px, auto;
}

body.is-auth .admin-shell--auth {
    width: 100%;
    min-height: 100dvh;
    max-width: none;
    padding: 32px;
    place-items: center;
}

body.is-auth .admin-workspace,
body.is-auth .admin-main {
    width: 100%;
}

body.is-auth .admin-workspace {
    max-width: 1160px;
}

body.is-auth .admin-main {
    gap: 0;
}

.auth-grid {
    width: 100%;
    min-height: auto;
    display: grid;
    grid-template-columns: minmax(0, 1.06fr) minmax(340px, 0.74fr);
    gap: 14px;
    align-items: stretch;
}

.auth-brand-panel,
.auth-card {
    min-width: 0;
    min-height: 640px;
    border: 1px solid rgba(15, 23, 42, 0.1);
    border-radius: var(--radius);
    box-shadow: 0 24px 70px rgba(15, 23, 42, 0.11);
}

.auth-brand-panel {
    position: relative;
    display: grid;
    grid-template-rows: auto 1fr auto auto;
    gap: 26px;
    padding: 40px;
    overflow: hidden;
    background:
        linear-gradient(140deg, rgba(255, 255, 255, 0.96), rgba(255, 250, 240, 0.92)),
        #ffffff;
}

.auth-brand-panel::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        linear-gradient(135deg, rgba(184, 134, 11, 0.12), transparent 42%),
        repeating-linear-gradient(135deg, rgba(17, 17, 17, 0.07) 0 1px, transparent 1px 18px);
    opacity: 0.78;
    pointer-events: none;
}

.auth-brand-panel::after {
    content: "";
    position: absolute;
    right: -72px;
    bottom: -96px;
    width: 276px;
    height: 276px;
    border: 1px solid rgba(184, 134, 11, 0.2);
    border-radius: 24px;
    transform: rotate(16deg);
    pointer-events: none;
}

.auth-brand-panel > * {
    position: relative;
    z-index: 1;
}

.auth-brand-copy,
.auth-hero-copy,
.auth-hero-copy p,
.auth-checklist span,
.auth-meta-item,
.auth-card-head,
.auth-form {
    min-width: 0;
}

.auth-brand-lockup {
    display: flex;
    align-items: center;
    gap: 14px;
}

.auth-brand-lockup .admin-brand-mark {
    width: 50px;
    height: 50px;
    border-radius: var(--radius);
    background: linear-gradient(135deg, #111827, #b8860b);
    border-color: rgba(255, 255, 255, 0.34);
    color: #ffffff;
    box-shadow: 0 14px 30px rgba(15, 23, 42, 0.16);
}

.auth-brand-copy strong {
    color: var(--text);
    font-family: var(--font-display);
    font-size: 18px;
    font-weight: 700;
    line-height: 1.15;
}

.auth-brand-copy span {
    margin-top: 5px;
    color: var(--primary);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.14em;
}

.auth-hero-copy {
    align-self: center;
    max-width: 620px;
}

.auth-hero-copy .eyebrow,
.auth-card-head .eyebrow {
    color: var(--primary);
}

.auth-hero-copy h1 {
    max-width: 620px;
    margin: 0;
    color: var(--text);
    font-family: var(--font-display);
    font-size: 50px;
    font-weight: 800;
    line-height: 1.04;
}

.auth-hero-copy p {
    max-width: 520px;
    margin: 20px 0 0;
    color: var(--muted-strong);
    font-size: 16px;
    line-height: 1.7;
}

.auth-checklist {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 8px;
}

.auth-checklist span {
    display: inline-flex;
    min-width: 0;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    border: 1px solid rgba(184, 134, 11, 0.14);
    border-radius: var(--radius);
    background: rgba(255, 255, 255, 0.7);
    color: var(--muted-strong);
    font-size: 12px;
    font-weight: 700;
    line-height: 1.35;
}

.auth-checklist span::before {
    content: "";
    width: 7px;
    height: 7px;
    flex: 0 0 auto;
    border-radius: 2px;
    background: var(--primary);
}

.auth-meta-strip {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0;
    overflow: hidden;
    border: 1px solid rgba(15, 23, 42, 0.1);
    border-radius: var(--radius);
    background: rgba(255, 255, 255, 0.76);
}

.auth-meta-item {
    min-width: 0;
    padding: 16px;
}

.auth-meta-item + .auth-meta-item {
    border-left: 1px solid rgba(15, 23, 42, 0.08);
}

.auth-meta-item strong {
    display: block;
    color: var(--text);
    font-family: var(--font-display);
    font-size: 26px;
    font-weight: 800;
    line-height: 1;
}

.auth-meta-item span {
    display: block;
    margin-top: 8px;
    color: var(--muted);
    font-size: 12px;
    font-weight: 700;
    line-height: 1.35;
}

.auth-card {
    position: relative;
    display: grid;
    align-content: center;
    gap: 24px;
    padding: 40px;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.94);
}

.auth-card::before {
    content: "";
    position: absolute;
    inset: 0 0 auto;
    height: 4px;
    background: linear-gradient(90deg, var(--primary), var(--accent), #d4af37);
}

.auth-card-head {
    margin: 0;
}

.auth-card-head h2 {
    margin: 0;
    color: var(--text);
    font-family: var(--font-display);
    font-size: 34px;
    font-weight: 800;
    line-height: 1.12;
}

.auth-card-head p {
    max-width: 360px;
    margin: 12px 0 0;
    color: var(--muted);
    font-size: 14px;
    line-height: 1.65;
}

.auth-form {
    display: grid;
    gap: 16px;
}

.auth-card .field,
.auth-card .field-full {
    gap: 8px;
}

.auth-card .field label {
    color: var(--text);
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0;
    text-transform: none;
}

.auth-card .field input {
    min-height: 54px;
    padding: 0 14px;
    border: 1px solid rgba(184, 134, 11, 0.26);
    border-radius: var(--radius);
    background: #ffffff;
    color: var(--text);
    box-shadow: 0 1px 0 rgba(15, 23, 42, 0.03);
}

.auth-card .field input::placeholder {
    color: #9a8b73;
}

.auth-card .field input:focus {
    border-color: rgba(184, 134, 11, 0.58);
    box-shadow: 0 0 0 4px rgba(184, 134, 11, 0.12);
}

.auth-form .button {
    width: 100%;
    min-height: 54px;
    border-color: transparent;
    border-radius: var(--radius);
    background: linear-gradient(135deg, #b8860b, #111111);
    color: #ffffff;
    font-weight: 800;
    box-shadow: 0 14px 28px rgba(184, 134, 11, 0.22);
}

.auth-form .button:hover {
    background: linear-gradient(135deg, #8f6508, #2b2b2b);
}

.auth-support-note {
    margin: 0;
    padding-top: 18px;
    border-top: 1px solid var(--line-soft);
    color: var(--muted);
    font-size: 12px;
    line-height: 1.6;
}

.auth-card .alert {
    margin: 0;
}

@media (max-width: 1040px) {
    body.is-auth .admin-shell--auth {
        padding: 24px;
        place-items: start center;
    }

    .auth-grid {
        grid-template-columns: 1fr;
    }

    .auth-brand-panel,
    .auth-card {
        min-height: auto;
    }

    .auth-brand-panel {
        gap: 22px;
    }

    .auth-hero-copy {
        align-self: start;
    }

    .auth-hero-copy h1 {
        font-size: 40px;
    }

    .auth-card {
        align-content: start;
    }
}

@media (max-width: 680px) {
    body.is-auth {
        background-size: 34px 34px, 34px 34px, auto;
    }

    body.is-auth .admin-shell--auth {
        padding: 16px;
        overflow-x: hidden;
    }

    body.is-auth .admin-workspace,
    body.is-auth .admin-main,
    .auth-grid,
    .auth-brand-panel,
    .auth-card {
        max-width: 100%;
    }

    .auth-grid {
        gap: 12px;
    }

    .auth-card {
        order: -1;
    }

    .auth-brand-panel,
    .auth-card {
        padding: 22px;
    }

    .auth-brand-panel {
        grid-template-rows: auto auto auto auto;
    }

    .auth-hero-copy h1 {
        font-size: 32px;
    }

    .auth-hero-copy p {
        max-width: 100%;
        margin-top: 14px;
        font-size: 14px;
        overflow-wrap: break-word;
    }

    .auth-card-head h2 {
        font-size: 28px;
    }

    .auth-checklist {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 520px) {
    body.is-auth .admin-shell--auth {
        padding: 10px;
    }

    .auth-grid {
        width: min(100%, 370px);
        margin-inline: 0 auto;
    }

    .auth-brand-panel,
    .auth-card {
        width: 100%;
        padding: 18px;
    }

    .auth-brand-lockup {
        align-items: flex-start;
        gap: 10px;
    }

    .auth-brand-lockup .admin-brand-mark {
        width: 42px;
        height: 42px;
        font-size: 12px;
    }

    .auth-brand-copy strong {
        font-size: 16px;
    }

    .auth-hero-copy h1 {
        font-size: 28px;
    }

    .auth-meta-item {
        display: block;
        padding: 12px;
    }

    .auth-meta-item + .auth-meta-item {
        border-top: 0;
        border-left: 1px solid rgba(15, 23, 42, 0.08);
    }

    .auth-meta-item strong {
        font-size: 22px;
    }

    .auth-meta-item span {
        margin-top: 6px;
        font-size: 11px;
        text-align: left;
        overflow-wrap: break-word;
    }

    .auth-card .field input,
    .auth-form .button {
        min-height: 50px;
    }
}

/* The auth surface uses the same restrained canvas and card language as the
 * authenticated admin workspace. Keep identity and credentials only. */
body.is-auth {
    min-height: 100dvh;
    overflow-x: hidden;
    background:
        radial-gradient(ellipse 32% 48% at 20% 20%, rgba(184, 134, 11, .09), transparent 70%),
        radial-gradient(ellipse 28% 42% at 84% 76%, rgba(78, 89, 108, .1), transparent 70%),
        #f7f7fa;
}

body.is-auth::before,
body.is-auth::after {
    position: fixed;
    z-index: 0;
    display: block;
    border-radius: 50%;
    content: "";
    pointer-events: none;
}

body.is-auth::before {
    top: -170px;
    left: 50%;
    width: 460px;
    height: 300px;
    background: rgba(184, 134, 11, .06);
    filter: blur(1px);
    transform: translateX(-50%) rotate(-17deg);
}

body.is-auth::after {
    top: 8%;
    left: 12%;
    width: 12px;
    height: 12px;
    background: rgba(184, 134, 11, .28);
    box-shadow:
        760px 12px 0 rgba(184, 134, 11, .42),
        980px 360px 0 rgba(78, 89, 108, .18),
        190px 500px 0 rgba(184, 134, 11, .22);
}

body.is-auth .admin-shell--auth {
    width: 100%;
    max-width: none;
    min-height: 100dvh;
    padding: 24px;
    place-items: start center;
}

body.is-auth .admin-workspace {
    width: min(100%, 1040px);
    max-width: 1040px;
    position: relative;
    z-index: 1;
}

body.is-auth .admin-main {
    width: 100%;
    gap: 0;
}

body.is-auth .admin-alert-stack {
    width: 100%;
    margin-bottom: 12px;
}

body.is-auth .auth-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    gap: 0;
    width: min(100%, 820px);
    min-height: 500px;
    margin: clamp(34px, 9vh, 78px) auto 0;
    overflow: hidden;
    border: 1px solid #e1e4e8;
    border-radius: 20px;
    box-shadow: 0 24px 70px rgba(31, 38, 48, .12);
}

body.is-auth .auth-brand-panel {
    order: 2;
    position: relative;
    display: grid;
    min-height: 500px;
    align-content: space-between;
    gap: 32px;
    padding: 36px;
    border: 0;
    border-radius: 0;
    overflow: hidden;
    border-left: 1px solid rgba(255, 255, 255, .08);
    background:
        radial-gradient(circle at 75% 26%, rgba(184, 134, 11, .22), transparent 26%),
        linear-gradient(145deg, #2b3038, #17191d 70%);
}

body.is-auth .auth-brand-panel::before {
    background:
        linear-gradient(135deg, rgba(184, 134, 11, .28), transparent 42%),
        repeating-linear-gradient(135deg, rgba(255, 255, 255, .06) 0 1px, transparent 1px 22px);
    opacity: 1;
}

body.is-auth .auth-brand-panel::after {
    right: -82px;
    bottom: -105px;
    width: 300px;
    height: 300px;
    border-color: rgba(212, 175, 55, .28);
}

body.is-auth .auth-brand-panel .auth-brand-lockup {
    padding-bottom: 0;
    border-bottom: 0;
}

body.is-auth .auth-brand-panel .auth-brand-lockup .admin-brand-mark {
    width: 42px;
    height: 42px;
    border-radius: 9px;
    background: linear-gradient(145deg, #d5a72c, #8f6508);
}

body.is-auth .auth-brand-panel .auth-brand-copy strong {
    color: #fff;
    font-size: 16px;
}

body.is-auth .auth-brand-panel .auth-brand-copy span {
    color: #d5a72c;
}

body.is-auth .auth-visual {
    position: relative;
    min-height: 300px;
    margin: 0 -4px -4px;
    border: 1px solid rgba(255, 255, 255, .17);
    border-radius: 16px;
    background: linear-gradient(145deg, rgba(255, 255, 255, .1), rgba(255, 255, 255, .02));
    overflow: hidden;
}

body.is-auth .auth-visual::before {
    content: "";
    position: absolute;
    inset: 24px;
    border: 1px solid rgba(212, 175, 55, .24);
    border-radius: 12px;
}

body.is-auth .auth-visual-orbit {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 230px;
    height: 230px;
    border: 1px solid rgba(212, 175, 55, .42);
    border-radius: 50%;
    transform: translate(-50%, -50%) rotate(-24deg) scaleX(1.55);
}

body.is-auth .auth-visual-orbit::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #d5a72c;
    box-shadow: 0 0 0 7px rgba(212, 175, 55, .12), 0 0 34px rgba(212, 175, 55, .7);
    transform: translate(-50%, -50%);
}

body.is-auth .auth-visual-bars {
    position: absolute;
    right: 31px;
    bottom: 31px;
    display: flex;
    align-items: end;
    gap: 7px;
    height: 92px;
}

body.is-auth .auth-visual-bars i {
    display: block;
    width: 13px;
    height: 42px;
    border-radius: 4px 4px 0 0;
    background: #d5a72c;
    opacity: .85;
}

body.is-auth .auth-visual-bars i:nth-child(2) { height: 62px; opacity: .62; }
body.is-auth .auth-visual-bars i:nth-child(3) { height: 50px; opacity: .72; }
body.is-auth .auth-visual-bars i:nth-child(4) { height: 78px; opacity: .48; }
body.is-auth .auth-visual-bars i:nth-child(5) { height: 66px; opacity: .58; }

body.is-auth .auth-visual-window {
    position: absolute;
    top: 50%;
    left: 50%;
    width: calc(100% - 62px);
    height: 194px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, .25);
    border-radius: 11px;
    background: rgba(248, 250, 252, .94);
    box-shadow: 0 22px 32px rgba(0, 0, 0, .2);
    transform: translate(-50%, -50%) rotate(-3deg);
}

body.is-auth .auth-visual-window__top {
    display: flex;
    align-items: center;
    gap: 5px;
    height: 28px;
    padding: 0 11px;
    border-bottom: 1px solid #e4e8ec;
    background: #fff;
}

body.is-auth .auth-visual-window__top i {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #c9ced5;
}

body.is-auth .auth-visual-window__top i:first-child { background: #d5a72c; }

body.is-auth .auth-visual-window__body {
    display: grid;
    grid-template-columns: 42px minmax(0, 1fr);
    height: calc(100% - 28px);
}

body.is-auth .auth-visual-window__rail {
    border-right: 1px solid #e4e8ec;
    background:
        linear-gradient(#d5a72c 0 0) 13px 18px / 16px 5px no-repeat,
        repeating-linear-gradient(180deg, #d9dee3 0 4px, transparent 4px 15px) 13px 43px / 16px 4px no-repeat,
        #f1f3f5;
}

body.is-auth .auth-visual-window__content {
    position: relative;
    padding: 24px 22px 18px;
    background: linear-gradient(145deg, #fff, #f3f5f7);
}

body.is-auth .auth-visual-window__line {
    width: 42%;
    height: 7px;
    border-radius: 999px;
    background: #24282e;
    opacity: .82;
}

body.is-auth .auth-visual-window__content::before,
body.is-auth .auth-visual-window__content::after {
    content: "";
    position: absolute;
    left: 22px;
    right: 22px;
    height: 1px;
    background: #e5e8eb;
}

body.is-auth .auth-visual-window__content::before { top: 57px; }
body.is-auth .auth-visual-window__content::after { top: 91px; }

body.is-auth .auth-visual-window__content .auth-visual-bars {
    position: absolute;
    right: 22px;
    bottom: 18px;
    display: flex;
    align-items: end;
    gap: 6px;
    height: 74px;
}

body.is-auth .auth-visual-window__content .auth-visual-bars i {
    display: block;
    width: 12px;
    height: 34px;
    border-radius: 3px 3px 0 0;
    background: #1f2328;
    opacity: .86;
}

body.is-auth .auth-visual-window__content .auth-visual-bars i:nth-child(2) { height: 52px; background: #b8860b; }
body.is-auth .auth-visual-window__content .auth-visual-bars i:nth-child(3) { height: 43px; opacity: .58; }
body.is-auth .auth-visual-window__content .auth-visual-bars i:nth-child(4) { height: 66px; background: #b8860b; opacity: .74; }
body.is-auth .auth-visual-window__content .auth-visual-bars i:nth-child(5) { height: 55px; opacity: .66; }

body.is-auth .auth-visual-float {
    position: absolute;
    display: block;
    width: 13px;
    height: 13px;
    border: 1px solid rgba(255, 255, 255, .5);
    border-radius: 50%;
    background: #d5a72c;
    box-shadow: 0 0 0 6px rgba(213, 167, 44, .1);
}

body.is-auth .auth-visual-float--one { top: 29px; right: 31px; }
body.is-auth .auth-visual-float--two { bottom: 28px; left: 28px; width: 9px; height: 9px; opacity: .6; }
}

body.is-auth .auth-card {
    order: 1;
    width: auto;
    min-height: 500px;
    margin: 0;
    align-content: start;
    padding: 56px 50px;
    border: 0;
    border-right: 1px solid #e6e8eb;
    border-left: 0;
    border-radius: 0;
    background: #fff;
    box-shadow: none;
}

body.is-auth .auth-card::before {
    height: 3px;
    background: var(--primary);
}

body.is-auth .auth-brand-lockup {
    gap: 11px;
    padding-bottom: 20px;
    border-bottom: 1px solid #edf0f2;
}

body.is-auth .auth-brand-lockup .admin-brand-mark {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #1c1e22;
    border: 0;
    box-shadow: none;
}

body.is-auth .auth-brand-copy strong {
    color: #17191d;
    font-size: 14px;
    font-weight: 700;
}

body.is-auth .auth-brand-copy span {
    margin-top: 3px;
    color: #858b94;
    font-size: 9px;
    letter-spacing: .1em;
}

body.is-auth .auth-card-head {
    margin: 0;
}

body.is-auth .auth-card-head h1 {
    margin: 0;
    color: #17191d;
    font-family: var(--font-display);
    font-size: 30px;
    font-weight: 600;
    letter-spacing: -.035em;
    line-height: 1.15;
}

body.is-auth .auth-card-head p {
    max-width: 280px;
    margin: 9px 0 0;
    color: #858b94;
    font-size: 12px;
    line-height: 1.5;
}

body.is-auth .auth-form {
    gap: 14px;
}

body.is-auth .auth-card .field,
body.is-auth .auth-card .field-full {
    gap: 6px;
}

body.is-auth .auth-card .field label {
    color: #17191d;
    font-size: 11px;
    font-weight: 650;
}

body.is-auth .auth-card .field input {
    min-height: 44px;
    padding: 0 12px;
    border: 1px solid #dfe3e7;
    border-radius: 8px;
    background: #fff;
    box-shadow: none;
}

body.is-auth .auth-card .field input:focus {
    border-color: rgba(184, 134, 11, .55);
    box-shadow: 0 0 0 3px rgba(184, 134, 11, .1);
}

body.is-auth .auth-form .button {
    min-height: 44px;
    border: 0;
    border-radius: 8px;
    background: #1c1e22;
    box-shadow: none;
    font-size: 12px;
}

body.is-auth .auth-form .button:hover {
    background: #34373c;
}

body.is-auth .auth-card .alert {
    border-radius: 8px;
    font-size: 11px;
}

@media (max-width: 900px) {
    body.is-auth .auth-grid {
        grid-template-columns: minmax(0, 1fr) minmax(0, 380px);
        margin-top: 32px;
    }

    body.is-auth .auth-brand-panel,
    body.is-auth .auth-card {
        min-height: 460px;
    }

    body.is-auth .auth-brand-panel { padding: 30px; }
    body.is-auth .auth-card { padding: 42px 30px; }
    body.is-auth .auth-visual { min-height: 250px; }
}

@media (max-width: 680px) {
    body.is-auth .auth-grid {
        grid-template-columns: 1fr;
        margin-top: 20px;
    }

    body.is-auth .auth-card {
        order: -1;
        min-height: 0;
        padding: 28px 22px;
        border-right: 0;
        border-left: 0;
        border-bottom: 1px solid #e6e8eb;
    }

    body.is-auth .auth-brand-panel {
        min-height: 260px;
        grid-template-rows: auto 1fr;
        padding: 26px 22px;
    }

    body.is-auth .auth-visual {
        min-height: 130px;
        margin: 0;
    }

    body.is-auth .auth-visual-orbit { width: 120px; height: 120px; }
    body.is-auth .auth-visual-bars { right: 20px; bottom: 20px; transform: scale(.72); transform-origin: bottom right; }
}

@media (max-width: 520px) {
    body.is-auth .admin-shell--auth { padding: 14px; }
    body.is-auth .auth-grid { margin-top: 24px; }
    body.is-auth .auth-card { min-height: 0; padding: 28px 22px; }
}

/* Keep page-level overflow out of the viewport; wide data regions scroll locally. */
body.is-admin {
    overflow-x: hidden;
    overflow-x: clip;
}

/* The admin loads 400, 500, 600, 700 and 800 only; avoid synthetic heavy faces. */
body.is-admin .admin-sidebar .admin-brand-copy strong,
body.is-admin .admin-sidebar .admin-nav-link strong,
body.is-admin .admin-main strong,
body.is-admin .admin-main b {
    font-weight: 600;
}

body.is-admin .admin-main h1,
body.is-admin .admin-main h2,
body.is-admin .admin-main h3,
body.is-admin .admin-pagebar h1,
body.is-admin .admin-sidebar .admin-sidebar-meta strong {
    font-family: var(--font-sans);
    font-weight: 700;
}

body.is-admin .admin-sidebar .admin-nav-label,
body.is-admin .admin-sidebar .admin-nav-link span:not(.admin-nav-icon),
body.is-admin .admin-main .eyebrow,
body.is-admin .admin-main .metric-label,
body.is-admin .admin-main label,
body.is-admin .admin-main .quote-table th,
body.is-admin .admin-main .admin-pill,
body.is-admin .admin-main .admin-date-pill {
    font-weight: 600;
}

body.is-admin .admin-sidebar .admin-nav-link span:not(.admin-nav-icon) {
    font-weight: 500;
}

body.is-admin .button,
body.is-admin button.button,
body.is-admin .ghost-button,
body.is-admin button.ghost-button {
    font-weight: 600;
}

/* Prevent the page header and editor grids from widening the document. */
body.is-admin .admin-pagebar,
body.is-admin .admin-pagebar-title,
body.is-admin .admin-pagebar-title > div,
body.is-admin .admin-pagebar-actions,
body.is-admin .admin-main > *,
body.is-admin .admin-main .dashboard-grid,
body.is-admin .admin-main .dashboard-grid > *,
body.is-admin .admin-main .hero-banner > *,
body.is-admin .admin-main .panel,
body.is-admin .admin-main .form-grid > *,
body.is-admin .admin-main .template-grid > *,
body.is-admin .admin-main .line-items-editor {
    min-width: 0;
}

body.is-admin .admin-pagebar-title {
    flex: 1 1 auto;
}

body.is-admin .admin-pagebar-actions {
    flex: 0 1 auto;
    flex-wrap: nowrap;
    justify-content: flex-end;
}

body.is-admin .admin-profile-copy span,
body.is-admin .admin-main .hero-banner h1,
body.is-admin .admin-main .hero-banner p,
body.is-admin .admin-main .panel-head p,
body.is-admin .admin-main .callout-card strong,
body.is-admin .admin-main .stack-list strong,
body.is-admin .admin-main .meta-item strong {
    overflow-wrap: anywhere;
}

/*
 * Final admin surface pass: the reference uses a quiet canvas, a slim rail,
 * and one clear working area. These tokens intentionally stay local to the
 * authenticated shell so invoice documents and the login surface keep their
 * existing treatment.
 */
body.is-admin {
    --bg: #f4f5f7;
    --surface: #ffffff;
    --surface-soft: #f8f9fb;
    --panel: #ffffff;
    --panel-soft: #fbf7ed;
    --text: #17191d;
    --muted: #747a84;
    --muted-strong: #343942;
    --line: #e5e7eb;
    --line-soft: #edf0f2;
    --primary: #c38d0a;
    --primary-strong: #946a05;
    --primary-soft: #fff7df;
    --accent: #17191d;
    --accent-soft: #f0f1f3;
    --traffic: #262a31;
    --quote: #c38d0a;
    --lead: #d36b2e;
    --pipeline: #59616c;
    --shadow: 0 16px 36px rgba(31, 38, 48, 0.08);
    --shadow-soft: 0 1px 2px rgba(31, 38, 48, 0.03);
    --radius: 10px;
    --sidebar-width: 248px;
    --sidebar-collapsed-width: 72px;
    background: var(--bg);
}

body.is-admin .admin-workspace--with-sidebar {
    grid-template-columns: var(--sidebar-width) minmax(0, 1fr);
    background: var(--surface);
}

body.is-admin .admin-sidebar {
    background: #f0f2f5;
    border-right-color: #e1e4e8;
}

body.is-admin .admin-sidebar-inner {
    gap: 10px;
    padding: 18px 14px;
}

body.is-admin .admin-sidebar-brand {
    min-height: 46px;
    padding: 5px 6px;
}

body.is-admin .admin-sidebar-brand:hover {
    background: rgba(255, 255, 255, 0.72);
}

body.is-admin .admin-brand-mark {
    width: 34px;
    height: 34px;
    flex-basis: 34px;
    border: 0;
    border-radius: 10px;
    background: var(--primary);
}

body.is-admin .admin-sidebar .admin-brand-copy strong {
    color: #25282e;
    font-size: 12px;
}

body.is-admin .admin-sidebar .admin-brand-copy span {
    color: #8b919a;
    font-size: 10px;
    letter-spacing: 0.08em;
}

body.is-admin .admin-sidebar-collapse,
body.is-admin .admin-sidebar-collapse:hover {
    width: 30px;
    height: 30px;
    flex-basis: 30px;
    border: 0;
    background: transparent;
    color: #7d838c;
}

body.is-admin .admin-nav {
    gap: 2px;
    margin-top: 8px;
}

body.is-admin .admin-nav-label {
    margin: 14px 9px 6px;
    color: #9a9fa7;
    font-size: 9px;
    letter-spacing: 0.14em;
}

body.is-admin .admin-nav-link {
    grid-template-columns: 30px minmax(0, 1fr);
    gap: 9px;
    min-height: 40px;
    padding: 5px 8px;
    border: 0;
    border-radius: 8px;
    color: #69717b;
}

body.is-admin .admin-nav-link:hover,
body.is-admin .admin-nav-link.active {
    border: 0;
    background: rgba(255, 255, 255, 0.72);
    color: var(--primary-strong);
}

body.is-admin .admin-nav-link.active::before {
    left: -14px;
    top: 9px;
    bottom: 9px;
    width: 2px;
    background: var(--primary);
}

body.is-admin .admin-nav-icon {
    width: 30px;
    height: 30px;
    border-radius: 8px;
}

body.is-admin .admin-nav-icon svg {
    width: 16px;
    height: 16px;
    stroke-width: 1.7;
}

body.is-admin .admin-nav-link strong {
    color: inherit;
    font-size: 12px;
    font-weight: 600;
}

body.is-admin .admin-nav-link span:not(.admin-nav-icon) {
    display: none;
}

body.is-admin .admin-sidebar-meta {
    display: none;
}

body.is-admin .admin-workspace--with-sidebar .admin-main {
    padding: 0 34px 48px;
    background: var(--surface);
}

body.is-admin .admin-pagebar {
    min-height: 68px;
    margin: 0 -34px 22px;
    padding: 12px 34px;
    border-bottom-color: #e5e7eb;
    background: rgba(255, 255, 255, 0.94);
    box-shadow: 0 1px 0 rgba(31, 38, 48, 0.02);
}

body.is-admin .admin-pagebar-title {
    gap: 10px;
}

body.is-admin .admin-pagebar .eyebrow {
    color: #9a9fa7;
    font-size: 9px;
    letter-spacing: 0.12em;
}

body.is-admin .admin-pagebar h1 {
    color: #17191d;
    font-size: 20px;
    letter-spacing: -0.02em;
}

body.is-admin .admin-pagebar-actions > .button {
    min-height: 34px;
    padding-inline: 14px;
    border: 0;
    border-radius: 5px;
    background: #1c1e22;
    color: #fff;
    box-shadow: none;
    font-size: 11px;
    font-weight: 600;
}

body.is-admin .admin-pagebar-actions > .button:hover {
    background: var(--primary-strong);
}

body.is-admin .admin-pagebar-actions .admin-profile-menu summary {
    min-height: 34px;
    padding: 1px;
    border: 0;
    border-radius: 50%;
    background: transparent;
}

body.is-admin .admin-pagebar-actions .admin-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
}

.admin-search-trigger {
    display: inline-flex;
    min-width: 190px;
    height: 36px;
    align-items: center;
    gap: 8px;
    padding: 0 9px;
    border: 1px solid #e7e9ec;
    border-radius: 8px;
    background: #fafbfc;
    color: #8a9099;
    cursor: pointer;
    font: inherit;
    font-size: 11px;
    text-align: left;
}

.admin-search-trigger:hover {
    border-color: rgba(195, 141, 10, 0.42);
    color: var(--primary-strong);
}

.admin-search-trigger svg,
.command-palette__input-wrap svg {
    width: 15px;
    height: 15px;
    flex: 0 0 auto;
    fill: none;
    stroke: currentColor;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 1.7;
}

.admin-search-trigger kbd {
    margin-left: auto;
    padding: 2px 5px;
    border: 1px solid #e4e6e9;
    border-radius: 4px;
    color: #9da2aa;
    font-size: 9px;
}

body.is-admin .admin-date-pill {
    display: none;
}

body.is-admin .admin-profile-menu summary {
    min-height: 36px;
    padding: 3px;
    border-color: #e7e9ec;
    border-radius: 8px;
}

body.is-admin .admin-avatar {
    width: 28px;
    height: 28px;
    border-radius: 7px;
    background: #eceff2;
    color: #555c66;
}

body.is-admin .admin-profile-copy {
    display: none;
}

body.is-admin .panel {
    border-color: #e6e8eb;
    border-radius: 10px;
    background: #ffffff;
    box-shadow: none;
}

body.is-admin .panel-padded {
    padding: 18px;
}

body.is-admin .dashboard-overview-grid {
    grid-template-columns: minmax(0, 1.85fr) minmax(280px, 0.72fr);
    gap: 20px;
}

body.is-admin .dashboard-overview-chart-panel {
    padding: 20px;
}

body.is-admin .dashboard-chart-head {
    align-items: center;
    margin-bottom: 14px;
}

body.is-admin .dashboard-chart-head h2 {
    font-size: 19px;
    letter-spacing: -0.02em;
}

.dashboard-chart-head-tools {
    display: grid;
    justify-items: end;
    gap: 9px;
}

.dashboard-chart-periods {
    display: inline-flex;
    gap: 2px;
    padding: 3px;
    border: 1px solid #e9ebee;
    border-radius: 7px;
    background: #f8f9fa;
}

.dashboard-chart-periods a {
    display: inline-flex;
    min-width: 31px;
    height: 24px;
    align-items: center;
    justify-content: center;
    border-radius: 5px;
    color: #858b94;
    font-size: 10px;
    font-weight: 700;
    text-decoration: none;
}

.dashboard-chart-periods a:hover,
.dashboard-chart-periods a.active {
    background: #ffffff;
    color: var(--primary-strong);
    box-shadow: 0 1px 3px rgba(31, 38, 48, 0.08);
}

body.is-admin .dashboard-overview-chart-panel .line-chart-shell {
    min-height: 300px;
    padding: 4px 0 0;
    border: 0;
    background: #ffffff;
}

body.is-admin .chart-grid-line {
    stroke: #edf0f2;
    stroke-dasharray: 2 4;
}

body.is-admin .chart-axis-label {
    fill: #9298a0;
    font-size: 9px;
}

body.is-admin .chart-line {
    stroke-width: 2.1;
}

body.is-admin .chart-dot {
    stroke-width: 2;
}

body.is-admin .chart-summary-grid {
    gap: 0;
    margin-top: 12px;
    border-top: 1px solid var(--line-soft);
    border-bottom: 1px solid var(--line-soft);
}

body.is-admin .chart-summary-grid .mini-card {
    min-height: 58px;
    padding: 11px 13px;
    border: 0;
    border-right: 1px solid var(--line-soft);
    border-radius: 0;
    background: #ffffff;
}

body.is-admin .chart-summary-grid .mini-card:last-child {
    border-right: 0;
}

body.is-admin .chart-summary-grid .mini-card strong {
    font-size: 16px;
}

body.is-admin .dashboard-signal-strip {
    gap: 0;
    margin-top: 14px;
    border-top: 1px solid var(--line-soft);
}

body.is-admin .dashboard-signal {
    min-height: 54px;
    padding: 12px 13px 0;
    border: 0;
    border-right: 1px solid var(--line-soft);
    border-radius: 0;
    background: #ffffff;
}

body.is-admin .dashboard-signal:last-child {
    border-right: 0;
}

body.is-admin .dashboard-signal strong {
    font-size: 13px;
}

body.is-admin .sticky-stack {
    gap: 14px;
}

body.is-admin .sticky-stack .panel {
    padding: 16px;
}

body.is-admin .sticky-stack .panel:first-child {
    position: static;
}

body.is-admin .sticky-stack .panel-head {
    margin-bottom: 12px;
}

body.is-admin .mini-chart--dashboard {
    min-height: 170px;
    gap: 7px;
}

body.is-admin .month-bar-column {
    max-width: 28px;
    border-radius: 5px 5px 0 0;
    background: linear-gradient(180deg, var(--primary), #ead18b);
}

body.is-admin .month-bar > span:first-child {
    color: var(--primary-strong);
    font-size: 9px;
    font-weight: 700;
}

body.is-admin .month-bar strong {
    color: #6f7680;
    font-size: 10px;
}

body.is-admin .shortcut-grid {
    gap: 0;
    border-top: 1px solid var(--line-soft);
    border-left: 1px solid var(--line-soft);
}

body.is-admin .shortcut-grid a {
    min-height: 74px;
    padding: 11px;
    border: 0;
    border-right: 1px solid var(--line-soft);
    border-bottom: 1px solid var(--line-soft);
    border-radius: 0;
    background: #ffffff;
}

body.is-admin .shortcut-grid a:hover {
    background: var(--primary-soft);
}

body.is-admin .shortcut-grid strong {
    font-size: 12px;
}

body.is-admin .shortcut-icon {
    width: 26px;
    height: 26px;
    margin-bottom: 5px;
    border-radius: 6px;
    background: #fafbfc;
}

body.is-admin .record-list {
    gap: 0;
    border-top: 1px solid var(--line-soft);
}

body.is-admin .record-list > li > a {
    min-height: 52px;
    padding: 9px 0;
    border: 0;
    border-bottom: 1px solid var(--line-soft);
    border-radius: 0;
    background: #ffffff;
}

body.is-admin .record-list > li > a:hover {
    padding-inline: 6px;
    background: var(--surface-soft);
}

body.is-admin .record-list__main strong {
    font-size: 12px;
}

body.is-admin .record-list__amount {
    color: var(--text);
    font-size: 12px;
}

body.is-admin .kpi-grid {
    gap: 12px;
}

body.is-admin .kpi-card {
    min-height: 118px;
    padding: 15px 16px 13px;
    border-top: 2px solid #d9dde2;
    border-radius: 8px;
    box-shadow: none;
}

body.is-admin .kpi-card--traffic { border-top-color: #2d3239; }
body.is-admin .kpi-card--quotes { border-top-color: var(--primary); }
body.is-admin .kpi-card--leads { border-top-color: var(--lead); }
body.is-admin .kpi-card--pipeline { border-top-color: #8d969f; }

body.is-admin .kpi-card:hover {
    border-color: #dfe2e6;
    border-top-color: var(--primary);
    box-shadow: 0 5px 18px rgba(31, 38, 48, 0.06);
}

body.is-admin .kpi-value {
    font-size: 29px;
    letter-spacing: -0.04em;
}

body.is-admin .kpi-context {
    font-size: 9px;
    letter-spacing: 0.06em;
}

body.is-admin .trend-pill {
    min-height: 24px;
    padding-inline: 7px;
    font-size: 10px;
}

body.is-admin .dash-topline {
    padding-top: 0;
}

body.is-admin .status-chip {
    min-height: 28px;
    padding-inline: 10px;
    border-color: #e8eaed;
    background: #ffffff;
    font-size: 11px;
}

body.is-admin .dash-topline__actions {
    gap: 7px;
}

body.is-admin .dash-topline__actions .ghost-button {
    min-height: 30px;
    padding-inline: 10px;
    font-size: 11px;
}

.command-palette[hidden] {
    display: none;
}

.command-palette {
    position: fixed;
    inset: 0;
    z-index: 100;
    display: grid;
    place-items: start center;
    padding-top: min(15vh, 130px);
}

.command-palette__backdrop {
    position: absolute;
    inset: 0;
    background: rgba(26, 30, 36, 0.32);
    backdrop-filter: blur(3px);
}

.command-palette__dialog {
    position: relative;
    width: min(560px, calc(100vw - 28px));
    overflow: hidden;
    border: 1px solid #e4e6e9;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 24px 70px rgba(25, 29, 36, 0.2);
}

.command-palette__head,
.command-palette__input-wrap,
.command-palette__foot {
    display: flex;
    align-items: center;
}

.command-palette__head {
    justify-content: space-between;
    padding: 13px 15px 10px;
    color: #777e88;
    font-size: 11px;
    font-weight: 700;
}

.command-palette__head .admin-icon-button {
    width: 26px;
    height: 26px;
    flex-basis: 26px;
    border: 0;
    font-size: 17px;
}

.command-palette__input-wrap {
    gap: 9px;
    margin: 0 12px;
    padding: 0 10px;
    border: 1px solid #e4e7eb;
    border-radius: 8px;
    color: #969ca5;
}

.command-palette__input-wrap input {
    width: 100%;
    height: 42px;
    border: 0;
    outline: 0;
    background: transparent;
    color: #20242a;
    font: inherit;
    font-size: 13px;
}

.command-palette__input-wrap kbd,
.command-palette__foot kbd {
    padding: 2px 5px;
    border: 1px solid #e3e6e9;
    border-radius: 4px;
    color: #9096a0;
    font-size: 9px;
    white-space: nowrap;
}

.command-palette__results {
    display: grid;
    max-height: min(52vh, 420px);
    overflow-y: auto;
    padding: 9px 8px;
}

.command-palette__item {
    display: grid;
    grid-template-columns: 30px minmax(0, 1fr) auto;
    align-items: center;
    gap: 9px;
    padding: 8px;
    border-radius: 7px;
    color: #252a31;
    text-decoration: none;
}

.command-palette__item:hover,
.command-palette__item.is-keyboard-active,
.command-palette__item:focus-visible {
    outline: 0;
    background: #f8f9fa;
}

.command-palette__item-icon {
    display: grid;
    width: 30px;
    height: 30px;
    place-items: center;
    border-radius: 7px;
    background: #f2f3f5;
    color: var(--primary-strong);
    font-size: 14px;
}

.command-palette__item strong,
.command-palette__item small {
    display: block;
}

.command-palette__item strong {
    font-size: 12px;
    font-weight: 650;
}

.command-palette__item small {
    margin-top: 2px;
    color: #8a9099;
    font-size: 10px;
}

.command-palette__arrow {
    color: #a2a7ae;
    font-size: 12px;
}

.command-palette__empty {
    margin: 0;
    padding: 26px 12px;
    color: #858b94;
    font-size: 12px;
    text-align: center;
}

.command-palette__foot {
    gap: 12px;
    padding: 9px 14px;
    border-top: 1px solid #eef0f2;
    color: #969ca5;
    font-size: 10px;
}

.command-palette__foot span:first-child {
    margin-right: auto;
}

body.is-command-open {
    overflow: hidden;
}

/* Project management keeps the same data and permissions, but gives the
 * dashboard one visual rhythm and hides advanced filters until requested. */
body.is-admin .pm-dashboard {
    gap: 18px;
}

body.is-admin .pm-dashboard .pm-hero {
    min-height: 74px;
    align-items: center;
    padding: 16px 18px;
}

body.is-admin .pm-dashboard .pm-hero h2 {
    margin-top: 3px;
    font-size: 23px;
    letter-spacing: -0.03em;
}

body.is-admin .pm-dashboard .pm-hero .button,
body.is-admin .pm-dashboard .pm-hero .ghost-button {
    min-height: 34px;
}

body.is-admin .pm-dashboard .pm-kpis {
    gap: 10px;
}

body.is-admin .pm-dashboard .pm-kpi {
    min-height: 84px;
    padding: 14px;
    border-top: 2px solid #d9dde2;
}

body.is-admin .pm-dashboard .pm-kpi strong {
    margin-top: 5px;
    font-size: 25px;
}

body.is-admin .pm-filter-panel {
    overflow: hidden;
}

body.is-admin .pm-filter-panel > summary {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    min-height: 52px;
    padding: 0 16px;
    color: var(--muted-strong);
    cursor: pointer;
}

body.is-admin .pm-filter-panel > summary::-webkit-details-marker {
    display: none;
}

body.is-admin .pm-filter-panel > summary > span:first-child {
    display: grid;
    gap: 2px;
}

body.is-admin .pm-filter-panel > summary strong {
    font-size: 12px;
}

body.is-admin .pm-filter-panel > summary small {
    color: var(--muted);
    font-size: 10px;
}

body.is-admin .pm-filter-panel__toggle {
    color: var(--primary-strong);
    font-size: 11px;
    font-weight: 700;
}

body.is-admin .pm-filter-panel__toggle::after {
    content: '⌄';
    display: inline-block;
    margin-left: 6px;
    transition: transform 0.18s ease;
}

body.is-admin .pm-filter-panel[open] .pm-filter-panel__toggle::after {
    transform: rotate(180deg);
}

body.is-admin .pm-filter-panel > form,
body.is-admin .pm-filter-panel > .pm-saved-views {
    padding: 0 16px 16px;
}

body.is-admin .pm-filter-panel > form {
    padding-top: 14px;
    border-top: 1px solid var(--line-soft);
}

body.is-admin .pm-saved-views {
    border-top: 1px solid var(--line-soft);
}

body.is-admin .pm-saved-views .pm-panel-head {
    margin: 14px 0 8px;
}

body.is-admin .pm-saved-views__links {
    margin-bottom: 10px !important;
}

body.is-admin .pm-dashboard .pm-panel {
    padding: 16px;
}

body.is-admin .pm-dashboard .pm-panel-head {
    margin-bottom: 10px;
}

body.is-admin .pm-dashboard .pm-panel-head h3 {
    color: #252a31;
    font-size: 14px;
}

body.is-admin .pm-dashboard .pm-list {
    gap: 0;
}

body.is-admin .pm-dashboard .pm-list-item {
    min-height: 54px;
    padding: 10px 0;
}

body.is-admin .pm-dashboard .pm-list-item strong {
    font-size: 12px;
}

body.is-admin .pm-dashboard .pm-list-item span,
body.is-admin .pm-dashboard .pm-muted {
    font-size: 11px;
}

body.is-admin .pm-dashboard .pm-progress {
    height: 5px;
}

body.is-admin .pm-dashboard .pm-chart-bars {
    gap: 8px;
}

body.is-admin .pm-dashboard .pm-grid-wide {
    gap: 14px;
}

body.is-admin .pm-dashboard .pm-chip {
    min-height: 22px;
    font-size: 10px;
}

@media (min-width: 1101px) {
    body.is-sidebar-collapsed .admin-workspace--with-sidebar {
        grid-template-columns: var(--sidebar-collapsed-width) minmax(0, 1fr);
    }

    body.is-sidebar-collapsed .admin-sidebar-inner {
        padding-inline: 10px;
    }

    body.is-sidebar-collapsed .admin-sidebar-top {
        flex-direction: column;
    }

    body.is-sidebar-collapsed .admin-sidebar-brand {
        justify-content: center;
    }

    body.is-sidebar-collapsed .admin-nav-link {
        grid-template-columns: 1fr;
        justify-items: center;
        padding-inline: 4px;
    }

    body.is-sidebar-collapsed .admin-nav-link.active::before {
        left: -10px;
    }
}

@media (max-width: 1240px) {
    body.is-admin .dashboard-overview-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 1100px) {
    body.is-admin .admin-workspace--with-sidebar .admin-main {
        padding-inline: 22px;
    }

    body.is-admin .admin-pagebar {
        margin-inline: -22px;
        padding-inline: 22px;
    }
}

@media (max-width: 760px) {
    body.is-admin .admin-workspace--with-sidebar .admin-main {
        padding-inline: 14px;
    }

    body.is-admin .admin-pagebar {
        margin-inline: -14px;
        padding-inline: 14px;
    }

    .admin-search-trigger {
        min-width: 36px;
        width: 36px;
        justify-content: center;
        padding: 0;
    }

    .admin-search-trigger span,
    .admin-search-trigger kbd {
        display: none;
    }

    .dashboard-chart-head-tools {
        width: 100%;
        justify-items: stretch;
    }

    .dashboard-chart-periods {
        justify-content: space-between;
    }

    .dashboard-chart-periods a {
        flex: 1;
    }

    body.is-admin .chart-summary-grid .mini-card {
        border-bottom: 1px solid var(--line-soft);
    }

    body.is-admin .chart-summary-grid .mini-card:nth-child(2n) {
        border-right: 0;
    }
}

@media (prefers-reduced-motion: reduce) {
    body.is-admin *,
    body.is-admin *::before,
    body.is-admin *::after {
        scroll-behavior: auto !important;
        transition-duration: 0.01ms !important;
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
    }
}

/* Session feedback floats above the workspace instead of consuming a full
 * content row. It remains in the DOM flow for assistive technology and uses
 * the same alert semantics supplied by the shared layout. */
body.is-admin .admin-alert-stack {
    position: fixed;
    top: 82px;
    right: 24px;
    z-index: 90;
    display: grid;
    width: min(360px, calc(100vw - 48px));
    gap: 8px;
    pointer-events: none;
}

body.is-admin .admin-alert-stack .alert {
    position: relative;
    display: flex;
    min-height: 42px;
    align-items: center;
    gap: 9px;
    padding: 10px 13px;
    border-radius: 9px;
    box-shadow: 0 12px 28px rgba(28, 34, 42, 0.14);
    font-size: 12px;
    line-height: 1.4;
    pointer-events: auto;
    animation: admin-alert-in 0.22s ease-out both;
}

body.is-admin .admin-alert-stack .alert > span {
    min-width: 0;
}

body.is-admin .admin-alert-close {
    display: inline-grid;
    width: 22px;
    height: 22px;
    flex: 0 0 22px;
    margin: -2px -3px -2px auto;
    place-items: center;
    border: 0;
    border-radius: 5px;
    background: transparent;
    color: currentColor;
    cursor: pointer;
    font-size: 18px;
    font-weight: 400;
    line-height: 1;
    opacity: .68;
}

body.is-admin .admin-alert-close:hover,
body.is-admin .admin-alert-close:focus-visible {
    background: rgba(28, 34, 42, .08);
    opacity: 1;
    outline: none;
}

body.is-admin .admin-alert-stack .alert.is-dismissing {
    animation: admin-alert-out 0.22s ease-in both;
    pointer-events: none;
}

body.is-admin .admin-alert-stack .alert::before {
    width: 7px;
    height: 7px;
    flex: 0 0 7px;
    border-radius: 50%;
    background: currentColor;
    content: "";
}

body.is-admin .admin-alert-stack .alert-success {
    border-color: rgba(22, 133, 86, 0.25);
    background: #f0fbf6;
}

body.is-admin .admin-alert-stack .alert-warning {
    border-color: rgba(183, 121, 31, 0.26);
    background: #fffaf0;
}

@keyframes admin-alert-in {
    from {
        opacity: 0;
        transform: translateY(-8px) scale(0.98);
    }

    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes admin-alert-out {
    from {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    to {
        opacity: 0;
        transform: translateY(-6px) scale(.98);
    }
}

@media (max-width: 760px) {
    body.is-admin .admin-alert-stack {
        top: 74px;
        right: 14px;
        width: min(360px, calc(100vw - 28px));
    }
}

/* The original shell used a generated icon marker that expands on hover.
 * The current navigation already has real SVG icons, so that marker creates
 * the stray beige tiles seen behind each item. */
body.is-admin .admin-sidebar .admin-nav-icon,
body.is-admin .admin-sidebar .admin-nav-icon:hover {
    background: transparent !important;
    box-shadow: none !important;
}

body.is-admin .admin-sidebar .admin-nav-link .admin-nav-icon::before {
    display: none !important;
}

body.is-admin .admin-sidebar .admin-nav-link:hover {
    transform: none;
    box-shadow: none;
}

/* The navigation owns its scroll area so long admin menus remain reachable
 * without moving the page canvas or exposing a scrollbar beside the rail. */
body.is-admin .admin-sidebar {
    height: 100vh;
    height: 100dvh;
    overflow: hidden;
}

body.is-admin .admin-sidebar-inner {
    min-height: 0;
}

body.is-admin .admin-sidebar .admin-nav {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
    overscroll-behavior: contain;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

body.is-admin .admin-sidebar .admin-nav::-webkit-scrollbar {
    width: 0;
    height: 0;
    display: none;
}

/* Keep the section label, icon column, and text column on the same guides. */
body.is-admin .admin-sidebar-inner {
    padding-inline: 18px;
}

body.is-admin .admin-nav-label {
    margin-inline: 11px;
}

body.is-admin .admin-sidebar .admin-nav-link {
    display: grid;
    grid-template-columns: 34px minmax(0, 1fr);
    gap: 16px;
    align-items: center;
    padding: 5px 8px 5px 6px;
}

body.is-admin .admin-sidebar .admin-nav-icon {
    align-self: center;
    width: 34px;
    height: 34px;
}

body.is-admin .admin-sidebar .admin-nav-link > div {
    display: flex;
    min-width: 0;
    min-height: 34px;
    align-items: center;
}

body.is-admin .admin-sidebar .admin-nav-link > div > strong {
    line-height: 1.2;
}

body.is-admin .admin-sidebar .admin-nav-link.active::before {
    left: -18px;
}

@media (min-width: 1101px) {
    body.is-sidebar-collapsed .admin-sidebar-inner {
        padding-inline: 10px;
    }

    body.is-sidebar-collapsed .admin-sidebar .admin-nav-link {
        grid-template-columns: 1fr;
        padding-inline: 4px;
    }

    body.is-sidebar-collapsed .admin-sidebar .admin-nav-link > div {
        display: none;
    }

    body.is-sidebar-collapsed .admin-sidebar .admin-nav-link.active::before {
        left: -10px;
    }
}

/* Final navigation alignment rule: this intentionally comes after the
 * legacy shell overrides so the icon and label cannot drift onto separate
 * vertical baselines. */
body.is-admin .admin-sidebar .admin-nav-link {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-start !important;
    gap: 16px !important;
    min-height: 40px !important;
    padding: 5px 8px 5px 6px !important;
}

body.is-admin .admin-sidebar .admin-nav-link .admin-nav-icon {
    display: grid !important;
    place-items: center !important;
    align-self: center !important;
    width: 34px !important;
    height: 34px !important;
    flex: 0 0 34px !important;
    margin: 0 !important;
    transform: none !important;
}

body.is-admin .admin-sidebar .admin-nav-link .admin-nav-icon svg {
    display: block !important;
    width: 16px !important;
    height: 16px !important;
    margin: 0 !important;
}

body.is-admin .admin-sidebar .admin-nav-link > div {
    display: flex !important;
    align-items: center !important;
    height: 34px !important;
    min-height: 34px !important;
    min-width: 0 !important;
    margin: 0 !important;
    transform: none !important;
}

body.is-admin .admin-sidebar .admin-nav-link > div > strong {
    display: block !important;
    margin: 0 !important;
    line-height: 1 !important;
}

@media (min-width: 1101px) {
    body.is-sidebar-collapsed .admin-sidebar .admin-nav-link {
        display: grid !important;
        grid-template-columns: 1fr !important;
        justify-items: center !important;
        gap: 0 !important;
        padding-inline: 4px !important;
    }

    body.is-sidebar-collapsed .admin-sidebar .admin-nav-link > div {
        display: none !important;
    }
}

/* Promotion and archive use the same open canvas as Activity and Insights. */
.tt-promo-grid { display: grid; grid-template-columns: minmax(0, 1.5fr) minmax(260px, .72fr); gap: 34px; margin-top: 30px; }
.tt-promo-grid > .tt-section { min-width: 0; }
.tt-promo-preview { padding-left: 28px; border-left: 1px solid #edf0f2; }
.tt-promo-preview h3 { margin: 18px 0 7px; color: #25292f; font-family: var(--font-display); font-size: 24px; letter-spacing: -.04em; }
.tt-promo-preview p { margin: 0 0 20px; color: #858b94; font-size: 12px; }
.tt-promo-discount { display: block; margin-top: 34px; color: var(--primary-strong); font-family: var(--font-display); font-size: 54px; letter-spacing: -.07em; line-height: .95; }
.tt-promo-preview .data-note { display: block; margin-top: 0; }
.tt-archive-section { margin-top: 30px; }
.tt-archive-table th { color: #858b94; font-size: 9px; letter-spacing: .12em; text-transform: uppercase; }
.tt-archive-table td { vertical-align: middle; }

@media (max-width: 900px) {
    .tt-promo-grid { grid-template-columns: 1fr; gap: 24px; }
    .tt-promo-preview { padding: 24px 0 0; border-top: 1px solid #edf0f2; border-left: 0; }
}

@media (max-width: 680px) {
    .tt-archive-section { margin-top: 24px; }
    .tt-archive-table { min-width: 760px; }
}

/* Invoice builder: treat the form as a working studio, not a generic panel. */
body.is-invoice-builder .admin-main { gap: 0; background: #fff; }
body.is-invoice-builder .admin-pagebar { margin-bottom: 0; }
body.is-invoice-builder .tt-builder-head {
    max-width: 1540px;
    min-height: 118px;
    margin: 0 auto 26px;
    padding: 34px 0 18px;
    border-top: 0;
    border-bottom: 1px solid #edf0f2;
}
body.is-invoice-builder .tt-builder-head h2 {
    margin: 5px 0 0;
    color: #17191d;
    font-family: var(--font-display);
    font-size: 30px;
    font-weight: 650;
    letter-spacing: -.05em;
    line-height: 1.05;
}
body.is-invoice-builder .tt-builder-head p { margin: 7px 0 0; color: #858b94; font-size: 13px; }
body.is-invoice-builder .tt-builder-head .tt-page-badge { align-self: end; padding-bottom: 3px; }
body.is-invoice-builder .tt-builder-layout {
    max-width: 1540px;
    width: 100%;
    margin: 0 auto;
    grid-template-columns: minmax(0, 1.72fr) minmax(285px, .62fr);
    gap: 30px;
    align-items: start;
}
body.is-invoice-builder .tt-builder-main {
    padding: 26px 28px 28px;
    border: 1px solid #e7eaed;
    border-radius: 10px;
    background: #fff;
    box-shadow: none;
}
body.is-invoice-builder .tt-builder-main > .panel-head { margin-bottom: 22px; }
body.is-invoice-builder .tt-builder-main > .panel-head h2 { margin-top: 4px; font-size: 22px; letter-spacing: -.04em; }
body.is-invoice-builder .tt-builder-main > .panel-head p { margin-top: 7px; font-size: 12px; }
body.is-invoice-builder .quote-wizard { gap: 22px; }
body.is-invoice-builder .wizard-progress { gap: 8px; margin-bottom: 0; overflow: visible; }
body.is-invoice-builder .wizard-progress-button {
    min-height: 68px;
    padding: 12px 13px;
    border-color: #e2e6ea;
    border-radius: 9px;
    background: #fafbfc;
    transition: border-color .18s ease, background .18s ease, transform .18s ease;
}
body.is-invoice-builder .wizard-progress-button:hover { transform: translateY(-1px); border-color: #cfd5db; }
body.is-invoice-builder .wizard-progress-button.is-active,
body.is-invoice-builder .wizard-progress-button.is-complete {
    border-color: rgba(190, 139, 12, .42);
    background: #fff8e5;
    color: #956b08;
}
body.is-invoice-builder .wizard-progress-button.is-active { box-shadow: inset 0 2px 0 #c38d0a; }
body.is-invoice-builder .wizard-progress-index { width: 30px; height: 30px; border: 1px solid currentColor; background: transparent; }
body.is-invoice-builder .wizard-progress-copy strong { font-size: 13px; }
body.is-invoice-builder .wizard-progress-copy span { margin-top: 3px; color: #858b94; }
body.is-invoice-builder .wizard-pane-grid { gap: 18px; }
body.is-invoice-builder .field,
body.is-invoice-builder .field-full { gap: 8px; }
body.is-invoice-builder .field label,
body.is-invoice-builder .field-full > label { color: #59616b; font-size: 10px; font-weight: 750; letter-spacing: .1em; text-transform: uppercase; }
body.is-invoice-builder .field input,
body.is-invoice-builder .field select,
body.is-invoice-builder .field textarea,
body.is-invoice-builder .field-full input,
body.is-invoice-builder .field-full select,
body.is-invoice-builder .field-full textarea { min-height: 46px; border-color: #e1e5e9; border-radius: 8px; background: #fff; }
body.is-invoice-builder .field textarea,
body.is-invoice-builder .field-full textarea { min-height: 118px; }
body.is-invoice-builder .template-grid { gap: 12px; }
body.is-invoice-builder .template-card {
    min-height: 158px;
    padding: 16px;
    border-color: #e0e4e8;
    border-radius: 10px;
    background: #fff;
    transition: border-color .18s ease, background .18s ease, box-shadow .18s ease, transform .18s ease;
}
body.is-invoice-builder .template-card:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(32, 38, 45, .06); }
body.is-invoice-builder .template-card:has(input:checked) { border-color: #d4a334; background: #fff8e4; box-shadow: 0 8px 20px rgba(195, 141, 10, .1); }
body.is-invoice-builder .template-card p { display: block; margin: 0; color: #858b94; font-size: 11px; line-height: 1.45; }
body.is-invoice-builder .template-card strong { font-size: 15px; letter-spacing: -.02em; }
body.is-invoice-builder .swatch { width: 24px; height: 24px; }
body.is-invoice-builder .wizard-note { min-height: 100%; align-content: center; padding: 16px; border: 1px solid #e8ebee; border-radius: 9px; background: #f7f8f9; }
body.is-invoice-builder .wizard-note strong { font-size: 15px; }
body.is-invoice-builder .wizard-note p { margin: 0; font-size: 12px; line-height: 1.5; }
body.is-invoice-builder .wizard-actions { margin-top: 24px; padding-top: 18px; border-top-color: #e7eaed; }
body.is-invoice-builder .wizard-actions .admin-pill { min-height: 30px; padding-inline: 10px; font-size: 9px; }
body.is-invoice-builder .tt-builder-rail { position: sticky; top: 22px; gap: 14px; }
body.is-invoice-builder .tt-builder-rail > .tt-section { padding: 20px; border: 1px solid #e7eaed; border-radius: 10px; background: #fff; box-shadow: none; }
body.is-invoice-builder .tt-builder-rail .panel-head { margin-bottom: 14px; }
body.is-invoice-builder .tt-builder-rail .panel-title { font-size: 18px; letter-spacing: -.03em; }
body.is-invoice-builder .tt-builder-rail .stat-list { gap: 9px; }
body.is-invoice-builder .tt-builder-rail .stat-row { padding: 14px; border: 1px solid #edf0f2; border-radius: 9px; background: #f8f9fa; }
body.is-invoice-builder .tt-builder-rail .stat-row__value { font-size: 21px; }
body.is-invoice-builder .tt-builder-rail .record-list { gap: 0; }
body.is-invoice-builder .tt-builder-rail .record-list li { padding: 13px 0; border-bottom-color: #edf0f2; }
body.is-invoice-builder .tt-builder-rail .activity-item { padding: 12px; border-color: #edf0f2; background: #f8f9fa; }

@media (max-width: 900px) {
    body.is-invoice-builder .tt-builder-head { padding-inline: 28px; }
    body.is-invoice-builder .tt-builder-layout { grid-template-columns: 1fr; }
    body.is-invoice-builder .tt-builder-rail { position: static; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
}

@media (max-width: 680px) {
    body.is-invoice-builder .tt-builder-head { min-height: 0; padding: 26px 18px 18px; }
    body.is-invoice-builder .tt-builder-head h2 { font-size: 25px; }
    body.is-invoice-builder .tt-builder-head .tt-page-badge { margin-top: 14px; }
    body.is-invoice-builder .tt-builder-layout { padding-inline: 18px; }
    body.is-invoice-builder .tt-builder-main { padding: 20px 18px; }
    body.is-invoice-builder .wizard-progress { grid-template-columns: repeat(4, minmax(132px, 1fr)); overflow-x: auto; }
    body.is-invoice-builder .wizard-pane-grid { grid-template-columns: 1fr; }
    body.is-invoice-builder .tt-builder-rail { grid-template-columns: 1fr; }
}

/* Keep the builder heading and canvas on the exact same workspace guides. */
body.is-invoice-builder .admin-main {
    min-width: 0;
    overflow-x: hidden;
}

body.is-invoice-builder .tt-builder-head,
body.is-invoice-builder .tt-builder-layout {
    width: 100%;
    max-width: none;
    box-sizing: border-box;
    margin-left: 0;
    margin-right: 0;
}

body.is-invoice-builder .tt-builder-head {
    padding-left: 0;
    padding-right: 0;
}

body.is-invoice-builder .tt-builder-layout {
    justify-self: stretch;
}

/* Reduce instructional noise and let the controls carry the page. */
body.is-invoice-builder .tt-builder-head {
    min-height: 96px;
    padding-top: 24px;
    padding-bottom: 14px;
    margin-bottom: 20px;
}
body.is-invoice-builder .tt-builder-head h2 { font-size: 27px; letter-spacing: -.045em; }
body.is-invoice-builder .tt-builder-head p { font-size: 12px; }
body.is-invoice-builder .tt-builder-head p span { padding-inline: 4px; color: #b98a18; }
body.is-invoice-builder .tt-builder-main { padding: 22px 26px 24px; }
body.is-invoice-builder .tt-builder-main > .panel-head { margin-bottom: 18px; }
body.is-invoice-builder .tt-builder-main > .panel-head h2 { font-size: 20px; }
body.is-invoice-builder .tt-builder-main > .panel-head p { display: none; }
body.is-invoice-builder .quote-wizard { gap: 18px; }
body.is-invoice-builder .wizard-progress { gap: 7px; }
body.is-invoice-builder .wizard-progress-button {
    min-height: 58px;
    padding: 9px 11px;
    border-radius: 8px;
}
body.is-invoice-builder .wizard-progress-copy span { font-size: 10px; }
body.is-invoice-builder .wizard-progress-button.is-complete .wizard-progress-index { font-size: 0; }
body.is-invoice-builder .wizard-progress-button.is-complete .wizard-progress-index::after { content: "✓"; font-size: 13px; }
body.is-invoice-builder .wizard-progress-button:focus-visible,
body.is-invoice-builder .template-card:focus-within { outline: 3px solid rgba(195, 141, 10, .18); outline-offset: 2px; }
body.is-invoice-builder .template-card {
    min-height: 126px;
    padding: 14px;
    gap: 7px;
    border-radius: 9px;
}
body.is-invoice-builder .template-card p { display: none; }
body.is-invoice-builder .template-card::after {
    content: "";
    position: absolute;
    top: 13px;
    right: 13px;
    display: grid;
    width: 20px;
    height: 20px;
    place-items: center;
    border: 1px solid #dfe3e7;
    border-radius: 50%;
    color: transparent;
    font-size: 12px;
    font-weight: 800;
}
body.is-invoice-builder .template-card:has(input:checked)::after {
    content: "✓";
    border-color: #bb8813;
    background: #bb8813;
    color: #fff;
}
body.is-invoice-builder .template-card:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(32, 38, 45, .08); }
body.is-invoice-builder .template-card:has(input:checked) { box-shadow: 0 10px 24px rgba(195, 141, 10, .13); }
body.is-invoice-builder .template-card .eyebrow { padding-right: 24px; }
body.is-invoice-builder .swatch-row { gap: 6px; }
body.is-invoice-builder .swatch { width: 22px; height: 22px; }
body.is-invoice-builder .wizard-note { min-height: 80px; padding: 13px 15px; }
body.is-invoice-builder .wizard-note p { display: none; }
body.is-invoice-builder .tt-builder-rail > .tt-section { padding: 18px; }
body.is-invoice-builder .tt-builder-rail .panel-head { margin-bottom: 10px; }
body.is-invoice-builder .tt-builder-rail .panel-title { font-size: 17px; }
body.is-invoice-builder .tt-builder-rail .stat-list { gap: 7px; }
body.is-invoice-builder .tt-builder-rail .stat-row {
    position: relative;
    min-height: 72px;
    padding: 12px 13px 11px 16px;
    border: 0;
    border-radius: 8px;
    background: #f7f8f9;
    overflow: hidden;
}
body.is-invoice-builder .tt-builder-rail .stat-row::before { content: ""; position: absolute; inset: 0 auto 0 0; width: 3px; background: #c38d0a; }
body.is-invoice-builder .tt-builder-rail .stat-row:nth-child(2)::before { background: #d47b45; }
body.is-invoice-builder .tt-builder-rail .stat-row:nth-child(3)::before { background: #5b9b79; }
body.is-invoice-builder .tt-builder-rail .stat-row__meta { font-size: 10px; }
body.is-invoice-builder .tt-builder-rail .record-list__main span { font-size: 10px; }
body.is-invoice-builder .tt-builder-rail .activity-feed { gap: 7px; }

/* The rail is a reading aid, so its metrics should scan like a list instead
 * of stacking three oversized cards inside another card. */
body.is-invoice-builder .tt-builder-rail .stat-list { gap: 0; }
body.is-invoice-builder .tt-builder-rail .stat-row {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    grid-template-rows: auto auto;
    column-gap: 12px;
    min-height: 0;
    padding: 13px 0 13px 13px;
    border: 0;
    border-bottom: 1px solid #edf0f2;
    border-radius: 0;
    background: transparent;
}
body.is-invoice-builder .tt-builder-rail .stat-row:last-child { border-bottom: 0; }
body.is-invoice-builder .tt-builder-rail .stat-row::before {
    width: 2px;
    background: #c38d0a;
}
body.is-invoice-builder .tt-builder-rail .stat-row:nth-child(2)::before { background: #d47b45; }
body.is-invoice-builder .tt-builder-rail .stat-row:nth-child(3)::before { background: #5b9b79; }
body.is-invoice-builder .tt-builder-rail .stat-row__label { grid-column: 1; grid-row: 1; font-size: 9px; }
body.is-invoice-builder .tt-builder-rail .stat-row__value { grid-column: 2; grid-row: 1 / span 2; align-self: center; font-size: 18px; line-height: 1.1; text-align: right; white-space: nowrap; }
body.is-invoice-builder .tt-builder-rail .stat-row__meta { grid-column: 1; grid-row: 2; margin-top: 3px; font-size: 10px; }
body.is-invoice-builder .tt-builder-rail .record-list > li > a { min-height: 46px; padding: 8px 0; }
body.is-invoice-builder .tt-builder-rail .record-list__amount { font-size: 13px; }
body.is-invoice-builder .tt-builder-rail .record-list__empty { padding: 10px 0; border: 0; background: transparent; }

/* Keep the primary heading across every admin workspace on the same Sora
 * weight. The loaded display family provides 600/700/800; fractional weights
 * made otherwise identical page headers resolve differently by browser. */
body.is-admin .admin-pagebar h1,
body.is-admin .admin-main > .page-header h1,
body.is-admin .admin-main > .hero-banner h1,
body.is-admin .tt-dashboard-title h1,
body.is-admin .tt-dashboard-intro h2,
body.is-admin .tt-subpage-head h1,
body.is-admin .tt-builder-head h2,
body.is-admin .pm-hero h2 {
    font-family: var(--font-display);
    font-size: 18px !important;
    font-weight: 600 !important;
    letter-spacing: -0.025em;
    line-height: 1.2;
}

/* Board-wide dashboard shell: every authenticated page uses the same quiet
 * header, canvas, dividers, controls, and heading rhythm as the overview. */
body.is-admin:not(.is-dashboard-overview) .admin-workspace--with-sidebar .admin-main {
    padding: 0 42px 56px;
    background: #fff;
}

body.is-admin:not(.is-dashboard-overview) .admin-pagebar {
    min-height: 104px;
    margin: 0 -42px 0;
    padding: 0 42px;
    border-bottom: 1px solid #edf0f2;
    background: #fff;
    box-shadow: none;
}

body.is-admin:not(.is-dashboard-overview) .admin-pagebar h1 {
    color: #17191d;
    font-family: var(--font-display);
    font-size: 28px;
    font-weight: 600 !important;
    letter-spacing: -0.04em;
    line-height: 1.05;
}

body.is-admin:not(.is-dashboard-overview) .admin-pagebar .eyebrow {
    color: #858b94;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: .1em;
}

body.is-admin:not(.is-dashboard-overview) .admin-pagebar-actions {
    gap: 12px;
}

body.is-admin:not(.is-dashboard-overview) .admin-pagebar-actions .admin-search-trigger {
    min-width: 230px;
    height: 36px;
    border: 0;
    border-bottom: 1px solid #e4e7ea;
    border-radius: 0;
    background: transparent;
}

body.is-admin:not(.is-dashboard-overview) .admin-pagebar-actions .admin-search-trigger:hover {
    border-color: var(--primary);
    background: transparent;
}

body.is-admin:not(.is-dashboard-overview) .admin-pagebar-actions .button {
    min-height: 34px;
    padding-inline: 14px;
    border: 0;
    border-radius: 5px;
    background: #1c1e22;
    box-shadow: none;
    font-size: 11px;
}

body.is-admin:not(.is-dashboard-overview) .admin-pagebar-actions .ghost-button {
    min-height: 34px;
    border-radius: 5px;
    box-shadow: none;
}

body.is-admin:not(.is-dashboard-overview) .admin-pagebar-actions .admin-profile-menu summary {
    min-height: 34px;
    padding: 1px;
    border: 0;
    border-radius: 50%;
    background: transparent;
}

body.is-admin:not(.is-dashboard-overview) .admin-pagebar-actions .admin-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #eef0f2;
    color: #555b65;
    font-size: 10px;
}

body.is-admin:not(.is-dashboard-overview) .admin-main > .hero-banner,
body.is-admin:not(.is-dashboard-overview) .admin-main > .page-header,
body.is-admin:not(.is-dashboard-overview) .admin-main > .section-heading,
body.is-admin:not(.is-dashboard-overview) .admin-main > .tt-subpage-head,
body.is-admin:not(.is-dashboard-overview) .admin-main > .tt-builder-head {
    min-height: 104px;
    margin: 0 auto;
    padding: 34px 0 20px;
    border: 0;
    border-bottom: 1px solid #edf0f2;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
}

body.is-admin:not(.is-dashboard-overview) .admin-main > .hero-banner,
body.is-admin:not(.is-dashboard-overview) .admin-main > .page-header {
    display: grid;
    width: 100%;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 24px;
}

body.is-admin:not(.is-dashboard-overview) .admin-main > .hero-banner h1,
body.is-admin:not(.is-dashboard-overview) .admin-main > .page-header h1,
body.is-admin:not(.is-dashboard-overview) .admin-main > .section-heading h2,
body.is-admin:not(.is-dashboard-overview) .admin-main > .tt-subpage-head h1,
body.is-admin:not(.is-dashboard-overview) .admin-main > .tt-builder-head h2 {
    max-width: 780px;
    color: #17191d;
    font-family: var(--font-display);
    font-size: 18px !important;
    font-weight: 600 !important;
    letter-spacing: -0.025em;
    line-height: 1.2;
}

body.is-admin:not(.is-dashboard-overview) .pm-hero h2 {
    font-size: 18px !important;
    letter-spacing: -0.025em;
    line-height: 1.2;
}

/* The dashboard header is intentionally two-tiered: eyebrow plus title.
 * Remove the extra descriptive line from equivalent page headers so every
 * admin workspace keeps the same compact top-bar rhythm. */
body.is-admin .tt-dashboard-title > p,
body.is-admin .tt-subpage-head p,
body.is-admin .tt-builder-head p,
body.is-admin .tt-page > .hero-banner > div:first-child p,
body.is-admin .tt-page > .page-header > div:first-child p,
body.is-admin .admin-main > .hero-banner > div:first-child p,
body.is-admin .admin-main > .page-header > div:first-child p,
body.is-admin .admin-main > .section-heading > div:first-child p,
body.is-admin .pm-hero > div:first-child p {
    display: none !important;
}

/* Floating row actions are positioned from viewport coordinates in the
 * shared script. A transformed animated ancestor would create a different
 * containing block and make the menu jump or render in the wrong place. */
@keyframes tt-admin-fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

body.is-admin .tt-page,
body.is-admin .tt-dashboard {
    animation: tt-admin-fade-in 260ms ease both;
}

body.is-admin .action-menu-panel.is-floating {
    position: fixed !important;
}

/* Project files is a scanning surface: keep the action and the data visible,
 * and remove repeated explanatory sentences from nested cards and empty
 * states. */
body.is-admin .project-upload-panel .panel-head > div > p,
body.is-admin .project-analytics-grid .panel-head > p,
body.is-admin .project-upload-empty > p,
body.is-admin .project-empty-chart > p,
body.is-admin .project-kpi-grid .kpi-meta,
body.is-admin .project-upload-panel .admin-pill {
    display: none;
}

body.is-admin .project-upload-empty {
    min-height: 112px;
    align-content: center;
    gap: 12px;
}

body.is-admin .project-empty-chart {
    min-height: 150px;
    align-content: center;
}

body.is-admin:not(.is-dashboard-overview) .admin-main > .hero-banner p,
body.is-admin:not(.is-dashboard-overview) .admin-main > .page-header p,
body.is-admin:not(.is-dashboard-overview) .admin-main > .section-heading p,
body.is-admin:not(.is-dashboard-overview) .admin-main > .tt-subpage-head p,
body.is-admin:not(.is-dashboard-overview) .admin-main > .tt-builder-head p {
    max-width: 660px;
    color: #858b94;
    font-size: 12px;
}

body.is-admin:not(.is-dashboard-overview) .admin-main > .hero-banner .hero-callout {
    align-self: stretch;
}

body.is-admin:not(.is-dashboard-overview) .admin-main > .hero-banner .callout-card {
    border: 0;
    border-left: 2px solid #ead9a2;
    border-radius: 0;
    background: #fffbed;
    box-shadow: none;
}

body.is-admin:not(.is-dashboard-overview) .admin-main > .panel,
body.is-admin:not(.is-dashboard-overview) .admin-main > .dashboard-grid > .panel,
body.is-admin:not(.is-dashboard-overview) .admin-main > .dashboard-grid > .tt-section {
    border-color: #edf0f2;
    border-radius: 9px;
    background: #fff;
    box-shadow: none;
}

body.is-admin:not(.is-dashboard-overview) .pm-shell { gap: 18px; }
body.is-admin:not(.is-dashboard-overview) .pm-subnav { margin-bottom: 0; }

@media (max-width: 1100px) {
    body.is-admin:not(.is-dashboard-overview) .admin-workspace--with-sidebar .admin-main { padding-inline: 22px; }
    body.is-admin:not(.is-dashboard-overview) .admin-pagebar { margin-inline: -22px; padding-inline: 22px; }
}

@media (max-width: 760px) {
    body.is-admin:not(.is-dashboard-overview) .admin-workspace--with-sidebar .admin-main { padding-inline: 14px; }
    body.is-admin:not(.is-dashboard-overview) .admin-pagebar { margin-inline: -14px; padding-inline: 14px; }
    body.is-admin:not(.is-dashboard-overview) .admin-main > .hero-banner,
    body.is-admin:not(.is-dashboard-overview) .admin-main > .page-header { grid-template-columns: 1fr; }
    body.is-admin:not(.is-dashboard-overview) .admin-main > .hero-banner,
    body.is-admin:not(.is-dashboard-overview) .admin-main > .page-header,
    body.is-admin:not(.is-dashboard-overview) .admin-main > .section-heading,
    body.is-admin:not(.is-dashboard-overview) .admin-main > .tt-subpage-head,
        body.is-admin:not(.is-dashboard-overview) .admin-main > .tt-builder-head { padding-top: 26px; }
}

/* Projects & Files workspace ------------------------------------------------
 * A compact handoff dashboard with one clear upload action, a small metrics
 * strip, and a directory that stays readable when the dataset grows. */
.tt-projects-page {
    display: grid;
    width: 100%;
    max-width: none;
    margin: 0;
    gap: 0;
    color: #1b1d21;
}

body.is-project-files .admin-workspace--with-sidebar { width: auto; }
body.is-project-files .admin-main { width: auto; max-width: none; justify-self: stretch; }

.tt-projects-page-head {
    display: flex;
    min-height: 104px;
    align-items: center;
    justify-content: space-between;
    gap: 28px;
    padding: 0;
    border-bottom: 1px solid #edf0f2;
}

.tt-projects-page-head > div:first-child {
    min-width: 0;
}

.tt-projects-page-head h1 {
    margin: 2px 0 0;
    color: #17191d;
    font-family: var(--font-display);
    font-size: 28px;
    font-weight: 650;
    letter-spacing: -.04em;
    line-height: 1.05;
}

.tt-projects-heading-icon,
.tt-projects-section-icon,
.tt-projects-stat-icon,
.tt-projects-empty-icon { display: inline-grid; place-items: center; }
.tt-projects-heading-icon { display: none; }
.tt-projects-heading-icon + .eyebrow { display: block; }
.tt-projects-page-actions .button,
.tt-projects-page-actions .ghost-button { display: inline-flex; align-items: center; }
.tt-projects-page-actions svg { width: 17px; height: 17px; margin-right: 7px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.7; }

.tt-projects-page-actions,
.tt-projects-card-head,
.project-index-upload-form__actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.tt-projects-page-actions { flex-wrap: wrap; }
.tt-projects-page-actions .button,
.tt-projects-page-actions .ghost-button { min-height: 34px; border-radius: 5px; box-shadow: none; }

.tt-projects-stat-strip {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    margin-top: 30px;
    border-top: 1px solid #edf0f2;
    border-bottom: 1px solid #edf0f2;
}

.tt-projects-stat-strip > div {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 6px 12px;
    min-height: 88px;
    align-content: center;
    padding: 16px 22px;
    border-right: 1px solid #edf0f2;
}

.tt-projects-stat-strip > div:first-child { padding-left: 0; }
.tt-projects-stat-strip > div:last-child { border-right: 0; }
.tt-projects-stat-strip strong { grid-column: 1; color: #17191d; font-family: var(--font-display); font-size: 24px; font-weight: 620; line-height: 1; }
.tt-projects-stat-strip .metric-label { grid-column: 1 / -1; color: #858b94; font-size: 10px; }
.tt-projects-stat-icon { display: none; }

.tt-projects-workspace {
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(235px, .72fr);
    gap: 0;
    align-items: start;
    margin-top: 30px;
    border-bottom: 1px solid #edf0f2;
}

.tt-projects-card {
    min-width: 0;
    padding: 0;
    border: 0;
    border-radius: 0;
    background: transparent;
}

.tt-projects-card-head { align-items: flex-start; margin-bottom: 18px; }
.tt-projects-card-head > div:first-child { display: grid; min-width: 0; grid-template-columns: 24px minmax(0, 1fr); grid-template-rows: auto auto; column-gap: 8px; align-items: center; }
.tt-projects-section-icon { grid-row: 1 / span 2; display: inline-grid; width: 24px; height: 24px; margin: 0; border-radius: 7px; background: #f0f1f3; color: #69717b; }
.tt-projects-section-icon svg { width: 13px; height: 13px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.6; }
.tt-projects-card-head .eyebrow { grid-column: 2; }
.tt-projects-card-head h2 { grid-column: 2; margin: 4px 0 0; color: #25292f; font-family: var(--font-display); font-size: 17px; font-weight: 600; letter-spacing: -.025em; line-height: 1.2; }
.tt-projects-card-note,
.tt-projects-count { color: #858b94; font-size: 10px; white-space: nowrap; }
.tt-projects-card-note { padding: 5px 8px; border: 1px solid #ead9a2; border-radius: 5px; background: #fffbed; color: #946a05; }
.tt-projects-upload-card { padding: 0 28px 26px 0; }
.tt-projects-side { display: grid; gap: 24px; padding: 0 0 26px 28px; border-left: 1px solid #edf0f2; }

.tt-projects-upload-form { margin-top: 0; gap: 13px 16px; }
.tt-projects-upload-form .field,
.tt-projects-upload-form .field-full { gap: 6px; }
.tt-projects-upload-form .field > label,
.tt-projects-upload-form .field-full > label { color: #69717b; font-size: 10px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
.tt-projects-upload-form input,
.tt-projects-upload-form select,
.tt-projects-upload-form textarea { min-height: 42px; border-color: #e1e5e9; border-radius: 7px; background: #fbfcfc; }
.tt-projects-upload-form input[type="file"] { min-height: 42px; padding: 9px; border-style: dashed; }
.tt-projects-upload-form textarea { min-height: 78px; }
.tt-projects-form-note { color: #858b94; font-size: 10px; }
.tt-projects-upload-form__actions { padding-top: 4px; border-top: 1px solid #edf0f2; }
.tt-projects-upload-form__actions .button { min-height: 36px; border-radius: 5px; box-shadow: none; }
.tt-projects-upload-form [data-project-file-status]:empty { display: none; }

.tt-project-upload-progress {
    display: grid;
    gap: 8px;
    margin-top: 14px;
    padding: 12px 13px;
    border: 1px solid #e7eaed;
    border-radius: 8px;
    background: #fbfcfc;
}

.tt-project-upload-progress__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    color: #59616c;
    font-size: 11px;
}

.tt-project-upload-progress__head strong { color: #25292f; font-size: 11px; font-variant-numeric: tabular-nums; }
.tt-project-upload-progress__track { position: relative; height: 7px; overflow: hidden; border-radius: 99px; background: #e9edef; }
.tt-project-upload-progress__track > span { display: block; width: 0; height: 100%; border-radius: inherit; background: linear-gradient(90deg, #b27c00, #d6a32a); transition: width 180ms ease; }
.tt-project-upload-progress.is-indeterminate .tt-project-upload-progress__track > span { width: 42% !important; animation: tt-project-upload-progress 1.15s ease-in-out infinite; }
.tt-project-upload-progress.is-complete .tt-project-upload-progress__track > span { background: #4d9b70; }
.tt-project-upload-progress.is-error .tt-project-upload-progress__track > span { background: #c46b5e; }
.tt-project-upload-progress__detail { color: #858b94; font-size: 10px; }

@keyframes tt-project-upload-progress {
    0% { transform: translateX(-115%); }
    100% { transform: translateX(275%); }
}

@media (prefers-reduced-motion: reduce) {
    .tt-project-upload-progress.is-indeterminate .tt-project-upload-progress__track > span { animation: none; width: 42% !important; }
}

.tt-projects-empty {
    display: flex;
    min-height: 112px;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 20px;
    border: 1px dashed #dfe3e7;
    border-radius: 8px;
    background: #fbfcfc;
}

.tt-projects-empty strong { color: #25292f; font-size: 13px; }
.tt-projects-empty > span { color: #858b94; font-size: 11px; }
.tt-projects-empty-copy { display: grid; flex: 1; gap: 5px; }
.tt-projects-empty-copy span { color: #858b94; font-size: 11px; }
.tt-projects-empty .ghost-button { min-height: 32px; flex: 0 0 auto; border-radius: 5px; }
.tt-projects-empty--files { display: grid; min-height: 150px; align-content: center; justify-items: start; }
.tt-projects-empty--upload { min-height: 116px; }
.tt-projects-empty-icon { width: 30px; height: 30px; flex: 0 0 30px; border-radius: 8px; background: #fff7df; color: #946a05 !important; }
.tt-projects-empty-icon svg { width: 20px; height: 20px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-width: 1.8; }
.tt-projects-muted { color: #858b94; font-size: 11px; }

.tt-projects-status-card .project-status-chart-wrap { grid-template-columns: 1fr; gap: 12px; min-height: 0; justify-items: center; }
.tt-projects-status-card .project-status-chart { width: min(132px, 100%); }
.tt-projects-status-card .project-status-chart__centre strong { font-size: 23px; }
.tt-projects-status-card .tt-projects-muted { white-space: nowrap; text-align: center; }
.tt-projects-status-card .project-status-legend { gap: 9px; }
.tt-projects-status-card .project-status-legend__item { gap: 7px; }
.tt-projects-status-card .project-status-legend__item strong { font-size: 11px; }
.tt-projects-status-card .project-status-legend__item small { font-size: 10px; }
.tt-projects-files-card .project-file-bars { gap: 13px; padding-top: 0; }
.tt-projects-files-card .project-file-bar { gap: 7px; }

.tt-projects-register { margin-top: 30px; padding: 0; overflow: hidden; }
.tt-projects-register .tt-projects-card-head { margin: 0; padding: 20px 22px 16px; border-bottom: 1px solid #edf0f2; }
.tt-projects-register .table-wrap { border-top: 0; }
.tt-projects-register .quote-table th,
.tt-projects-register .quote-table td { padding-inline: 22px; }
.tt-projects-register .quote-table th:first-child,
.tt-projects-register .quote-table td:first-child { padding-left: 22px; }
.tt-projects-table-empty { display: flex; align-items: center; justify-content: space-between; gap: 12px; }

.tt-project-file-manager { overflow: visible; border-top: 1px solid #edf0f2; border-bottom: 1px solid #edf0f2; }
.project-file-manager-list { display: grid; gap: 10px; padding: 18px 22px 22px; }
.project-file-manager-row { display: grid; grid-template-columns: 42px minmax(0, 1fr); gap: 13px; padding: 14px; border: 1px solid #e7eaed; border-radius: 8px; background: #fbfcfc; }
.project-file-manager-row__body,
.project-file-manager-row__heading,
.project-file-manager-row__heading > div { min-width: 0; }
.project-file-manager-row__heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
.project-file-manager-row h3 { margin: 0; overflow-wrap: anywhere; color: #25292f; font-size: 13px; line-height: 1.3; }
.project-file-manager-row h3 + p { margin: 5px 0 0; color: #858b94; font-size: 10px; line-height: 1.5; }
.project-file-manager-row h3 + p a { color: #69717b; text-decoration: underline; text-underline-offset: 2px; }
.project-file-manager-row .project-file-card__description { margin-bottom: 0; }
.project-file-manager-row .project-file-card__actions { align-items: flex-start; margin-top: 12px; }
.project-file-manager-row .project-file-edit__form { position: static; }

@media (max-width: 980px) {
    body.is-admin .admin-workspace--with-sidebar { grid-template-columns: 1fr; }

    body.is-admin .admin-shell--app,
    body.is-admin .admin-workspace--with-sidebar,
    body.is-admin .admin-workspace--with-sidebar .admin-main {
        width: 100%;
        max-width: none;
        min-width: 0;
    }

    body.is-admin .admin-main > .tt-page,
    body.is-admin .admin-main > .tt-dashboard,
    body.is-admin .admin-main > .tt-projects-page {
        width: 100%;
        max-width: none;
        margin-inline: 0;
        justify-self: stretch;
    }

    body.is-project-files .admin-shell--app,
    body.is-project-files .admin-workspace--with-sidebar,
    body.is-project-files .admin-main,
    body.is-project-files .tt-projects-page {
        width: 100%;
        max-width: none;
        min-width: 0;
    }

    .tt-projects-workspace { grid-template-columns: 1fr; }
    .tt-projects-upload-card { padding-right: 0; }
    .tt-projects-side { grid-template-columns: repeat(2, minmax(0, 1fr)); padding-left: 0; border-left: 0; }
    body.is-admin.is-project-files .admin-workspace--with-sidebar .admin-main { padding-inline: 18px; }
}

@media (max-width: 680px) {
    .tt-projects-page { gap: 18px; }
    .tt-projects-page-head { align-items: flex-start; flex-direction: column; gap: 14px; }
    .tt-projects-page-head h1 { white-space: normal; }
    .tt-projects-page-actions { width: 100%; }
    .tt-projects-page-actions .button,
    .tt-projects-page-actions .ghost-button { flex: 1 1 auto; }
    body.is-admin.is-project-files .admin-workspace--with-sidebar .admin-main { padding-inline: 14px; }
    .tt-projects-stat-strip { grid-template-columns: repeat(2, minmax(0, 1fr)); margin-top: 24px; }
    .tt-projects-stat-strip > div,
    .tt-projects-stat-strip > div:first-child { padding: 12px 14px; }
    .tt-projects-stat-strip > div:nth-child(2) { border-right: 0; }
    .tt-projects-stat-strip > div:nth-child(-n+2) { border-bottom: 1px solid #edf0f2; }
    .tt-projects-side { grid-template-columns: 1fr; gap: 24px; padding-top: 24px; border-top: 1px solid #edf0f2; }
    .tt-projects-card { padding: 0; }
    .tt-projects-upload-card { padding: 0 0 24px; }
    .tt-projects-empty--upload { align-items: flex-start; flex-wrap: wrap; }
    .tt-projects-empty--upload .tt-projects-empty-copy { min-width: 0; }
    .tt-projects-empty--upload .ghost-button { width: 100%; }
    .tt-projects-upload-form__actions { align-items: flex-start; flex-direction: column; }
    .tt-projects-upload-form__actions .button { width: 100%; }
    .tt-projects-register .tt-projects-card-head { padding: 16px; }
    .tt-projects-register .quote-table th,
    .tt-projects-register .quote-table td { padding-inline: 16px; }
}

/* Responsive admin chrome --------------------------------------------------
 * The pagebar has two independent groups. Each breakpoint chooses which
 * controls belong in the single row; the toolbar never creates a second row
 * by wrapping its children. */
body.is-admin .admin-pagebar {
    display: flex;
    width: calc(100% + 84px);
    min-width: 0;
    min-height: 72px;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    flex-wrap: nowrap;
}

body.is-admin:not(.is-dashboard-overview) .admin-pagebar {
    min-height: 72px;
}

body.is-admin:not(.is-dashboard-overview) .admin-pagebar h1 {
    font-size: 24px;
}

body.is-dashboard-overview .admin-pagebar {
    margin-inline: -42px;
    padding-inline: 42px;
}

body.is-admin .admin-pagebar-title {
    min-width: 0;
    flex: 1 1 auto;
    align-items: center;
}

body.is-admin .admin-pagebar-title > div {
    min-width: 0;
}

body.is-admin .admin-pagebar-title h1 {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

body.is-admin .admin-pagebar-actions {
    display: flex;
    min-width: 0;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    flex: 0 0 auto;
    flex-wrap: nowrap;
}

body.is-admin .admin-pagebar-actions > *,
body.is-admin .admin-pagebar-actions .admin-profile-menu {
    flex: 0 0 auto;
}

body.is-admin .admin-pagebar-actions .admin-search-trigger {
    width: 230px;
    min-width: 0;
    flex: 0 1 230px;
}

.admin-mobile-page-action {
    display: none;
}

/* Medium: retain the action, but turn search into a compact icon. */
@media (min-width: 768px) and (max-width: 1023px) {
    body.is-admin .admin-pagebar {
        min-height: 68px;
        gap: 12px;
    }

    body.is-admin:not(.is-dashboard-overview) .admin-pagebar {
        min-height: 68px;
    }

    body.is-admin .admin-pagebar .eyebrow,
    body.is-admin:not(.is-dashboard-overview) .admin-pagebar .eyebrow {
        display: none;
    }

    body.is-admin .admin-pagebar h1,
    body.is-admin:not(.is-dashboard-overview) .admin-pagebar h1 {
        font-size: 18px;
    }

    body.is-admin .admin-pagebar-actions .admin-search-trigger {
        width: 36px;
        height: 36px;
        flex-basis: 36px;
        justify-content: center;
        padding: 0;
    }

    body.is-admin .admin-pagebar-actions .admin-search-trigger span,
    body.is-admin .admin-pagebar-actions .admin-search-trigger kbd {
        display: none;
    }

    body.is-admin .admin-pagebar-actions > .button,
    body.is-admin .admin-pagebar-actions > .ghost-button {
        max-width: 172px;
        min-height: 34px;
        overflow: hidden;
        padding-inline: 10px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
}

/* Small: the mobile topbar is intentionally only menu, title, and account.
 * Search and contextual actions remain available through their page/menu
 * surfaces instead of consuming a second toolbar row. */
@media (max-width: 767px) {
    body.is-admin .admin-pagebar {
        width: calc(100% + 28px);
        min-height: 64px;
        gap: 10px;
        margin-inline: -14px;
        padding: 10px 14px;
    }

    body.is-admin:not(.is-dashboard-overview) .admin-pagebar {
        width: calc(100% + 28px);
        min-height: 64px;
        margin-inline: -14px;
        padding: 10px 14px;
    }

    body.is-admin .admin-pagebar-title {
        gap: 10px;
    }

    body.is-admin .admin-mobile-nav-button {
        display: inline-grid;
        width: 40px;
        height: 40px;
        flex-basis: 40px;
        border-radius: 10px;
    }

    body.is-admin .admin-pagebar .eyebrow,
    body.is-admin:not(.is-dashboard-overview) .admin-pagebar .eyebrow {
        display: none;
    }

    body.is-admin .admin-pagebar h1,
    body.is-admin:not(.is-dashboard-overview) .admin-pagebar h1 {
        font-size: 18px;
    }

    body.is-admin .admin-pagebar-actions {
        gap: 0;
    }

    body.is-admin:not(.is-dashboard-overview) .admin-pagebar-actions {
        width: auto;
        justify-content: flex-end;
    }

    body.is-admin .admin-pagebar-actions .admin-search-trigger,
    body.is-admin .admin-pagebar-actions > .button,
    body.is-admin .admin-pagebar-actions > .ghost-button {
        display: none;
    }

    body.is-admin .admin-pagebar-actions .admin-profile-menu {
        display: block;
        margin-left: 0;
    }

    body.is-admin .admin-pagebar-actions .admin-profile-menu summary {
        min-height: 34px;
        padding: 1px;
    }

    body.is-admin .admin-pagebar-actions .admin-avatar {
        width: 32px;
        height: 32px;
        flex: 0 0 32px;
        border-radius: 50%;
    }

    .admin-mobile-page-action {
        display: flex;
        margin-top: -2px;
        padding-bottom: 18px;
    }

    .admin-mobile-page-action .ghost-button {
        width: 100%;
        min-height: 40px;
    }
}

/* The dashboard canvas uses a wider horizontal gutter than inner pages. Keep
 * the shared pagebar flush with that canvas instead of inheriting a smaller
 * legacy negative margin. */
@media (max-width: 1220px) {
    body.is-dashboard-overview .admin-workspace--with-sidebar .admin-main {
        padding-inline: 28px;
    }

    body.is-dashboard-overview .admin-pagebar {
        width: calc(100% + 56px);
        margin-inline: -28px;
        padding-inline: 28px;
    }
}

@media (min-width: 768px) and (max-width: 1100px) {
    body.is-admin:not(.is-dashboard-overview) .admin-pagebar {
        width: calc(100% + 44px);
    }
}

@media (max-width: 680px) {
    body.is-dashboard-overview .admin-workspace--with-sidebar .admin-main {
        padding-inline: 18px;
    }

    body.is-dashboard-overview .admin-pagebar {
        width: calc(100% + 36px);
        margin-inline: -18px;
        padding-inline: 18px;
    }
}
