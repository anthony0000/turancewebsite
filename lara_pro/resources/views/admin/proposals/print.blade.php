<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Print {{ $proposal->proposal_number }}</title>
    <style>
        body {
            margin: 0;
            background: #eef0f2;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .print-toolbar {
            position: sticky;
            top: 0;
            z-index: 5;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 18px;
            background: #111111;
            color: #ffffff;
        }

        .print-toolbar a,
        .print-toolbar button {
            border: 1px solid rgba(255, 255, 255, 0.22);
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            cursor: pointer;
            padding: 10px 14px;
            text-decoration: none;
        }

        .print-stage {
            max-width: 900px;
            margin: 24px auto;
        }

        @include('admin.proposals.partials.document-styles', ['proposal' => $proposal])

        @media print {
            body {
                background: #ffffff;
            }

            .print-toolbar {
                display: none;
            }

            .print-stage {
                max-width: none;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="print-toolbar">
        <strong>{{ $proposal->title }}</strong>
        <span>
            <a href="{{ route('admin.proposals.show', $proposal) }}">Back</a>
            <button type="button" onclick="window.print()">Print</button>
        </span>
    </div>

    <main class="print-stage">
        @include('admin.proposals.partials.document', ['proposal' => $proposal])
    </main>
</body>

</html>
