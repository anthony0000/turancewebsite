<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        @page { margin: 18px; }
        body { margin: 0; padding: 0; background: #fff; }
        @include('admin.project-payments.partials.document-styles')
        .payment-document { min-height: 0; }
    </style>
</head>
<body>@include('admin.project-payments.partials.document')</body>
</html>
