<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $letter['document_type'] }} | Turance Technologies</title>
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        @include('admin.letters.partials.document-styles')

        .letter-document {
            page-break-after: avoid;
        }
    </style>
</head>

<body>
    @include('admin.letters.partials.document', [
        'letter' => $letter,
        'backgroundSrc' => $backgroundSrc,
    ])
</body>

</html>
