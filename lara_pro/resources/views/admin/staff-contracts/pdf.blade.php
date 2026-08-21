<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Staff Contract {{ $contract->contract_number }}</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Urbanist', sans-serif;
            background: #ffffff;
        }

        @include('admin.staff-contracts.partials.document-styles')

        .staff-contract-document {
            border: none;
        }
    </style>
</head>

<body>
    @include('admin.staff-contracts.partials.document', [
        'contract' => $contract,
        'brand' => $brand,
    ])
</body>

</html>
