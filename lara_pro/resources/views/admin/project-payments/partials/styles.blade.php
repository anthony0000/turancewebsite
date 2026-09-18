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
    .ppi-receipt-grid { display: grid; grid-template-columns: minmax(300px, .8fr) minmax(0, 1.2fr); gap: 18px; align-items: start; }
    .ppi-upload-note { padding: 13px 15px; border: 1px solid #d9e7df; border-radius: 12px; background: #f4faf6; color: #38604b; font-size: 13px; line-height: 1.55; }
    .ppi-file-input { padding: 13px; border: 1px dashed #b9bec9; border-radius: 12px; background: #fafbfc; }
    .ppi-receipt-list { display: grid; gap: 11px; }
    .ppi-receipt-item { padding: 15px; border: 1px solid var(--line); border-radius: 13px; display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 14px; align-items: center; }
    .ppi-receipt-item h4 { margin: 0 0 5px; font-size: 15px; }
    .ppi-receipt-item p { margin: 0; color: var(--muted); font-size: 13px; line-height: 1.55; }
    .ppi-receipt-item .ppi-actions { justify-content: flex-end; }
    .ppi-secure-badge { display: inline-flex; align-items: center; gap: 7px; color: #24633f; font-size: 12px; font-weight: 800; }
    .ppi-secure-badge::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: #2f9d60; box-shadow: 0 0 0 4px #e7f6ed; }
    .ppi-index-page { gap: 16px; }
    .ppi-index-hero { position: relative; display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: clamp(28px, 5vw, 72px); min-height: 224px; padding: clamp(28px, 3.2vw, 46px); overflow: hidden; border-color: #262a31; background: #191c22; color: #fff; box-shadow: 0 20px 42px rgba(24, 27, 33, .12); }
    .ppi-index-hero::before { content: ''; position: absolute; inset: 0; background: radial-gradient(circle at 86% 6%, rgba(209, 170, 75, .19), transparent 31%), linear-gradient(115deg, transparent 60%, rgba(255,255,255,.025)); pointer-events: none; }
    .ppi-index-hero__copy, .ppi-collection-card { position: relative; z-index: 1; }
    .ppi-kicker { display: inline-flex; align-items: center; gap: 9px; color: #d7b864; font-size: 11px; font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
    .ppi-kicker > span { width: 22px; height: 1px; background: currentColor; }
    .ppi-index-hero h2 { margin: 12px 0 10px; max-width: 700px; color: #fff; font-size: clamp(31px, 3.2vw, 47px); line-height: 1.02; letter-spacing: -.035em; }
    .ppi-index-hero h2 em { color: #d7b864; font-family: Georgia, serif; font-weight: 500; }
    .ppi-index-hero__copy > p { max-width: 670px; margin: 0; color: #b7bbc3; font-size: 14px; line-height: 1.65; }
    .ppi-index-hero__actions { margin-top: 24px; }
    .ppi-index-hero .ppi-primary-action { min-height: 43px; border-color: #d0aa4e; background: #d0aa4e; color: #17191d; box-shadow: none; }
    .ppi-index-hero .ppi-primary-action:hover { border-color: #e0c173; background: #e0c173; }
    .ppi-primary-action svg, .ppi-text-action svg { width: 17px; height: 17px; fill: none; stroke: currentColor; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
    .ppi-text-action { display: inline-flex; min-height: 42px; align-items: center; gap: 7px; padding: 0 7px; color: #e1e3e7; font-size: 13px; font-weight: 720; text-decoration: none; }
    .ppi-text-action:hover { color: #d7b864; }
    .ppi-collection-card { align-self: stretch; padding: 18px; border: 1px solid rgba(255,255,255,.1); border-radius: 14px; background: rgba(255,255,255,.055); }
    .ppi-collection-card__top { display: flex; align-items: center; justify-content: space-between; color: #c5c8ce; font-size: 11px; font-weight: 750; letter-spacing: .08em; text-transform: uppercase; }
    .ppi-live-dot { display: inline-flex; align-items: center; gap: 6px; color: #8bd5aa; letter-spacing: .04em; }
    .ppi-live-dot::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #55bd7e; box-shadow: 0 0 0 4px rgba(85,189,126,.12); }
    .ppi-collection-card__body { display: grid; grid-template-columns: 112px 1fr; gap: 18px; align-items: center; margin-top: 18px; }
    .ppi-rate-ring { position: relative; display: grid; width: 108px; aspect-ratio: 1; place-content: center; border-radius: 50%; background: conic-gradient(#d6b258 calc(var(--ppi-rate) * 1%), rgba(255,255,255,.1) 0); text-align: center; }
    .ppi-rate-ring::before { content: ''; position: absolute; inset: 8px; border-radius: inherit; background: #25282e; }
    .ppi-rate-ring strong, .ppi-rate-ring span { position: relative; z-index: 1; }
    .ppi-rate-ring strong { font-size: 24px; line-height: 1; }
    .ppi-rate-ring span { margin-top: 4px; color: #aeb2b9; font-size: 10px; text-transform: uppercase; }
    .ppi-collection-summary { display: grid; }
    .ppi-collection-summary > strong { font-size: 18px; }
    .ppi-collection-summary > span { margin-top: 2px; color: #9fa4ac; font-size: 12px; }
    .ppi-collection-summary__line { display: flex; justify-content: space-between; gap: 10px; margin-top: 18px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,.09); color: #b6bac1; font-size: 11px; }
    .ppi-collection-summary__line b { color: #fff; }
    .ppi-index-page .ppi-kpis { gap: 12px; }
    .ppi-index-page .ppi-kpi { position: relative; min-height: 126px; padding: 18px 19px; overflow: hidden; box-shadow: none; }
    .ppi-index-page .ppi-kpi::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 3px; background: var(--kpi-color, #8c929c); opacity: .75; }
    .ppi-kpi__head { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .ppi-index-page .ppi-kpi__head > span:first-child { color: var(--muted); font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
    .ppi-kpi__icon { display: grid; width: 30px; height: 30px; place-items: center; border-radius: 9px; background: color-mix(in srgb, var(--kpi-color, #8c929c) 11%, white); color: var(--kpi-color, #8c929c); }
    .ppi-kpi__icon svg { width: 16px; height: 16px; fill: none; stroke: currentColor; stroke-width: 1.7; stroke-linecap: round; stroke-linejoin: round; }
    .ppi-index-page .ppi-kpi strong { margin-top: 8px; overflow: hidden; font-size: clamp(22px, 2vw, 29px); line-height: 1.05; text-overflow: ellipsis; white-space: nowrap; }
    .ppi-index-page .ppi-kpi small { display: block; margin-top: 7px; color: var(--muted); font-size: 11px; }
    .ppi-kpi--neutral { --kpi-color: #667085; }
    .ppi-kpi--paid { --kpi-color: #248557; }
    .ppi-kpi--open { --kpi-color: #bd7b16; }
    .ppi-kpi--collected { --kpi-color: #a67c19; }
    .ppi-index-page .ppi-toolbar { padding: 18px 19px 19px; box-shadow: none; }
    .ppi-toolbar__head, .ppi-table-heading { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 15px; }
    .ppi-toolbar__head h3, .ppi-table-heading h3 { margin: 3px 0 0; font-size: 17px; letter-spacing: -.01em; }
    .ppi-result-count { padding: 5px 9px; border-radius: 999px; background: #f1f3f5; color: #666d77; font-size: 11px; font-weight: 750; }
    .ppi-index-page .ppi-filter-grid { grid-template-columns: minmax(280px, 1.4fr) minmax(150px, .42fr) minmax(210px, .65fr) auto; }
    .ppi-index-page .field label { font-size: 10px; letter-spacing: .09em; }
    .ppi-index-page .field input, .ppi-index-page .field select { min-height: 43px; border-color: #dfe3e8; border-radius: 9px; background-color: #fbfcfd; }
    .ppi-input-shell { position: relative; }
    .ppi-input-shell svg { position: absolute; top: 50%; left: 13px; width: 17px; height: 17px; transform: translateY(-50%); fill: none; stroke: #8a9099; stroke-width: 1.8; stroke-linecap: round; }
    .ppi-input-shell input { width: 100%; padding-left: 40px !important; }
    .ppi-filter-actions { flex-wrap: nowrap; }
    .ppi-filter-actions .button, .ppi-filter-actions .ghost-button { min-height: 43px; }
    .ppi-invoice-panel { overflow: hidden; box-shadow: none; }
    .ppi-table-heading { margin: 0; padding: 19px 20px 15px; border-bottom: 1px solid var(--line); }
    .ppi-table-heading > a { color: #8c6a18; font-size: 12px; font-weight: 750; text-decoration: none; }
    .ppi-table-heading > a span { display: inline-block; margin-left: 4px; transition: transform .16s ease; }
    .ppi-table-heading > a:hover span { transform: translateX(3px); }
    .ppi-index-page .ppi-table th { padding: 11px 17px; background: #fafbfc; color: #7b818a; font-size: 10px; }
    .ppi-index-page .ppi-table td { padding: 17px; }
    .ppi-index-page .ppi-table tbody tr { transition: background .16s ease; }
    .ppi-index-page .ppi-table tbody tr:hover { background: #fcfbf7; }
    .ppi-invoice-identity { display: flex; align-items: center; gap: 11px; min-width: 230px; }
    .ppi-invoice-icon { display: grid; width: 35px; height: 35px; flex: 0 0 auto; place-items: center; border-radius: 10px; background: #f6f0df; color: #9a741d; }
    .ppi-invoice-icon svg { width: 17px; height: 17px; fill: none; stroke: currentColor; stroke-width: 1.7; stroke-linecap: round; stroke-linejoin: round; }
    .ppi-progress-cell { min-width: 112px; }
    .ppi-progress-cell > div:first-child { display: flex; align-items: baseline; justify-content: space-between; gap: 8px; }
    .ppi-progress-cell span { color: var(--muted); font-size: 10px; }
    .ppi-progress-track { height: 4px; margin-top: 8px; overflow: hidden; border-radius: 999px; background: #e9ecf0; }
    .ppi-progress-track span { display: block; height: 100%; border-radius: inherit; background: #c69a36; }
    .ppi-amount-primary, .ppi-date-primary { color: var(--text); font-size: 14px; white-space: nowrap; }
    .ppi-date-primary { font-size: 13px; }
    .ppi-index-page .ppi-status { padding: 6px 10px; border: 1px solid rgba(148,102,0,.12); }
    .ppi-index-page .ppi-status--paid { border-color: rgba(21,115,71,.12); }
    .ppi-row-action { display: grid !important; width: 34px; height: 34px; place-items: center; border: 1px solid var(--line); border-radius: 9px; background: #fff; color: #59606a !important; transition: border-color .16s ease, background .16s ease, color .16s ease; }
    .ppi-row-action:hover { border-color: #d3b15d; background: #fbf6e9; color: #8e6a16 !important; }
    .ppi-row-action svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
    @media (max-width: 1050px) {
        .ppi-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ppi-form-layout, .ppi-detail-grid, .ppi-receipt-grid { grid-template-columns: 1fr; }
        .ppi-live-card { position: static; }
        .ppi-filter-grid { grid-template-columns: 1fr 1fr; }
        .ppi-index-hero { grid-template-columns: minmax(0, 1fr) 270px; }
        .ppi-index-page .ppi-filter-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 820px) {
        .ppi-index-hero { grid-template-columns: 1fr; }
        .ppi-collection-card { max-width: 420px; }
    }
    @media (max-width: 680px) {
        .ppi-hero { align-items: flex-start; flex-direction: column; }
        .ppi-kpis, .ppi-grid-2, .ppi-grid-3, .ppi-filter-grid { grid-template-columns: 1fr; }
        .ppi-actions > * { width: 100%; justify-content: center; text-align: center; }
        .ppi-table th:nth-child(3), .ppi-table td:nth-child(3), .ppi-table th:nth-child(5), .ppi-table td:nth-child(5) { display: none; }
        .ppi-receipt-item { grid-template-columns: 1fr; }
        .ppi-receipt-item .ppi-actions { justify-content: flex-start; }
        .ppi-index-hero { min-height: 0; padding: 25px 21px; }
        .ppi-index-hero h2 { font-size: 34px; }
        .ppi-index-hero__actions > * { width: 100%; justify-content: center; }
        .ppi-collection-card { max-width: none; }
        .ppi-index-page .ppi-kpis, .ppi-index-page .ppi-filter-grid { grid-template-columns: 1fr; }
        .ppi-index-page .ppi-kpi { min-height: 112px; }
        .ppi-filter-actions > * { flex: 1; }
        .ppi-toolbar__head, .ppi-table-heading { align-items: flex-start; }
        .ppi-table-heading > a { display: none; }
    }
</style>
