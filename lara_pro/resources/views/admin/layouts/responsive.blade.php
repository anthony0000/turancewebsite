/* Shared screen layout. Keep this after the product skin so desktop rules
 * cannot reinstate columns at smaller breakpoints. PDF layouts are separate. */
body.is-admin .admin-pagebar,
body.is-admin:not(.is-dashboard-overview) .admin-pagebar {
    width: auto;
}

body.is-admin .admin-main :where(
    .panel, .pm-shell, .pm-panel, .pm-grid, .pm-grid-wide, .pm-detail-grid,
    .ppi-shell, .ppi-form-layout, .ppi-detail-grid, .ppi-receipt-grid,
    .proposal-builder-shell, .proposal-preview-shell, .staff-contract-preview-shell,
    .letter-workspace, .form-grid, .pm-form-grid, .credential-form-grid,
    .proposal-form-grid, .ppi-grid-2, .ppi-grid-3, .sticky-stack
),
body.is-admin .admin-main :where(
    .preview-grid, .dashboard-grid, .pm-shell, .pm-grid, .pm-grid-wide, .pm-detail-grid,
    .ppi-form-layout, .ppi-detail-grid, .ppi-receipt-grid,
    .proposal-builder-shell, .proposal-preview-shell, .staff-contract-preview-shell,
    .form-grid, .pm-form-grid, .credential-form-grid, .proposal-form-grid,
    .ppi-grid-2, .ppi-grid-3, .sticky-stack, .letter-workspace
) > * {
    min-width: 0;
}

body.is-admin :where(input, select, textarea) {
    min-width: 0;
    max-width: 100%;
}

body.is-admin :where(.field, .field-full, .pm-list-item > div, .credential-project-card) {
    min-width: 0;
    overflow-wrap: anywhere;
}

/* Wide records and document sheets scroll inside their own workspace. */
body.is-admin .admin-main :where(
    .table-wrap, .pm-table-wrap, .pm-task-overview-table, .pm-board,
    .document-stage, .proposal-document-stage, .proposal-preview-stage,
    .staff-contract-document-stage, .letter-preview-stage, .ppi-document-stage
) {
    min-width: 0;
    max-width: 100%;
    overflow-x: auto;
    overscroll-behavior-x: contain;
}

body.is-admin :where(.action-menu-panel, .admin-profile-panel) {
    max-width: calc(100vw - 28px);
    max-height: calc(100dvh - 28px);
    overflow-y: auto;
}

body.is-admin .admin-mobile-nav-close { display: none; }

@media (max-width: 1240px) {
    body.is-admin .admin-main :where(.pm-backlog-row, .fm-file-row) {
        grid-template-columns: minmax(0, 1fr);
        gap: 12px;
    }

    body.is-admin :where(.pm-backlog-row--head, .fm-file-row--head) { display: none; }
    body.is-admin .fm-file-cell > small { display: block; }
    body.is-admin .fm-file-row .fm-file-cell { display: block; }
    body.is-admin .fm-file-actions { justify-content: flex-start; }
    body.is-admin .fm-file-actions > .project-file-edit .project-file-edit__form {
        right: auto;
        left: 0;
        max-width: calc(100vw - 64px);
    }

    body.is-admin .pm-backlog-cell:not(.pm-backlog-cell--task) {
        display: grid;
        grid-template-columns: 105px minmax(0, 1fr);
        gap: 10px;
        align-items: center;
    }

    body.is-admin .pm-backlog-cell:not(.pm-backlog-cell--task)::before {
        content: attr(data-label);
        color: var(--muted);
        font-size: 11px;
    }
}

@media (max-width: 1100px) {
    /* Match the drawer breakpoint, including 981–1100px tablets. */
    body.is-admin .admin-workspace--with-sidebar {
        grid-template-columns: minmax(0, 1fr);
    }

    body.is-admin .admin-sidebar {
        visibility: hidden;
        pointer-events: none;
    }

    body.is-admin.is-mobile-nav-open { overflow: hidden; }
    body.is-admin.is-mobile-nav-open .admin-sidebar {
        visibility: visible;
        pointer-events: auto;
    }

    body.is-admin .admin-mobile-nav-close {
        display: inline-grid;
        flex: 0 0 40px;
        width: 40px;
        height: 40px;
        font-size: 24px;
    }

    body.is-admin .admin-main :where(.pm-subnav, .pm-tabs) {
        flex-wrap: wrap;
        overflow: visible;
    }

    body.is-admin .pm-subnav-more__items {
        position: static;
        margin-top: 6px;
        max-width: calc(100vw - 56px);
    }
}

@media (max-width: 900px) {
    body.is-admin .admin-main :where(.pm-task-hero, .ppi-filter-bar, .ppi-filter-grid) {
        grid-template-columns: minmax(0, 1fr);
    }

    body.is-admin .pm-task-hero__actions { min-width: 0; }

    body.is-admin .admin-main > .hero-banner,
    body.is-admin:not(.is-dashboard-overview) .admin-main > .hero-banner,
    body.is-admin .admin-main > .page-header,
    body.is-admin:not(.is-dashboard-overview) .admin-main > .page-header {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (max-width: 767px) {
    /* Preserve access to search and page actions that the old skin hid. */
    body.is-admin .admin-pagebar,
    body.is-admin:not(.is-dashboard-overview) .admin-pagebar {
        flex-wrap: wrap;
        gap: 10px;
    }

    body.is-admin .admin-pagebar-title { flex: 1 1 100%; }
    body.is-admin .admin-pagebar-actions,
    body.is-admin:not(.is-dashboard-overview) .admin-pagebar-actions {
        width: 100%;
        flex: 1 1 100%;
        flex-wrap: wrap;
        justify-content: flex-start;
        gap: 8px;
    }

    body.is-admin .admin-pagebar-actions .admin-search-trigger,
    body.is-admin .admin-pagebar-actions > .button,
    body.is-admin .admin-pagebar-actions > .ghost-button { display: inline-flex; }

    body.is-admin .admin-pagebar-actions .admin-search-trigger,
    body.is-admin:not(.is-dashboard-overview) .admin-pagebar-actions .admin-search-trigger {
        width: 40px;
        min-width: 40px;
        flex: 0 0 40px;
        padding: 0;
        justify-content: center;
    }

    body.is-admin .admin-search-trigger :where(span, kbd) { display: none; }
    body.is-admin .admin-pagebar-actions .admin-profile-menu { margin-left: auto; }

    body.is-admin .admin-main :where(
        .form-grid, .wizard-pane-grid, .review-grid, .sticky-stack,
        .proposal-form-grid,
        .pm-grid-wide, .pm-form-grid, .ppi-grid-2, .ppi-grid-3,
        .ppi-form-layout, .ppi-detail-grid, .ppi-receipt-grid,
        .line-item-row, .line-items-currency-grid
    ) { grid-template-columns: minmax(0, 1fr); }

    body.is-admin .admin-main :where(
        .panel-head--row, .pm-panel-head, .pm-list-item, .ppi-dashboard-header,
        .ppi-invoice-topbar, .proposal-builder-toolbar, .credential-project-card__footer
    ) { flex-wrap: wrap; }

    body.is-admin .admin-main :where(.hero-actions, .pm-actions, .form-actions, .table-actions) {
        flex-wrap: wrap;
        max-width: 100%;
    }

    body.is-admin .admin-main :where(.button, .ghost-button) {
        max-width: 100%;
        white-space: normal;
        overflow-wrap: anywhere;
        text-align: center;
    }

    body.is-admin .admin-main .pm-create-aside {
        position: static;
    }

    body.is-admin .admin-main :where(.document-stage, .proposal-document-stage, .staff-contract-document-stage) {
        padding: 14px;
    }

    body.is-admin .document-frame { min-width: 700px; }
    body.is-admin .document-frame::before { display: none; }

    body.is-admin .fm-pagination nav > div:last-child > div:last-child {
        max-width: 100%;
        overflow-x: auto;
    }

    body.is-admin .fm-file-actions > .project-file-edit .project-file-edit__form { left: -54px; }
    body.is-admin .fm-more-menu__panel { left: auto; right: 0; }
}

@media (max-width: 560px) {
    body.is-admin .admin-main .tt-metric-band,
    body.is-admin .admin-main .kpi-grid,
    body.is-admin:not(.is-dashboard-overview) .kpi-grid {
        grid-template-columns: minmax(0, 1fr);
    }

    body.is-admin .admin-main :where(.pm-hero, .hero-banner, .page-header) {
        padding: 18px;
        gap: 16px;
    }

    body.is-admin .pm-backlog-cell:not(.pm-backlog-cell--task) { grid-template-columns: minmax(0, 1fr); }
    body.is-admin .pm-backlog-assign { flex-wrap: wrap; }
    body.is-admin .pm-backlog-assign select { flex-basis: 100%; }

    /* Avoid automatic zoom when focusing form fields on mobile Safari. */
    body.is-admin .admin-main :is(.field, .field-full, .proposal-field) :is(input, select, textarea),
    body.is-admin .admin-main .rich-editor-body { font-size: 16px; }
}

@media (prefers-reduced-motion: reduce) {
    body.is-admin .admin-sidebar { transition: none; }
}
