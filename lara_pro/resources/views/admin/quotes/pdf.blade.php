<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $quote->quote_number }}</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            background: #ffffff;
        }

        @include('admin.quotes.partials.document-styles')

        .quote-document {
            border: none;
            border-radius: 0;
            box-shadow: none;
        }
    </style>
</head>

<body>
    @include('admin.quotes.partials.document', [
        'quote' => $quote,
        'template' => $template,
        'brand' => $brand,
    ])
</body>

</html>
