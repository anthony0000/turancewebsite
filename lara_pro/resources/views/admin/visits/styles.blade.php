.va-page { display: grid; min-width: 0; gap: 22px; padding-top: 4px; }
.va-page *, .va-page *::before, .va-page *::after { box-sizing: border-box; }
.va-heading, .va-section-head, .va-filter-top, .va-period-caption { display: flex; align-items: center; justify-content: space-between; gap: 20px; }
.va-heading { align-items: flex-end; padding: 8px 0 4px; }
.va-heading > div, .va-section-head > div { min-width: 0; }
.va-heading h1 { max-width: 720px; margin: 0; color: var(--text); font-family: var(--font-display); font-size: clamp(26px, 3vw, 38px); line-height: 1.15; letter-spacing: -.045em; }
.va-heading p { max-width: 670px; margin: 12px 0 0; color: var(--muted); font-size: 13px; line-height: 1.7; }
.va-heading-actions { display: grid; flex-shrink: 0; justify-items: end; gap: 10px; }
.va-updated { color: var(--muted); font-size: 10px; }
.va-page .eyebrow { margin-bottom: 8px; color: var(--primary-strong); }
.va-filters { padding: 20px; }
.va-filter-top { margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid var(--line-soft); }
.va-filter-top > strong { font-size: 13px; }
.va-presets { display: flex; flex-wrap: wrap; gap: 4px; padding: 4px; border: 1px solid var(--line); border-radius: 9px; background: var(--surface-soft); }
.va-presets a { display: inline-flex; min-height: 32px; align-items: center; justify-content: center; padding: 0 13px; border-radius: 6px; color: var(--muted-strong); font-size: 11px; font-weight: 700; }
.va-presets a.active, .va-presets a:hover { background: #20242b; color: #fff; }
.va-filter-grid { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 14px; align-items: end; }
.va-filter-grid > * { min-width: 0; }
.va-path-filter { grid-column: span 3; }
.va-filter-actions { grid-column: span 2; display: flex; align-items: center; gap: 8px; justify-content: flex-end; }
.va-filter-hint, .va-note { margin: 14px 0 0; color: var(--muted); font-size: 11px; line-height: 1.65; }
.va-period-caption { flex-wrap: wrap; gap: 6px 16px; font-size: 12px; }
.va-period-caption > span { color: var(--muted); font-size: 11px; }
.va-metrics { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
body.is-admin .va-metric { min-width: 0; padding: 20px; }
.va-metric > strong { display: block; margin: 15px 0 12px; overflow-wrap: anywhere; color: var(--text); font-size: clamp(24px, 2.4vw, 34px); line-height: 1; letter-spacing: -.04em; }
.va-metric p { margin: 8px 0 0; color: var(--muted); font-size: 10px; }
.va-change { display: flex; flex-wrap: wrap; gap: 4px 8px; color: var(--muted); font-size: 11px; font-weight: 700; }
.va-change--up { color: #26704f; }
.va-change--down { color: #ad5138; }
.va-change small { color: var(--muted); font-size: 10px; font-weight: 500; }
.va-main-grid { display: grid; grid-template-columns: minmax(0, 1.9fr) minmax(260px, .8fr); gap: 20px; align-items: stretch; }
.va-three-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px; align-items: start; }
.va-two-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; align-items: start; }
body.is-admin .va-card { min-width: 0; padding: 22px; }
.va-card h2 { margin: 0; color: var(--text); font-family: var(--font-display); font-size: 18px; letter-spacing: -.03em; }
.va-section-head { align-items: flex-start; margin-bottom: 22px; }
.va-section-head p, .va-audience > p { margin: 7px 0 0; color: var(--muted); font-size: 11px; line-height: 1.65; }
.va-section-head .ghost-button { flex-shrink: 0; }
.va-chart { width: 100%; min-width: 0; }
.va-chart svg { display: block; width: 100%; height: auto; overflow: visible; }
.va-gridline { stroke: #e8ebee; stroke-width: 1; }
.va-axis { fill: #838994; font-size: 12px; }
.va-chart-current { fill: none; stroke: #b98518; stroke-width: 3; stroke-linecap: round; stroke-linejoin: round; vector-effect: non-scaling-stroke; }
.va-chart-fill { fill: #b98518; fill-opacity: .08; }
.va-chart-previous { fill: none; stroke: #9da8ba; stroke-width: 2; stroke-dasharray: 5 5; vector-effect: non-scaling-stroke; }
.va-chart-cursor { stroke: #686f7a; stroke-width: 1; stroke-dasharray: 3 4; }
.va-chart-legend { display: flex; flex-wrap: wrap; align-items: center; gap: 9px 16px; margin: 12px 0 18px; color: var(--muted); font-size: 10px; }
.va-chart-legend > span { display: inline-flex; gap: 6px; align-items: center; }
.va-chart-legend i { width: 8px; height: 8px; border-radius: 50%; background: #b98518; }
.va-chart-legend .va-legend-previous { background: #9da8ba; }
.va-chart-legend output { flex-basis: 100%; min-height: 16px; color: var(--text); }
.va-daily-data { border-top: 1px solid var(--line-soft); padding-top: 13px; }
.va-daily-data summary { width: fit-content; cursor: pointer; color: var(--muted-strong); font-size: 11px; font-weight: 700; }
.va-daily-data .table-wrap { max-height: 330px; margin-top: 14px; overflow: auto; }
.va-daily-data table { min-width: 400px; }
.va-audience-numbers { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; margin-top: 24px; }
.va-audience-numbers strong { display: block; font-size: 27px; letter-spacing: -.04em; overflow-wrap: anywhere; }
.va-audience-numbers span { display: block; margin-top: 5px; color: var(--muted); font-size: 10px; line-height: 1.5; }
.va-audience-numbers > div:first-child strong { color: #a27416; }
.va-audience-bar { height: 7px; margin: 16px 0 20px; overflow: hidden; border-radius: 10px; background: #9da8ba; }
.va-audience-bar > span { display: block; height: 100%; background: #c4932a; }
.va-facts { margin: 0; }
.va-facts > div { display: flex; justify-content: space-between; gap: 15px; padding: 10px 0; border-bottom: 1px solid var(--line-soft); font-size: 11px; }
.va-facts dt { color: var(--muted); }
.va-facts dd { margin: 0; text-align: right; font-weight: 700; }
.va-ranking { display: grid; gap: 17px; }
.va-ranking-label { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 8px; font-size: 11px; }
.va-ranking-label > :first-child { min-width: 0; overflow-wrap: anywhere; }
.va-ranking-label a, .va-path { color: var(--primary-strong); text-decoration: underline; text-underline-offset: 3px; }
.va-ranking-label strong { flex-shrink: 0; }
.va-ranking-label small { display: inline-block; min-width: 40px; padding-left: 7px; color: var(--muted); text-align: right; font-size: 9px; font-weight: 500; }
.va-track { height: 5px; overflow: hidden; border-radius: 8px; background: #f0f2f5; }
.va-track > span { display: block; height: 100%; border-radius: inherit; background: #c4932a; }
.va-pages-table { min-width: 670px; }
.va-log-table { min-width: 940px; }
.va-page .quote-table th[scope="row"] { background: transparent; font-size: 11px; text-transform: none; letter-spacing: 0; }
.va-path { display: inline-block; max-width: 280px; overflow-wrap: anywhere; font-weight: 650; }
.va-log-table td small { display: block; margin-top: 5px; color: var(--muted); font-size: 10px; }
.va-source { max-width: 190px; overflow-wrap: anywhere; }
.va-pagination { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px; margin-top: 16px; color: var(--muted); font-size: 11px; }
.va-pagination > div { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
.va-pagination [aria-disabled="true"] { opacity: .45; }
.va-device { display: inline-flex; align-items: center; padding: 4px 7px; border-radius: 5px; background: #f0f3f5; color: #4f5b67; font-size: 10px; }
.va-device--bot { color: #9a6722; background: #fff4df; }
.va-hours { display: grid; grid-template-columns: repeat(24, minmax(20px, 1fr)); gap: 4px; min-width: 0; overflow-x: auto; padding-bottom: 8px; }
.va-hour { display: grid; gap: 7px; text-align: center; }
.va-hour-value, .va-hour small { color: var(--muted); font-size: 8px; }
.va-hour > div { display: flex; height: 98px; flex-direction: column; justify-content: flex-end; background: #f4f5f7; border-radius: 4px; overflow: hidden; }
.va-hour > div > span { background: #c4932a; border-radius: 4px 4px 0 0; }
.va-weekdays { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); margin-top: 19px; padding-top: 15px; border-top: 1px solid var(--line-soft); gap: 8px; }
.va-weekdays > div { min-width: 0; text-align: center; }
.va-weekdays span { color: var(--muted); font-size: 10px; }
.va-weekdays strong { display: block; margin-top: 5px; font-size: 12px; overflow-wrap: anywhere; }
.va-method ul { display: grid; gap: 11px; padding-left: 18px; margin: 19px 0 0; color: var(--muted-strong); font-size: 11px; line-height: 1.7; }
.va-empty { display: grid; justify-items: center; gap: 12px; padding: 35px 20px; text-align: center; }
.va-empty h2 { margin: 0; font-size: 20px; }
.va-empty p, .va-empty-copy { margin: 0; color: var(--muted); font-size: 12px; line-height: 1.7; }
.va-page [id] { scroll-margin-top: 150px; }
.va-page a:focus-visible, .va-page button:focus-visible, .va-page summary:focus-visible { outline: 2px solid var(--primary); outline-offset: 3px; }
.va-trend-card [aria-pressed="false"] { opacity: .6; }
@media (max-width: 1300px) {
    .va-main-grid { grid-template-columns: minmax(0, 1fr); }
    .va-three-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .va-three-grid > :last-child { grid-column: 1 / -1; }
    .va-heading { align-items: flex-start; flex-direction: column; }
    .va-heading-actions { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; width: 100%; }
    .va-filter-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .va-path-filter { grid-column: auto; }
    .va-filter-actions { grid-column: 1 / -1; }
}
@media (max-width: 900px) {
    .va-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .va-two-grid { grid-template-columns: minmax(0, 1fr); }
}
@media (max-width: 640px) {
    .va-page { gap: 16px; }
    body.is-admin .va-card, body.is-admin .va-metric, .va-filters { padding: 16px; }
    .va-three-grid, .va-filter-grid { grid-template-columns: minmax(0, 1fr); }
    .va-three-grid > :last-child, .va-filter-actions { grid-column: auto; }
    .va-filter-top, .va-section-head { align-items: flex-start; flex-direction: column; gap: 12px; }
    .va-filter-actions { justify-content: stretch; }
    .va-filter-actions > * { flex: 1; }
    .va-heading-actions { align-items: flex-start; flex-direction: column; }
    .va-presets { width: 100%; }
    .va-presets a { flex: 1; min-height: 40px; padding-inline: 8px; }
    .va-chart { overflow-x: auto; }
    .va-chart svg { min-width: 500px; }
}
@media (max-width: 380px) {
    .va-metrics { grid-template-columns: minmax(0, 1fr); }
    .va-presets a { flex-basis: calc(50% - 4px); }
}
