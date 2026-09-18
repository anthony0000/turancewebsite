<style>
    .ppi-page { width: 100%; max-width: none; margin: 0; display: grid; gap: 20px; }
    .ppi-hero { padding: clamp(22px, 3vw, 36px); display: flex; align-items: flex-end; justify-content: space-between; gap: 24px; overflow: hidden; position: relative; }
    .ppi-hero::after { content: ''; position: absolute; width: 260px; height: 260px; border-radius: 50%; right: -90px; top: -135px; background: radial-gradient(circle, rgba(197, 145, 22, .2), rgba(197, 145, 22, 0) 68%); pointer-events: none; }
    .ppi-hero h2 { margin: 5px 0 8px; max-width: 760px; font-size: clamp(25px, 3vw, 40px); line-height: 1.08; }
    .ppi-hero p { margin: 0; max-width: 720px; color: var(--muted); line-height: 1.65; }
    .ppi-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 10px; position: relative; z-index: 1; }
    .ppi-kpis { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
    .ppi-kpi { padding: 20px; }
    .ppi-kpi span { display: block; color: var(--muted); font-size: 12px; text-transform: uppercase; letter-spacing: .09em; }
    .ppi-kpi strong { display: block; margin-top: 9px; font-size: clamp(22px, 2vw, 31px); color: var(--text); }
    .ppi-toolbar { padding: 16px; }
    .ppi-filter-grid { display: grid; grid-template-columns: minmax(220px, 1fr) minmax(150px, .35fr) minmax(190px, .5fr) auto; gap: 12px; align-items: end; }
    .ppi-table-wrap { overflow-x: auto; }
    .ppi-table { width: 100%; border-collapse: collapse; }
    .ppi-table th { padding: 13px 15px; border-bottom: 1px solid var(--line); color: var(--muted); font-size: 11px; text-align: left; text-transform: uppercase; letter-spacing: .08em; white-space: nowrap; }
    .ppi-table td { padding: 16px 15px; border-bottom: 1px solid var(--line); vertical-align: middle; }
    .ppi-table tr:last-child td { border-bottom: 0; }
    .ppi-table a { color: var(--text); font-weight: 700; text-decoration: none; }
    .ppi-table small { display: block; margin-top: 4px; color: var(--muted); }
    .ppi-status { display: inline-flex; align-items: center; gap: 7px; padding: 6px 10px; border-radius: 999px; background: #fff5df; color: #946600; font-size: 12px; font-weight: 800; }
    .ppi-status::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
    .ppi-status--paid { background: #e9f8f0; color: #157347; }
    .ppi-empty { padding: 48px 24px; text-align: center; }
    .ppi-empty h3 { margin: 0 0 7px; }
    .ppi-empty p { margin: 0 auto 18px; max-width: 500px; color: var(--muted); }
    .ppi-form-layout { display: grid; grid-template-columns: minmax(0, 1.5fr) minmax(300px, .6fr); gap: 18px; align-items: start; }
    .ppi-stack { display: grid; gap: 18px; }
    .ppi-card { padding: clamp(18px, 2vw, 27px); }
    .ppi-card-head { display: flex; justify-content: space-between; gap: 18px; margin-bottom: 20px; }
    .ppi-card-head h3 { margin: 3px 0 0; font-size: 20px; }
    .ppi-card-head p { margin: 5px 0 0; color: var(--muted); }
    .ppi-step { width: 34px; height: 34px; border-radius: 50%; display: grid; place-items: center; flex: 0 0 auto; background: #f2e5bd; color: #8d6500; font-weight: 800; }
    .ppi-grid-2, .ppi-grid-3 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
    .ppi-grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .ppi-field-wide { grid-column: 1 / -1; }
    .ppi-live-card { position: sticky; top: 110px; overflow: hidden; }
    .ppi-live-top { padding: 24px; background: #202634; color: white; }
    .ppi-live-top span { color: #d6b55a; font-size: 11px; letter-spacing: .12em; text-transform: uppercase; }
    .ppi-live-top strong { display: block; margin-top: 7px; font-size: 22px; }
    .ppi-live-body { padding: 22px; }
    .ppi-live-row { display: flex; justify-content: space-between; gap: 18px; padding: 11px 0; border-bottom: 1px solid var(--line); }
    .ppi-live-row span { color: var(--muted); }
    .ppi-live-row strong { text-align: right; }
    .ppi-live-total { margin: 16px -22px -22px; padding: 22px; background: #f6f3eb; }
    .ppi-live-total strong { display: block; margin-top: 4px; color: #bd8500; font-size: 31px; }
    .ppi-progress-note { margin-top: 10px; padding: 11px 13px; border-radius: 10px; background: #f5f6f8; color: var(--muted); font-size: 13px; line-height: 1.5; }
    .ppi-errors { padding: 16px 20px; border: 1px solid #f1b7b7; background: #fff4f4; color: #8e2525; border-radius: 14px; }
    .ppi-errors strong { display: block; margin-bottom: 7px; }
    .ppi-errors ul { margin: 0; padding-left: 18px; }
    .ppi-detail-grid { display: grid; grid-template-columns: minmax(0, 1.45fr) minmax(290px, .55fr); gap: 20px; align-items: start; }
    .ppi-document-stage { padding: clamp(10px, 2vw, 24px); overflow: auto; background: #e9ebef; }
    .ppi-document-frame { min-width: 760px; max-width: 900px; margin: 0 auto; box-shadow: 0 18px 50px rgba(25, 30, 43, .12); }
    .ppi-meta { display: grid; gap: 0; }
    .ppi-meta-row { display: flex; justify-content: space-between; gap: 18px; padding: 12px 0; border-bottom: 1px solid var(--line); }
    .ppi-meta-row span { color: var(--muted); }
    .ppi-meta-row strong { text-align: right; }
    .ppi-paid-note { padding: 14px; border-radius: 12px; background: #eaf8f0; color: #16633f; line-height: 1.5; }
    .ppi-payment-form { display: grid; gap: 12px; }
    @media (max-width: 1050px) {
        .ppi-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ppi-form-layout, .ppi-detail-grid { grid-template-columns: 1fr; }
        .ppi-live-card { position: static; }
        .ppi-filter-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 680px) {
        .ppi-hero { align-items: flex-start; flex-direction: column; }
        .ppi-kpis, .ppi-grid-2, .ppi-grid-3, .ppi-filter-grid { grid-template-columns: 1fr; }
        .ppi-actions > * { width: 100%; justify-content: center; text-align: center; }
        .ppi-table th:nth-child(3), .ppi-table td:nth-child(3), .ppi-table th:nth-child(5), .ppi-table td:nth-child(5) { display: none; }
    }
</style>
