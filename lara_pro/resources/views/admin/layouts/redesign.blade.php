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
    --sidebar-width: 248px;
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

.admin-brand-copy {
    min-width: 0;
}

.admin-brand-copy strong {
    display: block;
    overflow: hidden;
    color: var(--text);
    font-size: 13px;
    font-weight: 800;
    line-height: 1.2;
    text-overflow: ellipsis;
    white-space: nowrap;
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

.dashboard-command {
    display: grid;
    grid-template-columns: minmax(0, 1.35fr) minmax(320px, 0.75fr);
    gap: 16px;
    align-items: stretch;
    padding-top: 4px;
}

.dashboard-command-main {
    display: grid;
    gap: 16px;
    align-content: center;
    padding: 24px;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--surface);
    box-shadow: var(--shadow-soft);
}

.dashboard-command-main h2 {
    max-width: 760px;
    margin: 0;
    color: var(--text);
    font-size: 34px;
    font-weight: 840;
    line-height: 1.04;
}

.dashboard-command-main p {
    max-width: 620px;
    margin: 0;
    color: var(--muted);
    font-size: 14px;
}

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

.kpi-card {
    display: grid;
    gap: 12px;
    min-height: 142px;
    padding: 17px;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--surface);
    box-shadow: var(--shadow-soft);
}

.kpi-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.kpi-value {
    color: var(--text);
    font-family: var(--font-display);
    font-size: 32px;
    font-weight: 840;
    line-height: 1;
}

.kpi-context {
    color: var(--muted);
    font-size: 12px;
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

.insight-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
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
    overflow: hidden;
    border: 1px solid var(--line-soft);
    border-radius: var(--radius);
    background: linear-gradient(180deg, #ffffff, #fffdf8);
}

.line-chart {
    display: block;
    width: 100%;
    min-height: 220px;
}

.chart-grid-line {
    stroke: #e7edf4;
    stroke-width: 1;
}

.chart-line {
    fill: none;
    stroke-width: 4;
    stroke-linecap: round;
    stroke-linejoin: round;
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

.mini-chart {
    display: grid;
    grid-template-columns: repeat(6, minmax(56px, 1fr));
    align-items: end;
    gap: 10px;
    min-height: 220px;
}

.month-bar {
    display: grid;
    min-width: 0;
    justify-items: center;
    gap: 8px;
    color: var(--muted);
    font-size: 11px;
    text-align: center;
}

.month-bar-column {
    width: 100%;
    max-width: 36px;
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

@media (max-width: 1240px) {
    .kpi-grid,
    .insight-grid,
    .chart-summary-grid,
    .template-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .analytics-grid,
    .dashboard-grid,
    .dashboard-command {
        grid-template-columns: 1fr;
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

    .section-heading {
        display: grid;
    }

    .kpi-grid,
    .insight-grid,
    .chart-summary-grid,
    .template-grid,
    .wizard-pane-grid,
    .form-grid,
    .review-grid,
    .line-items-currency-grid {
        grid-template-columns: 1fr;
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
