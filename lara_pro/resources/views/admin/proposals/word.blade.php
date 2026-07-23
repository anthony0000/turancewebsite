<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $proposal->title }} | {{ $proposal->proposal_number }}</title>
    <style>
        body {
            margin: 0;
            background: #ffffff;
        }

        @include('admin.proposals.partials.document-styles', ['proposal' => $proposal])

        .proposal-page {
            margin-bottom: 18px;
            box-shadow: none;
        }
    </style>
</head>

<body>
    @include('admin.proposals.partials.document', ['proposal' => $proposal])
</body>

</html>
