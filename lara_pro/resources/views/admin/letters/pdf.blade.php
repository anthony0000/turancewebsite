<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $letter['document_type'] }} | Turance Technologies</title>
    <style>
        html, body { margin: 0; padding: 0; }

        @include('admin.letters.partials.document-styles')

        .letter-document { page-break-after: always; }
        .letter-document:last-child { page-break-after: avoid; }
        .letter-copy--continuation { margin-top: 0; }
        @page { margin: 0; size: A4 portrait; }
    </style>
</head>

<body>
    @foreach ($pages as $page)
        @include('admin.letters.partials.document', [
            'letter' => array_merge($letter, ['body' => $page['body']]),
            'backgroundSrc' => $backgroundSrc,
            'showHeader' => $page['first'],
            'showSignoff' => $page['last'],
            'showPlaceholder' => false,
        ])
    @endforeach
</body>

</html>
