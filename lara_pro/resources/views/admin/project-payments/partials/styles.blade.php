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
    .ppi-row-action { display: grid !important; width: 34px; height: 34px; place-items: center; border: 1px solid var(--line); border-radius: 9px; background: #fff; color: #59606a !important; transition: border-color .16s ease, background .16s ease, color .16s ease; }
    .ppi-row-action:hover { border-color: #d3b15d; background: #fbf6e9; color: #8e6a16 !important; }
    .ppi-row-action svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
    body.is-admin .ppi-dashboard { gap: 18px; }
    body.is-admin .ppi-dashboard-header { display: flex; align-items: flex-end; justify-content: space-between; gap: 28px; padding: 5px 2px 21px; border-bottom: 1px solid #e7e9ec; }
    body.is-admin .ppi-dashboard-header h2 { margin: 5px 0 6px; color: #17191d; font-family: var(--font-display); font-size: clamp(28px, 3vw, 38px); font-weight: 600; letter-spacing: -.045em; line-height: 1.05; }
    body.is-admin .ppi-dashboard-header p { max-width: 680px; margin: 0; color: #747b85; font-size: 13px; line-height: 1.55; }
    body.is-admin .ppi-dashboard-header__actions { flex: 0 0 auto; }
    body.is-admin .ppi-dashboard-header__actions a { min-height: 40px; padding-inline: 15px; border-radius: 8px; }
    body.is-admin .ppi-dashboard-header__actions svg { width: 17px; height: 17px; fill: none; stroke: currentColor; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
    body.is-admin .ppi-summary-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
    body.is-admin .admin-main .ppi-summary-card.panel { position: relative; min-height: 132px; padding: 18px; overflow: hidden; border-color: #e5e8eb; border-radius: 11px; background: #fff; box-shadow: 0 1px 1px rgba(17, 24, 39, .02); }
    body.is-admin .admin-main .ppi-summary-card--collected.panel { border-color: #ded7c4; background: #fcfaf5; }
    .ppi-summary-card__top { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .ppi-summary-card__top > span:first-child { color: #747b85; font-size: 10px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
    .ppi-summary-icon { display: grid; width: 31px; height: 31px; place-items: center; border-radius: 8px; background: #f0f2f4; color: #69717c; }
    .ppi-summary-icon--amber { background: #fbf2df; color: #a87017; }
    .ppi-summary-icon--green { background: #e9f6ef; color: #217a4e; }
    .ppi-summary-icon svg { width: 16px; height: 16px; fill: none; stroke: currentColor; stroke-width: 1.7; stroke-linecap: round; stroke-linejoin: round; }
    body.is-admin .ppi-summary-card > strong { display: block; margin-top: 11px; overflow: hidden; color: #17191d; font-size: clamp(22px, 2vw, 29px); font-weight: 600; letter-spacing: -.035em; line-height: 1; text-overflow: ellipsis; white-space: nowrap; }
    .ppi-summary-card__foot { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 12px; color: #858b94; font-size: 10px; }
    .ppi-summary-card__foot b { color: #555d68; font-weight: 600; }
    .ppi-summary-progress { height: 3px; margin: 11px -18px -18px; overflow: hidden; background: #eee9dc; }
    .ppi-summary-progress span { display: block; height: 100%; border-radius: 999px; background: #2e9161; }
    body.is-admin .admin-main .ppi-register.panel { overflow: hidden; border-color: #e5e8eb; border-radius: 11px; background: #fff; }
    .ppi-register__head { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 18px 20px 14px; }
    .ppi-register__head h3 { margin: 3px 0 0; color: #20242a; font-size: 18px; letter-spacing: -.02em; }
    .ppi-register__count { padding: 5px 9px; border-radius: 999px; background: #f1f3f5; color: #69717c; font-size: 10px; font-weight: 700; }
    .ppi-filter-bar { display: grid; grid-template-columns: minmax(260px, 1fr) minmax(150px, .25fr) minmax(210px, .42fr) auto auto; gap: 10px; padding: 13px 20px; border-top: 1px solid #eef0f2; border-bottom: 1px solid #e8ebee; background: #fafbfc; }
    .ppi-filter-search, .ppi-filter-select { position: relative; }
    .ppi-filter-search svg { position: absolute; top: 50%; left: 12px; width: 16px; height: 16px; transform: translateY(-50%); fill: none; stroke: #858c96; stroke-width: 1.8; stroke-linecap: round; }
    body.is-admin .ppi-filter-bar input, body.is-admin .ppi-filter-bar select { width: 100%; min-height: 39px; border: 1px solid #dde1e5; border-radius: 7px; background-color: #fff; color: #252a31; font-size: 12px; }
    body.is-admin .ppi-filter-bar input { padding-left: 38px; }
    body.is-admin .ppi-filter-bar input:focus, body.is-admin .ppi-filter-bar select:focus { border-color: #c9a34c; box-shadow: 0 0 0 3px rgba(201, 163, 76, .1); }
    body.is-admin .ppi-filter-bar .button { min-height: 39px; padding-inline: 17px; border-radius: 7px; }
    .ppi-clear-filter { display: inline-flex; min-height: 39px; align-items: center; padding-inline: 7px; color: #69717c; font-size: 11px; font-weight: 650; text-decoration: none; }
    .ppi-clear-filter:hover { color: #8a6818; }
    .sr-only { position: absolute !important; width: 1px !important; height: 1px !important; padding: 0 !important; margin: -1px !important; overflow: hidden !important; clip: rect(0, 0, 0, 0) !important; white-space: nowrap !important; border: 0 !important; }
    .ppi-register-table th { padding: 11px 18px; background: #fff; color: #7d848e; font-size: 9px; }
    .ppi-register-table td { padding: 16px 18px; }
    .ppi-register-table tbody tr { transition: background .15s ease; }
    .ppi-register-table tbody tr:hover { background: #fdfbf7; }
    .ppi-register-table .ppi-status { border: 1px solid rgba(148,102,0,.11); }
    .ppi-register-table .ppi-status--paid { border-color: rgba(21,115,71,.11); }
    @media (max-width: 1050px) {
        .ppi-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ppi-form-layout, .ppi-detail-grid, .ppi-receipt-grid { grid-template-columns: 1fr; }
        .ppi-live-card { position: static; }
        .ppi-filter-grid { grid-template-columns: 1fr 1fr; }
        body.is-admin .ppi-summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ppi-filter-bar { grid-template-columns: minmax(240px, 1fr) minmax(140px, .4fr) minmax(190px, .6fr) auto; }
        .ppi-clear-filter { grid-column: 1 / -1; min-height: auto; padding: 0; }
    }
    @media (max-width: 680px) {
        .ppi-hero { align-items: flex-start; flex-direction: column; }
        .ppi-kpis, .ppi-grid-2, .ppi-grid-3, .ppi-filter-grid { grid-template-columns: 1fr; }
        .ppi-actions > * { width: 100%; justify-content: center; text-align: center; }
        .ppi-table th:nth-child(3), .ppi-table td:nth-child(3), .ppi-table th:nth-child(5), .ppi-table td:nth-child(5) { display: none; }
        .ppi-receipt-item { grid-template-columns: 1fr; }
        .ppi-receipt-item .ppi-actions { justify-content: flex-start; }
        body.is-admin .ppi-dashboard-header { align-items: flex-start; flex-direction: column; }
        body.is-admin .ppi-dashboard-header__actions { width: 100%; }
        body.is-admin .ppi-dashboard-header__actions > * { flex: 1; }
        body.is-admin .ppi-summary-grid { grid-template-columns: 1fr; }
        .ppi-filter-bar { grid-template-columns: 1fr; }
        body.is-admin .ppi-filter-bar .button { width: 100%; }
    }
</style>
