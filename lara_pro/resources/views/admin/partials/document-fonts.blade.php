@php
    $urbanistFontFiles = \App\Support\DocumentTypography::urbanistFontFiles();
@endphp

@foreach ($urbanistFontFiles as $font)
    @font-face {
        font-family: 'Urbanist';
        src: url('{{ \App\Support\DocumentTypography::urbanistFontUrl($font['file']) }}') format('truetype');
        font-style: normal;
        font-weight: {{ $font['weight'] }};
        font-display: swap;
    }
@endforeach
