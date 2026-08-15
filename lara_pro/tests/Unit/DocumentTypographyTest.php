<?php

use App\Support\DocumentTypography;

it('maps proposal font settings to reliable export families', function () {
    expect(DocumentTypography::proposalFamily('Aptos'))->toBe('Urbanist')
        ->and(DocumentTypography::proposalFamily('Inter'))->toBe('Urbanist')
        ->and(DocumentTypography::proposalFamily('Georgia'))->toBe('Urbanist')
        ->and(DocumentTypography::proposalFamily('Times New Roman'))->toBe('Urbanist');
});
