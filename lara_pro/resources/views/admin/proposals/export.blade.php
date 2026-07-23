<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $proposal->title }} | {{ $proposal->proposal_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        html,
        body {
            width: 210mm;
            min-width: 210mm;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        @include('admin.proposals.partials.document-styles', ['proposal' => $proposal])

        .proposal-export-wrapper,
        .proposal-document {
            width: 210mm;
            max-width: 210mm;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        .proposal-page {
            width: 210mm;
            height: 297mm;
            min-height: 297mm;
            max-height: 297mm;
            margin: 0;
            overflow: hidden;
            border: 0;
            box-shadow: none;
            break-after: page;
            page-break-after: always;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .proposal-page:last-child {
            break-after: auto;
            page-break-after: auto;
        }

        .proposal-page--cover,
        .proposal-cover-layout,
        .proposal-cover-main {
            height: 297mm;
            min-height: 297mm;
            max-height: 297mm;
        }

        .proposal-table,
        .proposal-total-table {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .proposal-table tr,
        .proposal-table th,
        .proposal-table td,
        .proposal-timeline-item,
        .proposal-team-card,
        .proposal-split-card,
        .proposal-signature {
            break-inside: avoid;
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <main class="proposal-export-wrapper">
        @include('admin.proposals.partials.document', [
            'proposal' => $proposal,
            'assetMode' => 'file-uri',
            'isPdfExport' => true,
        ])
    </main>
</body>

</html>
