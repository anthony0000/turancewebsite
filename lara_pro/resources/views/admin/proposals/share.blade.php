<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ $proposal->title }}</title>
    <style>
        body {
            margin: 0;
            background: #eceff1;
            color: #171717;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .share-shell {
            max-width: 980px;
            margin: 0 auto;
            padding: 22px;
        }

        .share-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            margin-bottom: 18px;
            padding: 16px 18px;
            background: #ffffff;
            border: 1px solid rgba(20, 20, 20, 0.08);
        }

        .share-header h1 {
            margin: 0;
            font-size: 20px;
            line-height: 1.2;
        }

        .share-header span {
            color: #667085;
            font-size: 12px;
        }

        @include('admin.proposals.partials.document-styles', ['proposal' => $proposal])
    </style>
</head>

<body>
    <main class="share-shell">
        <header class="share-header">
            <div>
                <h1>{{ $proposal->title }}</h1>
                <span>{{ $proposal->proposal_number }} / prepared for {{ $proposal->client_company ?: $proposal->client_name ?: 'Client' }}</span>
            </div>
            <span>{{ ucfirst($proposal->status) }}</span>
        </header>

        @include('admin.proposals.partials.document', ['proposal' => $proposal])
    </main>
</body>

</html>
