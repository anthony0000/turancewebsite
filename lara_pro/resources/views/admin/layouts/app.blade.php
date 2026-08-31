@php
    $adminSessionKey = config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated');
    $brandName = config('luxury-quotes.brand.studio_name', 'Turance Technologies');
    $isAuthenticated = (bool) session($adminSessionKey);
    $isInvoicePreview = request()->routeIs('admin.quotes.show');
    $isInvoiceEditor = request()->routeIs('admin.quotes.edit');
    $isProposalDashboard = request()->routeIs('admin.proposals.index');
    $isProposalPreview = request()->routeIs('admin.proposals.show');
    $isProposalEditor = request()->routeIs('admin.proposals.edit');
    $isProposalWorkspace = request()->routeIs('admin.proposals.*');
    $isStaffContractDashboard = request()->routeIs('admin.staff-contracts.index');
    $isStaffContractPreview = request()->routeIs('admin.staff-contracts.show');
    $isStaffContractEditor = request()->routeIs('admin.staff-contracts.edit');
    $isStaffContractBuilder = request()->routeIs('admin.staff-contracts.create');
    $isStaffContractWorkspace = request()->routeIs('admin.staff-contracts.*');
    $isProjectDashboard = request()->routeIs('admin.projects.index');
    $isProjectPreview = request()->routeIs('admin.projects.show');
    $isProjectFileWorkspace = request()->routeIs('admin.projects.*');
    $isProjectManagementWorkspace = request()->routeIs('admin.project-management.*');
    $isLetterBuilder = request()->routeIs('admin.letters.create');
    $isLetterWorkspace = request()->routeIs('admin.letters.*');
    $isAdminProfile = request()->routeIs('admin.profile');
    $isSubaccountWorkspace = request()->routeIs('admin.subaccounts.*');
    $canInvoices = \App\Support\AdminAccess::can('invoices');
    $canProposals = \App\Support\AdminAccess::can('proposals');
    $canStaffContracts = \App\Support\AdminAccess::can('staff-contracts');
    $canProjects = \App\Support\AdminAccess::can('projects');
    $canProjectManagement = \App\Support\AdminAccess::can('project-management');
    $canLetters = \App\Support\AdminAccess::can('letters');
    $canActivity = \App\Support\AdminAccess::can('activity');
    $canInsights = \App\Support\AdminAccess::can('insights');
    $canPromotion = \App\Support\AdminAccess::can('promotion');
    $canArchive = \App\Support\AdminAccess::can('archive');
    $isFullAdmin = \App\Support\AdminAccess::isFullAdmin();
    $isQuoteActivity = request()->routeIs('admin.quotes.activity');
    $isQuoteInsights = request()->routeIs('admin.quotes.insights');
    $isQuotePromotion = request()->routeIs('admin.quotes.promotion');
    $isQuoteBuilder = request()->routeIs('admin.quotes.create');
    $isQuoteArchive = request()->routeIs('admin.quotes.archive');
    $currentAdminView = match (true) {
        $isInvoicePreview => 'Invoice Preview',
        $isInvoiceEditor => 'Edit Invoice',
        $isProposalPreview => 'Proposal Preview',
        $isProposalEditor => 'Edit Proposal',
        $isProposalDashboard => 'Proposal Studio',
        $isStaffContractPreview => 'Staff Contract Preview',
        $isStaffContractEditor => 'Edit Staff Contract',
        $isProjectManagementWorkspace && request()->routeIs('admin.project-management.projects*') && ! \App\Support\ProjectManagementAccess::canManageWorkspace() => 'Projects',
        $isProjectManagementWorkspace && ! \App\Support\ProjectManagementAccess::canManageWorkspace() => 'My tasks',
        $isProjectManagementWorkspace => 'Project Management',
        $isProjectPreview => 'Project Workspace',
        $isProjectDashboard => 'File sharing',
        $isLetterBuilder => 'Letter Generator',
        $isSubaccountWorkspace => 'Staff accounts',
        $isAdminProfile => 'Profile & Settings',
        $isStaffContractDashboard => 'Staff Contracts',
        $isStaffContractBuilder => 'Staff Contract Builder',
        $isQuoteActivity => 'Activity Center',
        $isQuoteInsights => 'Business Insights',
        $isQuotePromotion => 'Promotion Control',
        $isQuoteBuilder => 'Invoice Builder',
        $isQuoteArchive => 'Invoice Archive',
        default => 'Analytics Dashboard',
    };
    $currentAdminHint = match (true) {
        $isInvoicePreview => 'Review invoice details, inspect the layout, and export the PDF or MOU when everything looks right.',
        $isInvoiceEditor => 'Edit saved invoice details, return to preview, and regenerate the PDF or MOU from the updated record.',
        $isProposalPreview => 'Review the complete proposal, share it online, or export the PDF, Word, and printable versions.',
        $isProposalEditor => 'Edit proposal sections, styling, pricing, timeline, and team content before exporting again.',
        $isProposalDashboard => 'Build, review, and export client proposals.',
        $isStaffContractPreview => 'Review the project-bound staff agreement, signing details, and contract PDF.',
        $isStaffContractEditor => 'Update the project, commercial terms, staff details, and signing section.',
        $isProjectManagementWorkspace && request()->routeIs('admin.project-management.projects*') && ! \App\Support\ProjectManagementAccess::canManageWorkspace() => 'Review the project records available to your account.',
        $isProjectManagementWorkspace && ! \App\Support\ProjectManagementAccess::canManageWorkspace() => 'Review the tasks assigned to you and open the relevant project file sharing area.',
        $isProjectManagementWorkspace => 'Plan projects, move work across the board, and keep delivery visible to the team.',
        $isProjectPreview => 'Keep project files private by default, then create a secure link for the right people.',
        $isProjectDashboard => 'Review project health, file activity, and the files attached to each engagement.',
        $isLetterBuilder => 'Write letters and basic company documents on the Turance letterhead, then export them as PDF.',
        $isSubaccountWorkspace => 'Create staff accounts and limit the parts of the admin workspace they can access.',
        $isAdminProfile => 'Manage your admin identity, contact details, and account security.',
        $isStaffContractDashboard => 'Track project-bound staff agreements and export signed-ready contract documents.',
        $isStaffContractBuilder => 'Create a staff agreement with a required project, price, terms, and signing section.',
        $isQuoteActivity => 'Review traffic, leads, and invoice movement.',
        $isQuoteInsights => 'Understand template, category, and pipeline patterns.',
        $isQuotePromotion => 'Manage the live anniversary discount and landing-page campaign.',
        $isQuoteBuilder => 'Create an invoice through a focused step-by-step flow.',
        $isQuoteArchive => 'Find saved invoices and export documents.',
        default => 'Track demand and create the next invoice.',
    };
    $adminPageTitle = match (true) {
        $isInvoicePreview => 'Invoice Preview',
        $isInvoiceEditor => 'Edit Invoice',
        $isProposalPreview => 'Proposal Preview',
        $isProposalEditor => 'Edit Proposal',
        $isProposalDashboard => 'Proposal Studio',
        $isStaffContractPreview => 'Staff Contract Preview',
        $isStaffContractEditor => 'Edit Staff Contract',
        $isProjectManagementWorkspace && request()->routeIs('admin.project-management.projects*') && ! \App\Support\ProjectManagementAccess::canManageWorkspace() => 'Projects',
        $isProjectManagementWorkspace && ! \App\Support\ProjectManagementAccess::canManageWorkspace() => 'My tasks',
        $isProjectManagementWorkspace => 'Project Management',
        $isProjectPreview => 'Project Workspace',
        $isProjectDashboard => 'File sharing',
        $isLetterBuilder => 'Letter Generator',
        $isSubaccountWorkspace => 'Staff accounts',
        $isAdminProfile => 'Profile & Settings',
        $isStaffContractDashboard => 'Staff Contracts',
        $isStaffContractBuilder => 'Staff Contract Builder',
        $isQuoteActivity => 'Activity',
        $isQuoteInsights => 'Insights',
        $isQuotePromotion => 'Promotion Control',
        $isQuoteBuilder => 'Invoice Builder',
        $isQuoteArchive => 'Archive',
        default => 'Dashboard',
    };
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap"
        rel="stylesheet">
    <title>@yield('title', 'Invoice Generator')</title>
    <style>
        :root {
            --font-sans: "Manrope", "Aptos", "Segoe UI Variable Text", "Segoe UI", ui-sans-serif, system-ui, sans-serif;
            --font-display: "Sora", "Manrope", "Aptos Display", "Segoe UI Variable Display", ui-sans-serif, system-ui, sans-serif;
            --bg: #fff8ea;
            --panel: rgba(255, 255, 255, 0.94);
            --panel-soft: rgba(184, 134, 11, 0.07);
            --text: #24190a;
            --muted: #786a57;
            --line: rgba(184, 134, 11, 0.22);
            --accent: #b8860b;
            --accent-soft: #8f6508;
            --traffic: #d4af37;
            --quote: #b8860b;
            --lead: #c08a4a;
            --pipeline: #6f5015;
            --success: #2f8054;
            --warning: #b8860b;
            --danger: #b94a3d;
            --shadow: 0 24px 70px rgba(102, 76, 20, 0.14);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(216, 179, 106, 0.22), transparent 28%),
                radial-gradient(circle at top right, rgba(255, 245, 220, 0.92), transparent 30%),
                radial-gradient(circle at bottom right, rgba(184, 134, 11, 0.14), transparent 26%),
                linear-gradient(180deg, #fffdf8 0%, #fff8eb 44%, #fff4df 100%);
            color: var(--text);
            font-family: var(--font-sans);
            font-size: 15px;
            letter-spacing: 0;
            text-rendering: optimizeLegibility;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        .admin-shell {
            max-width: 1600px;
            margin: 0 auto;
            padding: 24px;
        }

        .admin-shell--auth {
            min-height: 100vh;
            display: grid;
            align-items: center;
            max-width: 1180px;
            padding: 32px;
        }

        .admin-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
            padding: 18px 20px;
            border: 1px solid var(--line);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(20px);
            box-shadow: var(--shadow);
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .admin-brand-mark {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 16px;
            background: linear-gradient(135deg, #fff0ca, #d4af37);
            border: 1px solid rgba(184, 134, 11, 0.3);
            color: #4b3200;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .admin-brand-copy strong {
            display: block;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0;
        }

        .admin-brand-copy span {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .admin-topbar-actions {
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 12px;
        }

        .admin-pill {
            display: inline-flex;
            align-items: center;
            min-height: 38px;
            padding: 0 14px;
            border-radius: 999px;
            border: 1px solid rgba(216, 179, 106, 0.22);
            background: rgba(216, 179, 106, 0.08);
            color: var(--accent-soft);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .button,
        button.button,
        .ghost-button,
        button.ghost-button {
            appearance: none;
            border: 0;
            cursor: pointer;
            font: inherit;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 46px;
            padding: 0 18px;
            border-radius: 16px;
            background: linear-gradient(135deg, #f3c85b, #b8860b);
            color: #2b1a00;
            font-weight: 700;
            box-shadow: 0 16px 32px rgba(184, 134, 11, 0.2);
        }

        .ghost-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0 16px;
            border-radius: 15px;
            border: 1px solid var(--line);
            background: rgba(184, 134, 11, 0.05);
            color: var(--text);
        }

        .admin-workspace {
            display: grid;
            gap: 24px;
        }

        .admin-workspace--with-sidebar {
            grid-template-columns: 280px minmax(0, 1fr);
            align-items: start;
        }

        .admin-main {
            min-width: 0;
            display: grid;
            gap: 24px;
        }

        .admin-sidebar {
            position: sticky;
            top: 24px;
            padding: 22px;
            border: 1px solid var(--line);
            border-radius: 28px;
            background: var(--panel);
            backdrop-filter: blur(16px);
            box-shadow: var(--shadow);
        }

        .admin-nav {
            display: grid;
            gap: 10px;
            margin-top: 18px;
        }

        .admin-nav-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 15px;
            border: 1px solid transparent;
            border-radius: 18px;
            background: rgba(184, 134, 11, 0.05);
            color: var(--muted);
            transition: border-color 0.2s ease, background 0.2s ease, color 0.2s ease;
        }

        .admin-nav-link strong {
            display: block;
            color: var(--text);
            font-size: 15px;
        }

        .admin-nav-link span {
            display: block;
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .admin-nav-link.active,
        .admin-nav-link:hover {
            border-color: rgba(216, 179, 106, 0.22);
            background: rgba(216, 179, 106, 0.08);
            color: var(--accent-soft);
        }

        .admin-sidebar-note {
            margin-top: 20px;
            padding: 18px;
            border-radius: 22px;
            border: 1px solid var(--line);
            background: rgba(184, 134, 11, 0.05);
        }

        .admin-sidebar-note strong {
            display: block;
            margin-bottom: 8px;
            font-size: 18px;
            letter-spacing: 0;
        }

        .admin-sidebar-note p {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .panel {
            border: 1px solid var(--line);
            border-radius: 28px;
            background: var(--panel);
            backdrop-filter: blur(16px);
            box-shadow: var(--shadow);
        }

        .panel-padded {
            padding: 24px;
        }

        .eyebrow {
            display: inline-block;
            margin-bottom: 12px;
            color: var(--accent-soft);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .page-header,
        .hero-banner {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(280px, 0.75fr);
            gap: 22px;
            padding: 28px;
        }

        .page-header h1,
        .hero-banner h1,
        .panel-title,
        .panel-head h2,
        .auth-hero h1 {
            margin: 0;
            font-family: var(--font-sans);
            font-weight: 700;
            letter-spacing: 0;
        }

        .page-header h1,
        .hero-banner h1 {
            font-size: 42px;
            line-height: 0.98;
        }

        .page-header p,
        .hero-banner p,
        .panel-copy,
        .field-hint,
        .panel-head p,
        .auth-hero p {
            color: var(--muted);
            line-height: 1.7;
        }

        .page-header-aside,
        .hero-callout {
            display: grid;
            gap: 14px;
        }

        .status-card,
        .callout-card,
        .metric-card,
        .mini-card,
        .kpi-card,
        .highlight-card,
        .template-card {
            border-radius: 22px;
            border: 1px solid var(--line);
            background: var(--panel-soft);
        }

        .status-card,
        .callout-card,
        .metric-card,
        .mini-card,
        .kpi-card,
        .highlight-card {
            padding: 18px;
        }

        .status-card strong,
        .callout-card strong,
        .metric-card strong,
        .mini-card strong,
        .highlight-card strong {
            display: block;
            font-size: 22px;
            letter-spacing: 0;
        }

        .status-card p,
        .callout-card p,
        .metric-card p,
        .mini-card p,
        .highlight-card p {
            margin: 8px 0 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .hero-actions,
        .action-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 22px;
        }

        .metric-label {
            display: block;
            margin-bottom: 10px;
            color: var(--muted);
            font-size: 12px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .kpi-card {
            position: relative;
            overflow: hidden;
            display: grid;
            gap: 12px;
        }

        .kpi-card::before {
            content: "";
            position: absolute;
            inset: 0 auto auto 0;
            width: 100%;
            height: 3px;
            background: rgba(184, 134, 11, 0.16);
        }

        .kpi-card--traffic::before {
            background: var(--traffic);
        }

        .kpi-card--quotes::before {
            background: var(--quote);
        }

        .kpi-card--leads::before {
            background: var(--lead);
        }

        .kpi-card--pipeline::before {
            background: var(--pipeline);
        }

        .kpi-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .kpi-value {
            display: block;
            font-size: 38px;
            line-height: 0.96;
            letter-spacing: 0;
        }

        .kpi-context {
            color: var(--muted);
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .trend-pill {
            display: inline-flex;
            align-items: center;
            min-height: 28px;
            padding: 0 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .trend-pill--up {
            background: rgba(142, 183, 154, 0.14);
            color: var(--success);
        }

        .trend-pill--down {
            background: rgba(216, 153, 135, 0.14);
            color: var(--danger);
        }

        .trend-pill--flat {
            background: rgba(184, 134, 11, 0.08);
            color: var(--muted);
        }

        .analytics-grid,
        .dashboard-grid,
        .preview-grid,
        .auth-grid,
        .insight-grid {
            display: grid;
            gap: 24px;
        }

        .analytics-grid,
        .preview-grid,
        .dashboard-grid {
            grid-template-columns: minmax(0, 1.65fr) minmax(320px, 0.75fr);
        }

        .auth-grid {
            min-height: min(720px, calc(100vh - 64px));
            grid-template-columns: minmax(0, 0.95fr) minmax(380px, 0.72fr);
            align-items: stretch;
        }

        .auth-brand-panel,
        .auth-card {
            border: 1px solid var(--line);
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .auth-brand-panel {
            display: grid;
            align-content: space-between;
            gap: 48px;
            min-height: 620px;
            padding: 42px;
            border-radius: 28px;
            overflow: hidden;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(255, 248, 232, 0.9)),
                radial-gradient(circle at 82% 18%, rgba(212, 175, 55, 0.28), transparent 34%);
        }

        .auth-brand-lockup {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .auth-brand-copy strong {
            display: block;
            font-size: 20px;
        }

        .auth-brand-copy span {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .auth-hero-copy {
            max-width: 620px;
        }

        .auth-hero-copy h1 {
            max-width: 680px;
            margin: 0;
            font-size: 64px;
            line-height: 0.98;
            letter-spacing: 0;
        }

        .auth-hero-copy p {
            max-width: 540px;
            margin: 22px 0 0;
            color: var(--muted);
            font-size: 17px;
            line-height: 1.75;
        }

        .auth-meta-strip {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            border-top: 1px solid var(--line);
        }

        .auth-meta-item {
            padding: 20px 18px 0 0;
        }

        .auth-meta-item strong {
            display: block;
            font-size: 28px;
            color: var(--accent-soft);
        }

        .auth-meta-item span {
            display: block;
            margin-top: 6px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.5;
        }

        .auth-card {
            display: grid;
            align-content: center;
            padding: 38px;
            border-radius: 28px;
        }

        .auth-card-head {
            margin-bottom: 26px;
        }

        .auth-card-head h2 {
            margin: 0;
            font-size: 34px;
            line-height: 1.05;
            letter-spacing: 0;
        }

        .auth-card-head p {
            margin: 12px 0 0;
            color: var(--muted);
            line-height: 1.65;
        }

        .auth-form {
            display: grid;
            gap: 18px;
        }

        .auth-form .button {
            width: 100%;
            min-height: 52px;
        }

        .auth-support-note {
            margin: 18px 0 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .insight-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .sticky-stack {
            display: grid;
            gap: 18px;
            align-content: start;
        }

        .sticky-stack .panel:first-child {
            position: sticky;
            top: 24px;
        }

        .panel-head {
            margin-bottom: 20px;
        }

        .panel-head h2 {
            font-size: 32px;
            line-height: 1.04;
        }

        .panel-title {
            font-size: 22px;
        }

        .section-heading {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 18px;
            margin-bottom: 18px;
        }

        .section-heading h2 {
            margin: 0;
            font-size: 28px;
            line-height: 1;
            letter-spacing: 0;
        }

        .section-heading p {
            max-width: 720px;
            margin: 10px 0 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .chart-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(184, 134, 11, 0.05);
            color: var(--muted);
            font-size: 12px;
        }

        .legend-swatch {
            width: 10px;
            height: 10px;
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

        .line-chart-shell,
        .mini-chart {
            margin-top: 18px;
            padding: 18px;
            border-radius: 24px;
            border: 1px solid var(--line);
            background:
                linear-gradient(180deg, rgba(255, 248, 232, 0.88), rgba(255, 255, 255, 0.9)),
                rgba(255, 255, 255, 0.72);
        }

        .line-chart {
            width: 100%;
            height: auto;
            display: block;
        }

        .chart-grid-line {
            stroke: rgba(184, 134, 11, 0.16);
            stroke-dasharray: 6 8;
        }

        .chart-line {
            fill: none;
            stroke-width: 3.5;
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
            gap: 12px;
            margin-top: 16px;
        }

        .chart-summary-grid .mini-card strong {
            font-size: 18px;
        }

        .bar-list,
        .stack-list,
        .feature-list,
        .mini-list,
        .activity-feed {
            display: grid;
            gap: 12px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .bar-row,
        .stack-list li,
        .mini-list li,
        .activity-item {
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px solid var(--line);
            background: rgba(184, 134, 11, 0.05);
        }

        .bar-header,
        .activity-item-header {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
        }

        .bar-header strong,
        .activity-item strong,
        .mini-list strong,
        .stack-list strong {
            display: block;
            margin-bottom: 4px;
            font-size: 15px;
        }

        .bar-meta,
        .activity-item span,
        .mini-list span,
        .stack-list span {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.5;
        }

        .bar-track {
            margin-top: 10px;
            height: 10px;
            overflow: hidden;
            border-radius: 999px;
            background: rgba(184, 134, 11, 0.12);
        }

        .bar-fill {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--accent), var(--traffic));
        }

        .bar-fill--quote {
            background: linear-gradient(90deg, var(--quote), #f4ddb0);
        }

        .bar-fill--lead {
            background: linear-gradient(90deg, var(--lead), #e7bea0);
        }

        .mini-chart {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            min-height: 240px;
        }

        .month-bar {
            flex: 1;
            min-width: 0;
            display: grid;
            justify-items: center;
            gap: 10px;
        }

        .month-bar-column {
            width: 100%;
            max-width: 48px;
            min-height: 16px;
            border-radius: 16px 16px 6px 6px;
            background: linear-gradient(180deg, var(--quote), var(--pipeline));
            box-shadow: 0 18px 30px rgba(102, 76, 20, 0.14);
        }

        .month-bar strong {
            font-size: 12px;
        }

        .month-bar span {
            color: var(--muted);
            font-size: 12px;
        }

        .data-note {
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px dashed rgba(216, 179, 106, 0.2);
            background: rgba(184, 134, 11, 0.04);
            color: var(--muted);
            line-height: 1.7;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid transparent;
            line-height: 1.6;
        }

        .alert-success {
            background: rgba(142, 183, 154, 0.12);
            border-color: rgba(142, 183, 154, 0.24);
            color: #24653f;
        }

        .alert-warning {
            background: rgba(240, 209, 141, 0.12);
            border-color: rgba(240, 209, 141, 0.24);
            color: #7c5700;
        }

        .alert-danger {
            background: rgba(216, 153, 135, 0.12);
            border-color: rgba(216, 153, 135, 0.24);
            color: #9f352f;
        }

        .alert ul {
            margin: 0;
            padding-left: 18px;
        }

        .quote-wizard {
            display: grid;
            gap: 24px;
        }

        .wizard-progress {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .wizard-progress-button {
            display: grid;
            gap: 10px;
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 20px;
            background: rgba(184, 134, 11, 0.04);
            color: var(--muted);
            text-align: left;
            transition: border-color 0.2s ease, background 0.2s ease, transform 0.2s ease, color 0.2s ease;
        }

        .wizard-progress-button:hover {
            transform: translateY(-1px);
        }

        .wizard-progress-button.is-active,
        .wizard-progress-button.is-complete {
            border-color: rgba(216, 179, 106, 0.24);
            background: rgba(216, 179, 106, 0.08);
            color: var(--accent-soft);
        }

        .wizard-progress-button.is-active {
            box-shadow: 0 18px 36px rgba(216, 179, 106, 0.12);
        }

        .wizard-progress-index {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 999px;
            border: 1px solid currentColor;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .wizard-progress-copy strong {
            display: block;
            margin-bottom: 4px;
            font-size: 15px;
            color: var(--text);
        }

        .wizard-progress-copy span {
            display: block;
            font-size: 12px;
            line-height: 1.5;
        }

        .wizard-pane {
            display: none;
            gap: 18px;
        }

        .wizard-pane.is-active {
            display: grid;
        }

        .wizard-pane-grid,
        .review-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .wizard-note,
        .review-card {
            padding: 18px;
            border-radius: 22px;
            border: 1px solid var(--line);
            background: rgba(184, 134, 11, 0.05);
        }

        .wizard-note strong,
        .review-card strong {
            display: block;
            margin-bottom: 8px;
            font-size: 18px;
            letter-spacing: 0;
        }

        .wizard-note p,
        .review-card p,
        .review-card span {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .review-card span {
            display: block;
            margin-top: 6px;
        }

        .review-list {
            display: grid;
            gap: 10px;
            margin: 12px 0 0;
            padding: 0;
            list-style: none;
        }

        .review-list li {
            padding: 12px 14px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(184, 134, 11, 0.04);
            color: var(--muted);
            line-height: 1.6;
        }

        .wizard-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding-top: 6px;
        }

        .wizard-actions-group {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .quote-edit-form {
            display: grid;
            gap: 26px;
        }

        .form-section {
            display: grid;
            gap: 16px;
            padding-bottom: 26px;
            border-bottom: 1px solid var(--line);
        }

        .form-section:last-of-type {
            padding-bottom: 0;
            border-bottom: 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .field,
        .field-full {
            display: grid;
            gap: 10px;
        }

        .field-full {
            grid-column: 1 / -1;
        }

        .field label,
        .field-full label {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--accent-soft);
        }

        .field input,
        .field select,
        .field textarea,
        .field-full input,
        .field-full select,
        .field-full textarea {
            width: 100%;
            padding: 15px 16px;
            border-radius: 16px;
            border: 1px solid rgba(216, 179, 106, 0.16);
            background: rgba(255, 255, 255, 0.86);
            color: var(--text);
        }

        .field textarea,
        .field-full textarea {
            min-height: 150px;
            resize: vertical;
        }

        .rich-editor-source {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0 0 0 0);
            clip-path: inset(50%);
            opacity: 0;
            pointer-events: none;
            white-space: nowrap;
        }

        .rich-editor {
            overflow: hidden;
            border: 1px solid rgba(216, 179, 106, 0.16);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.86);
        }

        .rich-editor.is-invalid {
            border-color: var(--danger);
            box-shadow: 0 0 0 3px rgba(185, 74, 61, 0.12);
        }

        .rich-editor-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 10px;
            border-bottom: 1px solid rgba(216, 179, 106, 0.16);
            background: rgba(184, 134, 11, 0.04);
        }

        .rich-editor-toolbar button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 32px;
            padding: 0 10px;
            border: 1px solid rgba(216, 179, 106, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.78);
            color: var(--text);
            cursor: pointer;
            font-size: 12px;
            font-weight: 700;
        }

        .rich-editor-toolbar button:hover,
        .rich-editor-toolbar button:focus-visible {
            border-color: rgba(184, 134, 11, 0.42);
            background: rgba(216, 179, 106, 0.16);
            outline: 0;
        }

        .rich-editor-body {
            min-height: 150px;
            padding: 15px 16px;
            color: var(--text);
            line-height: 1.65;
            white-space: pre-wrap;
        }

        .rich-editor-body:focus {
            outline: 0;
            box-shadow: inset 0 0 0 2px rgba(184, 134, 11, 0.18);
        }

        .rich-editor-body:empty::before {
            content: attr(data-placeholder);
            color: rgba(120, 106, 87, 0.72);
        }

        .rich-editor-body ul,
        .rich-editor-body ol {
            margin: 0;
            padding-left: 22px;
        }

        .rich-editor-feedback {
            display: none;
            margin-top: 8px;
            color: var(--danger);
            font-size: 12px;
            font-weight: 700;
        }

        .rich-editor.is-invalid + .rich-editor-feedback {
            display: block;
        }

        .line-items-editor {
            display: grid;
            gap: 14px;
            padding: 16px;
            border-radius: 18px;
            border: 1px solid var(--line);
            background: rgba(184, 134, 11, 0.04);
        }

        .line-items-editor-head,
        .line-items-total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .line-items-editor-head .field-hint {
            margin: 0;
        }

        .line-item-rows {
            display: grid;
            gap: 12px;
        }

        .line-item-row {
            display: grid;
            grid-template-columns: 44px minmax(0, 1fr) minmax(130px, 0.28fr) auto;
            align-items: end;
            gap: 12px;
            padding: 14px;
            border-radius: 16px;
            border: 1px solid rgba(184, 134, 11, 0.16);
            background: rgba(255, 255, 255, 0.62);
        }

        .line-item-index {
            display: inline-grid;
            place-items: center;
            width: 38px;
            height: 38px;
            margin-bottom: 6px;
            border-radius: 999px;
            border: 1px solid var(--line);
            color: var(--accent-soft);
            font-size: 12px;
            font-weight: 800;
        }

        .line-item-remove {
            min-height: 48px;
            align-self: end;
        }

        .line-items-total {
            padding: 14px 16px;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(243, 200, 91, 0.18), rgba(184, 134, 11, 0.08));
        }

        .line-items-currency-grid {
            display: grid;
            grid-template-columns: minmax(180px, 0.45fr) minmax(0, 1fr);
            gap: 12px;
            align-items: stretch;
        }

        .naira-total-card {
            display: grid;
            align-content: center;
            gap: 5px;
            padding: 14px 16px;
            border: 1px solid rgba(216, 179, 106, 0.16);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.72);
        }

        .naira-total-card span,
        .naira-total-card small {
            color: var(--muted);
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .naira-total-card strong {
            color: var(--text);
            font-size: 24px;
            line-height: 1.1;
        }

        .naira-total-card small {
            letter-spacing: 0;
            line-height: 1.4;
            text-transform: none;
        }

        .line-items-total span {
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .line-items-total strong {
            color: var(--accent-soft);
            font-size: 28px;
        }

        .field input::placeholder,
        .field textarea::placeholder,
        .field-full input::placeholder,
        .field-full textarea::placeholder {
            color: rgba(36, 25, 10, 0.38);
        }

        .template-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .template-card {
            display: grid;
            gap: 12px;
            padding: 18px;
            cursor: pointer;
        }

        .template-card:has(input:checked) {
            border-color: rgba(216, 179, 106, 0.34);
            box-shadow: 0 18px 36px rgba(216, 179, 106, 0.12);
        }

        .template-card input {
            margin: 0;
            accent-color: #d8b36a;
        }

        .template-card strong {
            font-size: 20px;
            letter-spacing: 0;
        }

        .template-card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .swatch-row {
            display: flex;
            gap: 8px;
        }

        .swatch {
            width: 18px;
            height: 18px;
            border-radius: 999px;
            border: 1px solid rgba(184, 134, 11, 0.3);
        }

        .table-wrap {
            overflow: auto;
        }

        .quote-table {
            width: 100%;
            border-collapse: collapse;
        }

        .quote-table th,
        .quote-table td {
            padding: 16px 14px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            vertical-align: top;
        }

        .quote-table th {
            color: var(--muted);
            font-size: 12px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .quote-table td strong {
            display: block;
            margin-bottom: 4px;
        }

        .quote-table td span {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.5;
        }

        .table-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .feature-list li {
            padding-left: 18px;
            position: relative;
            color: var(--muted);
            line-height: 1.7;
        }

        .feature-list li::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0.7em;
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--accent);
        }

        .meta-list {
            display: grid;
            gap: 12px;
        }

        .meta-item {
            padding-bottom: 12px;
            border-bottom: 1px solid var(--line);
        }

        .meta-item:last-child {
            padding-bottom: 0;
            border-bottom: 0;
        }

        .meta-item span {
            display: block;
            margin-bottom: 6px;
            color: var(--muted);
            font-size: 12px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .meta-item strong {
            font-size: 18px;
            letter-spacing: 0;
        }

        .meta-item p {
            margin: 8px 0 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .document-stage {
            padding: 44px 28px 38px;
            background: #f3f1ee;
            overflow: hidden;
        }

        .document-frame {
            position: relative;
            max-width: 760px;
            margin: 0 auto;
        }

        .document-frame::before {
            content: "";
            position: absolute;
            inset: 42px 42px -42px -42px;
            background: #c89b2e;
            box-shadow: 0 28px 70px rgba(30, 28, 24, 0.18);
        }

        .document-frame .quote-document {
            position: relative;
            z-index: 1;
        }

        .auth-hero {
            padding: 34px;
        }

        .auth-signals {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 24px;
        }

        .auth-signals .mini-card strong {
            color: var(--accent-soft);
        }

        .admin-shell--app {
            max-width: none;
            padding: 0;
        }

        .admin-workspace--with-sidebar {
            min-height: 100vh;
            grid-template-columns: 292px minmax(0, 1fr);
            gap: 0;
        }

        .admin-workspace--with-sidebar .admin-main {
            gap: 22px;
            padding: 28px 30px 42px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.68), rgba(255, 248, 234, 0.72)),
                var(--bg);
        }

        .admin-sidebar {
            top: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
            padding: 18px 16px;
            border-width: 0 1px 0 0;
            border-radius: 0;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(255, 249, 236, 0.92)),
                rgba(255, 255, 255, 0.94);
            box-shadow: 10px 0 34px rgba(100, 74, 20, 0.06);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(120, 106, 87, 0.34) transparent;
        }

        .admin-sidebar::-webkit-scrollbar {
            width: 7px;
        }

        .admin-sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .admin-sidebar::-webkit-scrollbar-thumb {
            border-radius: 999px;
            background: rgba(120, 106, 87, 0.28);
        }

        .admin-sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 72px;
            padding: 12px;
            border: 1px solid rgba(184, 134, 11, 0.18);
            border-radius: 14px;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.94), rgba(255, 247, 229, 0.9)),
                rgba(255, 255, 255, 0.78);
            box-shadow: 0 14px 32px rgba(102, 76, 20, 0.08);
        }

        .admin-sidebar .admin-brand-mark {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            border-radius: 12px;
            box-shadow: inset 0 0 0 1px rgba(103, 73, 9, 0.12);
        }

        .admin-sidebar .admin-brand-copy {
            min-width: 0;
        }

        .admin-sidebar .admin-brand-copy strong {
            overflow: hidden;
            font-size: 17px;
            line-height: 1.18;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .admin-sidebar .admin-brand-copy span {
            margin-top: 6px;
            color: rgba(120, 106, 87, 0.82);
            font-size: 10px;
            letter-spacing: 0.18em;
        }

        .admin-nav {
            position: relative;
            gap: 3px;
            margin-top: 16px;
            padding-left: 2px;
        }

        .admin-nav-label {
            margin: 16px 10px 7px;
            color: rgba(120, 106, 87, 0.8);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .admin-nav-link {
            position: relative;
            display: grid;
            grid-template-columns: 14px minmax(0, 1fr);
            align-items: center;
            justify-content: start;
            gap: 10px;
            min-height: 46px;
            padding: 9px 10px;
            border: 1px solid transparent;
            border-radius: 12px;
            background: transparent;
            color: var(--muted);
            isolation: isolate;
            transition: background 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease, color 0.18s ease, transform 0.18s ease;
        }

        .admin-nav-icon {
            position: relative;
            display: inline-grid;
            place-items: center;
            width: 12px;
            height: 32px;
            border-radius: 999px;
            background: transparent;
            color: transparent;
            font-size: 0;
            line-height: 0;
        }

        .admin-nav-icon::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: inherit;
            background: rgba(184, 134, 11, 0.34);
            box-shadow: 0 0 0 4px rgba(184, 134, 11, 0.06);
            transition: width 0.18s ease, height 0.18s ease, background 0.18s ease, box-shadow 0.18s ease;
        }

        .admin-nav-link strong {
            display: block;
            overflow: hidden;
            color: #2a1d0d;
            font-size: 13px;
            font-weight: 780;
            line-height: 1.2;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .admin-nav-link span:not(.admin-nav-icon) {
            display: block;
            margin-top: 3px;
            overflow: hidden;
            color: rgba(120, 106, 87, 0.82);
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.12em;
            line-height: 1.25;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .admin-nav-link.active,
        .admin-nav-link:hover {
            border-color: rgba(184, 134, 11, 0.24);
            background:
                linear-gradient(90deg, rgba(184, 134, 11, 0.13), rgba(255, 255, 255, 0.82)),
                rgba(255, 255, 255, 0.64);
            box-shadow: 0 10px 22px rgba(108, 82, 21, 0.08);
            transform: translateX(1px);
        }

        .admin-nav-link.active::before {
            content: "";
            position: absolute;
            left: -1px;
            top: 12px;
            bottom: 12px;
            width: 3px;
            border-radius: 999px;
            background: var(--accent);
        }

        .admin-nav-link.active .admin-nav-icon::before,
        .admin-nav-link:hover .admin-nav-icon::before {
            width: 8px;
            height: 23px;
            background: var(--accent);
            box-shadow: 0 0 0 4px rgba(184, 134, 11, 0.1);
        }

        .admin-sidebar-note {
            margin-top: auto;
            padding: 14px;
            border-radius: 12px;
            background: rgba(184, 134, 11, 0.06);
        }

        .admin-sidebar-note strong {
            font-size: 15px;
            line-height: 1.25;
        }

        .admin-sidebar-note p {
            font-size: 12px;
            line-height: 1.55;
        }

        .admin-sidebar-account {
            display: grid;
            gap: 10px;
            margin-top: 12px;
            padding: 12px;
            border-top: 1px solid var(--line);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.56);
        }

        .admin-sidebar-account span {
            min-width: 0;
            overflow: hidden;
            color: var(--muted);
            font-size: 12px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .admin-sidebar-account .ghost-button {
            width: 100%;
        }

        .admin-pagebar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            padding: 18px 20px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.82);
        }

        .admin-pagebar .eyebrow {
            margin-bottom: 5px;
        }

        .admin-pagebar h1 {
            margin: 0;
            font-family: var(--font-display);
            font-size: 28px;
            line-height: 1.05;
        }

        .admin-pagebar-actions {
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 10px;
        }

        .panel,
        .status-card,
        .callout-card,
        .metric-card,
        .mini-card,
        .kpi-card,
        .highlight-card,
        .template-card,
        .wizard-progress-button,
        .wizard-note,
        .review-card,
        .bar-row,
        .stack-list li,
        .mini-list li,
        .activity-item,
        .data-note,
        .alert,
        .line-chart-shell,
        .mini-chart {
            border-radius: 8px;
        }

        .button,
        .ghost-button,
        .field input,
        .field select,
        .field textarea,
        .field-full input,
        .field-full select,
        .field-full textarea {
            border-radius: 8px;
        }

        .button {
            min-height: 42px;
            box-shadow: none;
        }

        .ghost-button {
            min-height: 40px;
        }

        .page-header {
            grid-template-columns: minmax(0, 1fr) minmax(300px, 0.56fr);
            gap: 18px;
            padding: 22px;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.94), rgba(255, 248, 232, 0.88)),
                rgba(255, 255, 255, 0.72);
        }

        .page-header h1,
        .hero-banner h1 {
            max-width: 780px;
            font-family: var(--font-display);
            font-size: 42px;
            line-height: 1.02;
        }

        .page-header p,
        .hero-banner p {
            max-width: 760px;
            margin-bottom: 0;
            font-size: 15px;
        }

        .section-heading {
            align-items: flex-end;
            margin: 8px 0 -4px;
            padding-top: 8px;
            border-top: 1px solid rgba(184, 134, 11, 0.14);
        }

        .section-heading h2 {
            font-family: var(--font-display);
            font-size: 28px;
            line-height: 1.08;
        }

        .section-heading p {
            max-width: 680px;
            font-size: 14px;
        }

        .kpi-grid {
            gap: 14px;
        }

        .kpi-card {
            min-height: 164px;
            padding: 18px;
            background: rgba(255, 255, 255, 0.88);
        }

        .kpi-value {
            font-family: var(--font-display);
            font-size: 38px;
        }

        .analytics-grid,
        .dashboard-grid {
            grid-template-columns: minmax(0, 1.8fr) minmax(320px, 0.72fr);
            align-items: start;
        }

        .panel-padded {
            padding: 20px;
        }

        .panel-head {
            margin-bottom: 16px;
        }

        .panel-head h2,
        .panel-title {
            font-family: var(--font-display);
        }

        .panel-head h2 {
            font-size: 26px;
            line-height: 1.12;
        }

        .panel-title {
            font-size: 18px;
        }

        .chart-summary-grid {
            grid-template-columns: repeat(4, minmax(150px, 1fr));
        }

        .panel .mini-card,
        .panel .bar-row,
        .panel .activity-item,
        .panel .stack-list li,
        .panel .mini-list li {
            background: rgba(184, 134, 11, 0.035);
            box-shadow: none;
        }

        .insight-grid {
            gap: 14px;
        }

        .wizard-progress {
            grid-template-columns: repeat(4, minmax(150px, 1fr));
            overflow-x: auto;
            padding-bottom: 4px;
        }

        .quote-table th,
        .quote-table td {
            padding: 14px 12px;
        }

        @media (max-width: 1220px) {
            .admin-workspace--with-sidebar,
            .page-header,
            .hero-banner,
            .analytics-grid,
            .dashboard-grid,
            .preview-grid,
            .auth-grid,
            .insight-grid,
            .kpi-grid,
            .template-grid,
            .auth-signals,
            .form-grid,
            .chart-summary-grid,
            .wizard-progress,
            .wizard-pane-grid,
            .review-grid {
                grid-template-columns: 1fr;
            }

            .auth-grid {
                min-height: auto;
            }

            .auth-brand-panel {
                min-height: auto;
            }

            .admin-sidebar {
                position: static;
                height: auto;
                border-width: 0 0 1px;
            }

            .sticky-stack .panel:first-child {
                position: static;
            }
        }

        @media (max-width: 860px) {
            .admin-shell {
                padding: 16px;
            }

            .admin-shell--app {
                padding: 0;
            }

            .admin-workspace--with-sidebar .admin-main {
                padding: 18px;
            }

            .admin-shell--auth {
                padding: 18px;
            }

            .admin-topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .section-heading,
            .wizard-actions {
                flex-direction: column;
                align-items: flex-start;
            }

            .line-items-editor-head,
            .line-items-total {
                align-items: flex-start;
                flex-direction: column;
            }

            .line-item-row {
                grid-template-columns: 1fr;
                align-items: stretch;
            }

            .line-items-currency-grid {
                grid-template-columns: 1fr;
            }

            .line-item-index {
                margin-bottom: 0;
            }
        }

        @media (max-width: 640px) {
            .panel-padded,
            .page-header,
            .hero-banner,
            .auth-hero,
            .auth-brand-panel,
            .auth-card {
                padding: 18px;
            }

            .page-header h1,
            .hero-banner h1,
            .panel-head h2 {
                font-size: 28px;
            }

            .auth-hero-copy h1 {
                font-size: 36px;
            }

            .auth-meta-strip {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }

        @include('admin.layouts.redesign')

        @if ($isInvoicePreview)
            @include('admin.quotes.partials.document-styles')
        @endif
    .admin-promo-status {
        display: grid;
        gap: 3px;
        max-width: 420px;
        margin-top: 22px;
        padding: 14px 16px;
        border-left: 2px solid var(--primary);
        background: var(--primary-soft);
    }

    .admin-promo-status strong { font-size: 14px; }
    .admin-promo-status > span:last-child { color: var(--muted); font-size: 12px; }
    .admin-discount-control { padding: 18px; border: 1px solid var(--line); border-radius: var(--radius); background: var(--surface-soft); }
    .admin-discount-control__head { display: flex; align-items: flex-start; justify-content: space-between; gap: 18px; }
    .admin-discount-control__head > div { min-width: 0; }
    .admin-discount-control__grid { display: grid; grid-template-columns: auto minmax(150px, .5fr) minmax(180px, .8fr); align-items: end; gap: 16px; margin-top: 16px; }
    .admin-discount-toggle { display: flex; align-items: center; gap: 9px; min-height: 42px; font-weight: 700; }
    .admin-discount-toggle input { width: 17px; height: 17px; accent-color: var(--primary); }
    .line-items-total--discount { margin-top: 0; border-top: 0; }
    .promotion-form__status { padding: 16px; border: 1px solid var(--line); border-radius: var(--radius); background: var(--surface-soft); }
    .promotion-form__grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; margin-top: 20px; }
    .promotion-preview-card { background: linear-gradient(145deg, #fffdf8, #fff4d6); }
    .promotion-preview-card__discount { display: block; margin: 24px 0 8px; color: var(--primary-strong); font-family: var(--font-display); font-size: 52px; line-height: 1; }
    .promotion-preview-card h3 { margin: 0 0 12px; font-size: 20px; }
    .promotion-preview-card p { color: var(--muted); }
    @media (max-width: 760px) {
        .admin-discount-control__head { flex-direction: column; }
        .admin-discount-control__grid { grid-template-columns: 1fr; }
        .promotion-form__grid { grid-template-columns: 1fr; }
    }
    </style>
    @stack('styles')
</head>

<body class="{{ $isAuthenticated ? 'is-admin' : 'is-auth' }} {{ request()->routeIs('admin.quotes.index') ? 'is-dashboard-overview' : '' }} {{ request()->routeIs('admin.quotes.create') ? 'is-invoice-builder' : '' }} {{ request()->routeIs('admin.projects.index') ? 'is-project-files' : '' }}">
    <div class="admin-shell {{ $isAuthenticated ? 'admin-shell--app' : 'admin-shell--auth' }}">
        <div class="admin-workspace {{ $isAuthenticated ? 'admin-workspace--with-sidebar' : '' }}">
            @if ($isAuthenticated)
                <aside class="admin-sidebar" id="admin-sidebar" aria-label="Admin navigation">
                    <div class="admin-sidebar-inner">
                        <div class="admin-sidebar-top">
                            <a class="admin-sidebar-brand" href="{{ route($canInvoices ? 'admin.quotes.index' : ($canProposals ? 'admin.proposals.index' : ($canStaffContracts ? 'admin.staff-contracts.index' : ($canProjects ? 'admin.projects.index' : 'admin.profile')))) }}">
                                <span class="admin-brand-mark">
                                    <img src="{{ asset('/assets/img/logo/favicon.png') }}" alt="" aria-hidden="true">
                                </span>
                                <span class="admin-brand-copy">
                                    <strong>{{ $brandName }}</strong>
                                    <span>Admin Workspace</span>
                                </span>
                            </a>

                            <button class="admin-icon-button admin-sidebar-collapse" type="button"
                                data-sidebar-toggle aria-label="Collapse navigation">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M15 6l-6 6 6 6" />
                                </svg>
                            </button>
                        </div>

                        <nav class="admin-nav">
                            @if ($canInvoices || $canActivity || $canInsights || $canPromotion)
                            <span class="admin-nav-label">Overview</span>
                            @if ($canInvoices)
                                <a class="admin-nav-link {{ request()->routeIs('admin.quotes.index') ? 'active' : '' }}"
                                    href="{{ route('admin.quotes.index') }}">
                                <span class="admin-nav-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M4 5h7v7H4z" />
                                        <path d="M13 5h7v4h-7z" />
                                        <path d="M13 11h7v8h-7z" />
                                        <path d="M4 14h7v5H4z" />
                                    </svg>
                                </span>
                                <div>
                                    <strong>Overview</strong>
                                    <span>Key metrics</span>
                                </div>
                                </a>
                            @endif

                            @if ($canActivity)
                                <a class="admin-nav-link {{ request()->routeIs('admin.quotes.activity') ? 'active' : '' }}"
                                    href="{{ route('admin.quotes.activity') }}">
                                <span class="admin-nav-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M4 17l5-5 4 4 7-8" />
                                        <path d="M4 19h16" />
                                    </svg>
                                </span>
                                <div>
                                    <strong>Activity</strong>
                                    <span>Traffic and leads</span>
                                </div>
                                </a>
                            @endif

                            @if ($canInsights)
                                <a class="admin-nav-link {{ request()->routeIs('admin.quotes.insights') ? 'active' : '' }}"
                                    href="{{ route('admin.quotes.insights') }}">
                                <span class="admin-nav-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M12 3a7 7 0 0 0-4 12.75V18h8v-2.25A7 7 0 0 0 12 3z" />
                                        <path d="M9 21h6" />
                                    </svg>
                                </span>
                                <div>
                                    <strong>Insights</strong>
                                    <span>Demand signals</span>
                                </div>
                                </a>
                            @endif

                            @if ($canPromotion)
                                <a class="admin-nav-link {{ request()->routeIs('admin.quotes.promotion') ? 'active' : '' }}"
                                    href="{{ route('admin.quotes.promotion') }}">
                                <span class="admin-nav-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M12 3v18M3 12h18" />
                                        <circle cx="12" cy="12" r="8" />
                                    </svg>
                                </span>
                                <div>
                                    <strong>Promotion</strong>
                                    <span>Landing offer</span>
                                </div>
                                </a>
                            @endif
                            @endif

                            @if ($canInvoices || $canProposals || $canStaffContracts)
                            <span class="admin-nav-label">Create</span>
                            @if ($canInvoices)
                                <a class="admin-nav-link {{ request()->routeIs('admin.quotes.create') ? 'active' : '' }}"
                                    href="{{ route('admin.quotes.create') }}">
                                <span class="admin-nav-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M7 3h10v18l-2-1-2 1-2-1-2 1-2-1z" />
                                        <path d="M9 8h6" />
                                        <path d="M9 12h6" />
                                    </svg>
                                </span>
                                <div>
                                    <strong>Invoices</strong>
                                    <span>Builder</span>
                                </div>
                                </a>
                            @endif

                            @if ($canProposals)
                                <a class="admin-nav-link {{ $isProposalDashboard ? 'active' : '' }}"
                                    href="{{ route('admin.proposals.index') }}">
                                <span class="admin-nav-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M6 3h9l3 3v15H6z" />
                                        <path d="M14 3v4h4" />
                                        <path d="M9 12h6" />
                                        <path d="M9 16h5" />
                                    </svg>
                                </span>
                                <div>
                                    <strong>Proposals</strong>
                                    <span>Studio</span>
                                </div>
                                </a>
                            @endif

                            @if ($canStaffContracts)
                                <a class="admin-nav-link {{ $isStaffContractWorkspace ? 'active' : '' }}"
                                    href="{{ route('admin.staff-contracts.index') }}">
                                <span class="admin-nav-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M6 3h9l3 3v15H6z" />
                                        <path d="M14 3v4h4" />
                                        <path d="M9 12h6" />
                                        <path d="M9 16h4" />
                                        <path d="M9 8h2" />
                                    </svg>
                                </span>
                                <div>
                                    <strong>Staff Contracts</strong>
                                    <span>Project agreements</span>
                                </div>
                                </a>
                            @endif
                            @endif

                            @if ($canProjects)
                                <span class="admin-nav-label">Workspace</span>
                                @if ($isFullAdmin)
                                <a class="admin-nav-link {{ $isProjectManagementWorkspace ? 'active' : '' }}"
                                    href="{{ route('admin.project-management.dashboard') }}">
                                <span class="admin-nav-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M3 7h7l2 2h9v10H3z" />
                                        <path d="M3 7V5h7l2 2" />
                                    </svg>
                                </span>
                                <div>
                                    <strong>Projects</strong>
                                    <span>Delivery workspace</span>
                                </div>
                                </a>
                                @else
                                @if ($canProjectManagement)
                                <a class="admin-nav-link {{ $isProjectManagementWorkspace ? 'active' : '' }}"
                                    href="{{ route('admin.project-management.dashboard') }}">
                                <span class="admin-nav-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M3 7h7l2 2h9v10H3z" />
                                        <path d="M3 7V5h7l2 2" />
                                    </svg>
                                </span>
                                <div>
                                    <strong>Projects</strong>
                                    <span>Delivery workspace</span>
                                </div>
                                </a>
                                @else
                                <a class="admin-nav-link {{ request()->routeIs('admin.project-management.projects*') ? 'active' : '' }}"
                                    href="{{ route('admin.project-management.projects') }}">
                                <span class="admin-nav-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M3 7h7l2 2h9v10H3z" />
                                        <path d="M3 7V5h7l2 2" />
                                    </svg>
                                </span>
                                <div>
                                    <strong>Projects</strong>
                                    <span>Project records</span>
                                </div>
                                </a>
                                @endif
                                @endif

                                <a class="admin-nav-link {{ $isProjectFileWorkspace ? 'active' : '' }}"
                                    href="{{ route('admin.projects.index') }}">
                                <span class="admin-nav-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M4 6h6l2 2h8v11H4z" />
                                        <path d="M4 6V4h6l2 2" />
                                        <path d="M8 12h8" />
                                        <path d="M8 15h5" />
                                    </svg>
                                </span>
                                <div>
                                    <strong>File sharing</strong>
                                    <span>Shared project files</span>
                                </div>
                                </a>
                            @endif

                            @if ($canLetters || $canArchive)
                            <span class="admin-nav-label">Manage</span>

                            @if ($canLetters)
                                <a class="admin-nav-link {{ $isLetterWorkspace ? 'active' : '' }}"
                                    href="{{ route('admin.letters.create') }}">
                                <span class="admin-nav-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M6 3h9l3 3v15H6z" />
                                        <path d="M14 3v4h4" />
                                        <path d="M9 12h6" />
                                        <path d="M9 16h5" />
                                    </svg>
                                </span>
                                <div>
                                    <strong>Letterhead</strong>
                                    <span>Company documents</span>
                                </div>
                                </a>
                            @endif

                            @if ($canArchive)
                                <a class="admin-nav-link {{ request()->routeIs('admin.quotes.archive') ? 'active' : '' }}"
                                    href="{{ route('admin.quotes.archive') }}">
                                <span class="admin-nav-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M4 7h16v13H4z" />
                                        <path d="M4 7l2-4h12l2 4" />
                                        <path d="M9 12h6" />
                                    </svg>
                                </span>
                                <div>
                                    <strong>Archive</strong>
                                    <span>Saved work</span>
                                </div>
                                </a>
                            @endif
                            @endif

                            <span class="admin-nav-label">System</span>
                            <a class="admin-nav-link {{ $isAdminProfile ? 'active' : '' }}"
                                href="{{ route('admin.profile') }}">
                                <span class="admin-nav-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <circle cx="12" cy="8" r="3" />
                                        <path d="M5 20c1.2-3.4 3.6-5 7-5s5.8 1.6 7 5" />
                                    </svg>
                                </span>
                                <div>
                                    <strong>Profile & Settings</strong>
                                    <span>Account details</span>
                                </div>
                            </a>

                            @if ($isFullAdmin)
                                <a class="admin-nav-link {{ $isSubaccountWorkspace ? 'active' : '' }}"
                                    href="{{ route('admin.subaccounts.index') }}">
                                    <span class="admin-nav-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24">
                                            <circle cx="9" cy="8" r="3" />
                                            <circle cx="17" cy="10" r="2" />
                                            <path d="M3 20c.8-3.2 2.8-5 6-5s5.2 1.8 6 5" />
                                            <path d="M15 16c2.8 0 4.8 1.2 6 4" />
                                        </svg>
                                    </span>
                                    <div>
                                        <strong>Staff accounts</strong>
                                        <span>Team access</span>
                                    </div>
                                </a>
                            @endif

                            @if ($isInvoicePreview || $isInvoiceEditor || $isProposalPreview || $isProposalEditor || $isStaffContractPreview || $isStaffContractEditor || $isLetterWorkspace || $isAdminProfile || $isSubaccountWorkspace)
                                <span class="admin-nav-label">Current</span>
                                <a class="admin-nav-link active" href="{{ url()->current() }}">
                                    <span class="admin-nav-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24">
                                            <path d="M5 4h14v16H5z" />
                                            <path d="M8 8h8" />
                                            <path d="M8 12h8" />
                                            <path d="M8 16h5" />
                                        </svg>
                                    </span>
                                    <div>
                                        <strong>{{ $currentAdminView }}</strong>
                                        <span>{{ $isProjectManagementWorkspace ? 'Delivery' : ($isProposalWorkspace ? 'Proposal' : ($isStaffContractWorkspace ? 'Staff contract' : ($isLetterWorkspace ? 'Letterhead' : ($isSubaccountWorkspace ? 'Account access' : ($isAdminProfile ? 'Account' : ($isInvoiceEditor ? 'Edit' : 'Preview')))))) }}</span>
                                    </div>
                                </a>
                            @endif
                        </nav>

                        <div class="admin-sidebar-meta">
                            <span>Current view</span>
                            <strong>{{ $currentAdminView }}</strong>
                        </div>
                    </div>
                </aside>
                <div class="admin-sidebar-overlay" data-mobile-nav-close></div>
            @endif

            <main class="admin-main">
                @if ($isAuthenticated)
                    <header class="admin-pagebar">
                        <div class="admin-pagebar-title">
                            <button class="admin-icon-button admin-mobile-nav-button" type="button"
                                data-mobile-nav-toggle aria-controls="admin-sidebar" aria-label="Open navigation">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M4 7h16" />
                                    <path d="M4 12h16" />
                                    <path d="M4 17h16" />
                                </svg>
                            </button>
                            <div>
                                <span class="eyebrow">Workspace</span>
                                <h1>{{ $adminPageTitle }}</h1>
                            </div>
                        </div>

                        <div class="admin-pagebar-actions">
                            @if ($isFullAdmin)
                            <button class="admin-search-trigger" type="button" data-command-open aria-haspopup="dialog" aria-keyshortcuts="Control+K Meta+K">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.5" /><path d="m16 16 4 4" /></svg>
                                <span>Search anything...</span>
                                <kbd>⌘ K</kbd>
                            </button>
                            @endif
                            @if ($isSubaccountWorkspace)
                                <a class="button" href="{{ route('admin.subaccounts.create') }}">New Staff account</a>
                            @elseif ($isAdminProfile)
                                @if ($isFullAdmin)
                                    <a class="ghost-button" href="{{ route('admin.subaccounts.index') }}">Manage Staff accounts</a>
                                @endif
                            @elseif ($isProposalWorkspace)
                                <a class="button" href="{{ route('admin.proposals.index') }}">New Proposal</a>
                              @elseif ($isProjectPreview)
                                  <a class="ghost-button" href="{{ route('admin.projects.index') }}">Back to Projects</a>
                              @elseif ($isProjectManagementWorkspace)
                                  @if ($isFullAdmin)
                                  <a class="button" href="{{ route('admin.project-management.projects.create') }}">New Project</a>
                                  @endif
                              @elseif ($isStaffContractWorkspace)
                                <a class="button" href="{{ route('admin.staff-contracts.create') }}">New Staff Contract</a>
                            @elseif ($isLetterWorkspace)
                                <a class="button" href="{{ route('admin.letters.create') }}">New Letter</a>
                            @elseif ($canInvoices)
                                <a class="button" href="{{ route('admin.quotes.create') }}">New Invoice</a>
                            @endif
                            <details class="admin-profile-menu">
                                <summary aria-label="Open profile menu">
                                    <span class="admin-avatar">TT</span>
                                    <span class="admin-profile-copy">
                                        <strong>Admin</strong>
                                        <span>{{ session('luxury_quote_admin_email') }}</span>
                                    </span>
                                </summary>
                                <div class="admin-profile-panel">
                                    <strong>{{ \App\Support\AdminAccess::displayName() }}</strong>
                                    <p>{{ \App\Support\AdminAccess::email() }}</p>
                                    <a class="ghost-button" href="{{ route('admin.profile') }}">Profile & Settings</a>
                                    @if ($isFullAdmin)
                                        <a class="ghost-button" href="{{ route('admin.subaccounts.index') }}">Manage Staff accounts</a>
                                    @endif
                                    <form method="POST" action="{{ route('admin.logout') }}">
                                        @csrf
                                        <button type="submit" class="ghost-button">Sign Out</button>
                                    </form>
                                </div>
                            </details>
                        </div>
                    </header>
                @endif

                @if (session('status') || session('admin_notice'))
                    <div class="admin-alert-stack" data-admin-alert-stack aria-live="polite" aria-atomic="true">
                        @if (session('status'))
                            <div class="alert alert-success" role="status">
                                <span>{{ session('status') }}</span>
                                <button class="admin-alert-close" type="button" data-admin-alert-close aria-label="Dismiss notification">&times;</button>
                            </div>
                        @endif

                        @if (session('admin_notice'))
                            <div class="alert alert-warning" role="alert">
                                <span>{{ session('admin_notice') }}</span>
                                <button class="admin-alert-close" type="button" data-admin-alert-close aria-label="Dismiss notification">&times;</button>
                            </div>
                        @endif
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    @if ($isAuthenticated)
        <div class="command-palette" data-command-palette hidden>
            <div class="command-palette__backdrop" data-command-close></div>
            <section class="command-palette__dialog" role="dialog" aria-modal="true" aria-labelledby="command-palette-title">
                <div class="command-palette__head">
                    <label id="command-palette-title" for="command-palette-input">Jump to</label>
                    <button class="admin-icon-button" type="button" data-command-close aria-label="Close search">×</button>
                </div>
                <div class="command-palette__input-wrap">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.5" /><path d="m16 16 4 4" /></svg>
                    <input id="command-palette-input" type="search" data-command-input placeholder="Search workspace" autocomplete="off">
                    <kbd>ESC</kbd>
                </div>
                <div class="command-palette__results" data-command-results>
                    @if ($canInvoices)
                        <a class="command-palette__item" data-command-item data-command-label="Dashboard Overview" href="{{ route('admin.quotes.index') }}"><span class="command-palette__item-icon">⌂</span><span><strong>Overview</strong><small>Dashboard</small></span><span class="command-palette__arrow">↵</span></a>
                        <a class="command-palette__item" data-command-item data-command-label="New Invoice Builder" href="{{ route('admin.quotes.create') }}"><span class="command-palette__item-icon">＋</span><span><strong>New invoice</strong><small>Invoice builder</small></span><span class="command-palette__arrow">↵</span></a>
                    @endif
                    @if ($canActivity)
                        <a class="command-palette__item" data-command-item data-command-label="Activity Traffic Leads" href="{{ route('admin.quotes.activity') }}"><span class="command-palette__item-icon">↗</span><span><strong>Activity</strong><small>Traffic and leads</small></span><span class="command-palette__arrow">↵</span></a>
                    @endif
                    @if ($canProjects)
                        @if ($isFullAdmin)
                        <a class="command-palette__item" data-command-item data-command-label="Projects Delivery" href="{{ route('admin.project-management.dashboard') }}"><span class="command-palette__item-icon">□</span><span><strong>Projects</strong><small>Delivery workspace</small></span><span class="command-palette__arrow">↵</span></a>
                        @elseif ($canProjectManagement)
                        <a class="command-palette__item" data-command-item data-command-label="Projects Delivery" href="{{ route('admin.project-management.dashboard') }}"><span class="command-palette__item-icon">□</span><span><strong>Projects</strong><small>Delivery workspace</small></span><span class="command-palette__arrow">↵</span></a>
                        @else
                        <a class="command-palette__item" data-command-item data-command-label="Projects" href="{{ route('admin.project-management.projects') }}"><span class="command-palette__item-icon">□</span><span><strong>Projects</strong><small>Project records</small></span><span class="command-palette__arrow">↵</span></a>
                        @endif
                        <a class="command-palette__item" data-command-item data-command-label="File Sharing" href="{{ route('admin.projects.index') }}"><span class="command-palette__item-icon">▱</span><span><strong>File sharing</strong><small>Shared files</small></span><span class="command-palette__arrow">↵</span></a>
                    @endif
                    <a class="command-palette__item" data-command-item data-command-label="Profile Settings" href="{{ route('admin.profile') }}"><span class="command-palette__item-icon">◎</span><span><strong>Profile</strong><small>Account settings</small></span><span class="command-palette__arrow">↵</span></a>
                    <p class="command-palette__empty" data-command-empty hidden>No matching workspace items.</p>
                </div>
                <div class="command-palette__foot"><span>Navigate</span><span><kbd>↑</kbd><kbd>↓</kbd> Select</span><span><kbd>↵</kbd> Open</span></div>
            </section>
        </div>
    @endif
    <script>
        (() => {
            const stack = document.querySelector('[data-admin-alert-stack]');

            if (!stack) {
                return;
            }

            stack.querySelectorAll('.alert').forEach((alert) => {
                let dismissTimer;
                let isPaused = false;
                const delay = alert.classList.contains('alert-danger') ? 8000 : 5000;

                const remove = () => {
                    if (isPaused || alert.classList.contains('is-dismissing')) {
                        return;
                    }

                    alert.classList.add('is-dismissing');
                    window.setTimeout(() => alert.remove(), 220);
                };

                const schedule = (timeout = delay) => {
                    window.clearTimeout(dismissTimer);
                    dismissTimer = window.setTimeout(remove, timeout);
                };

                alert.querySelector('[data-admin-alert-close]')?.addEventListener('click', remove);
                alert.addEventListener('mouseenter', () => {
                    isPaused = true;
                    window.clearTimeout(dismissTimer);
                });
                alert.addEventListener('mouseleave', () => {
                    isPaused = false;
                    schedule(2500);
                });
                alert.addEventListener('focusin', () => {
                    isPaused = true;
                    window.clearTimeout(dismissTimer);
                });
                alert.addEventListener('focusout', () => {
                    isPaused = false;
                    schedule(2500);
                });

                schedule();
            });
        })();
    </script>
    <script>
        (() => {
            const body = document.body;
            const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
            const mobileToggle = document.querySelector('[data-mobile-nav-toggle]');
            const mobileClose = document.querySelector('[data-mobile-nav-close]');
            const collapsedKey = 'tt-admin-sidebar-collapsed';

            if (localStorage.getItem(collapsedKey) === 'true') {
                body.classList.add('is-sidebar-collapsed');
            }

            sidebarToggle?.addEventListener('click', () => {
                body.classList.toggle('is-sidebar-collapsed');
                localStorage.setItem(collapsedKey, body.classList.contains('is-sidebar-collapsed') ? 'true' : 'false');
            });

            mobileToggle?.addEventListener('click', () => {
                body.classList.add('is-mobile-nav-open');
            });

            mobileClose?.addEventListener('click', () => {
                body.classList.remove('is-mobile-nav-open');
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    body.classList.remove('is-mobile-nav-open');
                }
            });
        })();

        (() => {
            const palette = document.querySelector('[data-command-palette]');
            const openButton = document.querySelector('[data-command-open]');
            const closeButtons = document.querySelectorAll('[data-command-close]');
            const input = palette?.querySelector('[data-command-input]');
            const items = palette ? Array.from(palette.querySelectorAll('[data-command-item]')) : [];
            const empty = palette?.querySelector('[data-command-empty]');
            let lastFocusedElement = null;
            let activeIndex = 0;

            const visibleItems = () => items.filter((item) => !item.hidden);

            const setActiveItem = (index) => {
                const visible = visibleItems();

                if (!visible.length) {
                    activeIndex = 0;
                    return;
                }

                activeIndex = (index + visible.length) % visible.length;
                visible.forEach((item, itemIndex) => {
                    item.classList.toggle('is-keyboard-active', itemIndex === activeIndex);
                });
            };

            const closePalette = () => {
                if (!palette) {
                    return;
                }

                palette.hidden = true;
                document.body.classList.remove('is-command-open');
                lastFocusedElement?.focus?.();
            };

            const openPalette = () => {
                if (!palette) {
                    return;
                }

                lastFocusedElement = document.activeElement;
                palette.hidden = false;
                document.body.classList.add('is-command-open');
                input?.focus();
                input?.select();
                setActiveItem(0);
            };

            const filterItems = () => {
                const query = (input?.value || '').trim().toLowerCase();
                let visibleCount = 0;

                items.forEach((item) => {
                    const matches = !query || (item.dataset.commandLabel || item.textContent).toLowerCase().includes(query);
                    item.hidden = !matches;
                    visibleCount += matches ? 1 : 0;
                });

                if (empty) {
                    empty.hidden = visibleCount !== 0;
                }

                setActiveItem(0);
            };

            openButton?.addEventListener('click', openPalette);
            closeButtons.forEach((button) => button.addEventListener('click', closePalette));
            input?.addEventListener('input', filterItems);

            document.addEventListener('keydown', (event) => {
                if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
                    event.preventDefault();
                    openPalette();
                    return;
                }

                if (palette && !palette.hidden && ['ArrowDown', 'ArrowUp', 'Enter'].includes(event.key)) {
                    const visible = visibleItems();

                    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                        event.preventDefault();
                        setActiveItem(activeIndex + (event.key === 'ArrowDown' ? 1 : -1));
                    } else if (visible[activeIndex]) {
                        event.preventDefault();
                        visible[activeIndex].click();
                    }

                    return;
                }

                if (event.key === 'Escape' && palette && !palette.hidden) {
                    event.preventDefault();
                    closePalette();
                }
            });

            palette?.addEventListener('click', (event) => {
                if (event.target === palette) {
                    closePalette();
                }
            });
        })();

        (() => {
            const menus = Array.from(document.querySelectorAll('.action-menu'));
            const edgePadding = 8;
            const gap = 6;

            const clearPosition = (menu) => {
                const panel = menu.querySelector('.action-menu-panel');

                if (!panel) {
                    return;
                }

                panel.classList.remove('is-floating');
                panel.style.removeProperty('left');
                panel.style.removeProperty('top');
                panel.style.removeProperty('right');
            };

            const positionMenu = (menu) => {
                const summary = menu.querySelector('summary');
                const panel = menu.querySelector('.action-menu-panel');

                if (!menu.open || !summary || !panel) {
                    return;
                }

                panel.classList.add('is-floating');

                const summaryRect = summary.getBoundingClientRect();
                const panelWidth = panel.offsetWidth;
                const panelHeight = panel.offsetHeight;
                const maxLeft = Math.max(edgePadding, window.innerWidth - panelWidth - edgePadding);
                const maxTop = Math.max(edgePadding, window.innerHeight - panelHeight - edgePadding);
                const left = Math.min(
                    Math.max(edgePadding, summaryRect.right - panelWidth),
                    maxLeft
                );
                const belowTop = summaryRect.bottom + gap;
                const aboveTop = summaryRect.top - panelHeight - gap;
                const top = belowTop + panelHeight <= window.innerHeight - edgePadding
                    ? belowTop
                    : Math.max(edgePadding, aboveTop);

                panel.style.left = `${left}px`;
                panel.style.top = `${Math.min(top, maxTop)}px`;
                panel.style.right = 'auto';
            };

            menus.forEach((menu) => {
                menu.addEventListener('toggle', () => {
                    if (menu.open) {
                        window.requestAnimationFrame(() => positionMenu(menu));
                    } else {
                        clearPosition(menu);
                    }
                });
            });

            const repositionOpenMenus = () => {
                menus.filter((menu) => menu.open).forEach(positionMenu);
            };

            window.addEventListener('resize', repositionOpenMenus);
            window.addEventListener('scroll', repositionOpenMenus, true);
        })();

        (() => {
            const richEditorInstances = [];

            const escapeHtml = (value) => String(value || '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');

            const sanitizeEditorHtml = (editor) => {
                const clone = editor.cloneNode(true);
                const allowedTags = new Set(['div', 'p', 'br', 'ul', 'ol', 'li', 'strong', 'b', 'em', 'i', 'u']);
                const walk = document.createTreeWalker(clone, NodeFilter.SHOW_ELEMENT);
                const nodes = [];

                while (walk.nextNode()) {
                    nodes.push(walk.currentNode);
                }

                nodes.reverse().forEach((node) => {
                    const tag = node.tagName.toLowerCase();

                    if (tag === 'script' || tag === 'style') {
                        node.remove();
                        return;
                    }

                    if (!allowedTags.has(tag)) {
                        node.replaceWith(...Array.from(node.childNodes));
                        return;
                    }

                    Array.from(node.attributes).forEach((attribute) => node.removeAttribute(attribute.name));

                    if (tag === 'b') {
                        const strong = document.createElement('strong');
                        strong.append(...Array.from(node.childNodes));
                        node.replaceWith(strong);
                    }

                    if (tag === 'i') {
                        const emphasis = document.createElement('em');
                        emphasis.append(...Array.from(node.childNodes));
                        node.replaceWith(emphasis);
                    }
                });

                return clone.textContent.trim() ? clone.innerHTML.trim() : '';
            };

            const textToEditorHtml = (value) => {
                const text = String(value || '');

                if (!text.trim()) {
                    return '';
                }

                if (/<\/?[a-z][\s\S]*>/i.test(text)) {
                    const template = document.createElement('template');
                    template.innerHTML = text;
                    const wrapper = document.createElement('div');
                    wrapper.append(template.content.cloneNode(true));

                    return sanitizeEditorHtml(wrapper);
                }

                return text
                    .split(/\r?\n/)
                    .map((line) => `<div>${escapeHtml(line) || '<br>'}</div>`)
                    .join('');
            };

            const editorToPlainText = (editor) => {
                const lines = [];

                editor.childNodes.forEach((node) => {
                    if (node.nodeType === Node.TEXT_NODE) {
                        const text = node.textContent.trim();

                        if (text) {
                            lines.push(text);
                        }

                        return;
                    }

                    if (node.nodeType !== Node.ELEMENT_NODE) {
                        return;
                    }

                    const tag = node.tagName.toLowerCase();

                    if (tag === 'ul' || tag === 'ol') {
                        node.querySelectorAll('li').forEach((item, index) => {
                            const text = item.textContent.trim();

                            if (text) {
                                lines.push(`${tag === 'ol' ? `${index + 1}.` : '-'} ${text}`);
                            }
                        });

                        return;
                    }

                    if (tag === 'br') {
                        lines.push('');
                        return;
                    }

                    const text = node.textContent.trim();

                    if (text) {
                        lines.push(text);
                    }
                });

                return lines.join('\n').trim();
            };

            const validateRichEditor = (instance, shouldFocus = false) => {
                const value = editorToPlainText(instance.body);
                const isInvalid = instance.required && value.length === 0;

                instance.shell.classList.toggle('is-invalid', isInvalid);
                instance.source.value = sanitizeEditorHtml(instance.body);

                if (isInvalid && shouldFocus) {
                    instance.body.focus();
                }

                return !isInvalid;
            };

            const syncRichEditor = (instance) => {
                instance.source.value = sanitizeEditorHtml(instance.body);
                instance.source.dispatchEvent(new Event('input', { bubbles: true }));
                validateRichEditor(instance);
            };

            document.querySelectorAll('textarea[data-rich-editor]').forEach((textarea) => {
                const required = textarea.required;
                const placeholder = textarea.getAttribute('placeholder') || 'Write here...';
                const shell = document.createElement('div');
                const toolbar = document.createElement('div');
                const body = document.createElement('div');
                const feedback = document.createElement('span');

                textarea.required = false;
                textarea.classList.add('rich-editor-source');
                textarea.setAttribute('aria-hidden', 'true');

                shell.className = 'rich-editor';
                shell.dataset.richEditorInstance = '';

                toolbar.className = 'rich-editor-toolbar';
                toolbar.setAttribute('aria-label', 'Text editor tools');
                toolbar.innerHTML = `
                    <button type="button" data-rich-command="bold" aria-label="Bold"><strong>B</strong></button>
                    <button type="button" data-rich-command="formatBlock" data-rich-value="div">Paragraph</button>
                    <button type="button" data-rich-command="insertUnorderedList">Bullets</button>
                    <button type="button" data-rich-command="insertOrderedList">Numbers</button>
                    <button type="button" data-rich-clear>Clear</button>
                `;

                body.className = 'rich-editor-body';
                body.contentEditable = 'true';
                body.role = 'textbox';
                body.setAttribute('aria-multiline', 'true');
                body.dataset.placeholder = placeholder;
                body.innerHTML = textToEditorHtml(textarea.value);

                feedback.className = 'rich-editor-feedback';
                feedback.textContent = 'This field is required.';

                shell.append(toolbar, body);
                textarea.after(shell, feedback);

                const instance = {
                    source: textarea,
                    shell,
                    body,
                    required,
                };

                richEditorInstances.push(instance);

                toolbar.addEventListener('click', (event) => {
                    const button = event.target.closest('button');

                    if (!button) {
                        return;
                    }

                    event.preventDefault();
                    body.focus();

                    if (button.hasAttribute('data-rich-clear')) {
                        body.innerHTML = '';
                        syncRichEditor(instance);
                        return;
                    }

                    document.execCommand(
                        button.dataset.richCommand,
                        false,
                        button.dataset.richValue || null
                    );
                    syncRichEditor(instance);
                });

                body.addEventListener('input', () => syncRichEditor(instance));
                body.addEventListener('blur', () => syncRichEditor(instance));
            });

            window.validateRichEditorsIn = (scope = document, shouldFocus = false) => {
                const instances = richEditorInstances.filter((instance) => scope.contains(instance.shell));

                for (const instance of instances) {
                    if (!validateRichEditor(instance, shouldFocus)) {
                        return instance.shell;
                    }
                }

                return null;
            };

            document.querySelectorAll('form').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    const invalidEditor = window.validateRichEditorsIn(form, true);

                    if (invalidEditor) {
                        event.preventDefault();
                    }
                });
            });

            const formatCurrency = (value) => `$${Number(value || 0).toLocaleString(undefined, {
                maximumFractionDigits: 0,
            })}`;

            const formatNaira = (value) => `NGN ${Number(value || 0).toLocaleString(undefined, {
                maximumFractionDigits: 0,
            })}`;

            document.querySelectorAll('[data-line-items-editor]').forEach((editor) => {
                const rows = editor.querySelector('[data-line-item-rows]');
                const template = editor.querySelector('[data-line-item-template]');
                const addButton = editor.querySelector('[data-line-item-add]');
                const subtotalDisplay = editor.querySelector('[data-line-item-subtotal-display]');
                const totalDisplay = editor.querySelector('[data-line-item-total-display]');
                const totalInput = editor.querySelector('[data-line-item-total-input]');
                const exchangeRateInput = editor.querySelector('[data-exchange-rate]');
                const nairaTotalDisplay = editor.querySelector('[data-naira-total-display]');
                const discountEnabled = editor.closest('form')?.querySelector('[data-discount-enabled]');
                const discountPercent = editor.closest('form')?.querySelector('[data-discount-percent]');
                const discountCode = editor.closest('form')?.querySelector('[data-discount-code]');

                if (!rows || !template || !subtotalDisplay || !totalDisplay || !totalInput) {
                    return;
                }

                const recalculate = () => {
                    const subtotal = Array.from(rows.querySelectorAll('[data-line-item-amount]'))
                        .reduce((sum, input) => sum + (Number(input.value) || 0), 0);
                    const percent = discountEnabled?.checked ? Math.min(100, Math.max(0, Number(discountPercent?.value) || 0)) : 0;
                    const total = Math.max(0, subtotal - (subtotal * percent / 100));

                    subtotalDisplay.textContent = formatCurrency(subtotal);
                    totalDisplay.textContent = formatCurrency(total);
                    totalInput.value = total.toFixed(2);

                    if (exchangeRateInput && nairaTotalDisplay) {
                        nairaTotalDisplay.textContent = formatNaira(total * (Number(exchangeRateInput.value) || 0));
                    }

                    totalInput.dispatchEvent(new Event('input', { bubbles: true }));
                };

                const reindexRows = () => {
                    rows.querySelectorAll('[data-line-item-row]').forEach((row, index) => {
                        row.querySelector('[data-line-item-index]').textContent = String(index + 1).padStart(2, '0');

                        row.querySelectorAll('[name]').forEach((field) => {
                            field.name = field.name.replace(/line_items\[\d+\]/, `line_items[${index}]`);
                        });

                        row.querySelectorAll('[id]').forEach((field) => {
                            field.id = field.id.replace(/_(\d+)$/, `_${index}`);
                        });

                        row.querySelectorAll('label[for]').forEach((label) => {
                            label.htmlFor = label.htmlFor.replace(/_(\d+)$/, `_${index}`);
                        });
                    });

                    const removeButtons = rows.querySelectorAll('[data-line-item-remove]');
                    removeButtons.forEach((button) => {
                        button.disabled = removeButtons.length === 1;
                    });
                };

                rows.addEventListener('input', (event) => {
                    if (event.target.matches('[data-line-item-amount]')) {
                        recalculate();
                    }
                });

                exchangeRateInput?.addEventListener('input', recalculate);
                discountEnabled?.addEventListener('change', () => {
                    [discountPercent, discountCode].forEach((field) => {
                        if (field) field.disabled = !discountEnabled.checked;
                    });
                    recalculate();
                });
                discountPercent?.addEventListener('input', recalculate);

                rows.addEventListener('click', (event) => {
                    const removeButton = event.target.closest('[data-line-item-remove]');

                    if (!removeButton) {
                        return;
                    }

                    const row = removeButton.closest('[data-line-item-row]');

                    if (!row || rows.querySelectorAll('[data-line-item-row]').length === 1) {
                        return;
                    }

                    row.remove();
                    reindexRows();
                    recalculate();
                });

                addButton?.addEventListener('click', () => {
                    const index = rows.querySelectorAll('[data-line-item-row]').length;
                    const html = template.innerHTML
                        .replaceAll('__INDEX__', String(index))
                        .replaceAll('__NUMBER__', String(index + 1).padStart(2, '0'));

                    rows.insertAdjacentHTML('beforeend', html);
                    reindexRows();
                    recalculate();

                    const newRow = rows.querySelector('[data-line-item-row]:last-child input');
                    newRow?.focus();
                });

                reindexRows();
                recalculate();
            });
        })();
    </script>
    @stack('scripts')
</body>

</html>
