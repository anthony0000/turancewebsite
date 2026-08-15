<?php

namespace App\Support;

use Dompdf\Dompdf;

final class DocumentTypography
{
    /**
     * The shared document family is bundled and registered explicitly so
     * exports do not depend on fonts installed on the generating machine.
     */
    public const PDF_SANS = 'Urbanist';

    public const PDF_DISPLAY = 'Urbanist';

    /**
     * The static weights are kept in the application so browser and PDF
     * exports use the same typeface without depending on server fonts.
     *
     * @return array<int, array{file: string, weight: int}>
     */
    public static function urbanistFontFiles(): array
    {
        return [
            ['file' => 'Urbanist-Regular.ttf', 'weight' => 400],
            ['file' => 'Urbanist-Medium.ttf', 'weight' => 500],
            ['file' => 'Urbanist-SemiBold.ttf', 'weight' => 600],
            ['file' => 'Urbanist-Bold.ttf', 'weight' => 700],
        ];
    }

    public static function urbanistFontUrl(string $file): string
    {
        $path = str_replace('\\', '/', resource_path('fonts/'.$file));

        return str_starts_with($path, '/')
            ? 'file://'.$path
            : 'file:///'.ltrim($path, '/');
    }

    public static function registerDompdfFonts(Dompdf $dompdf): void
    {
        $fontDirectory = $dompdf->getOptions()->getFontDir();

        if (! is_dir($fontDirectory)) {
            mkdir($fontDirectory, 0755, true);
        }

        foreach (self::urbanistFontFiles() as $font) {
            $registered = $dompdf->getFontMetrics()->registerFont(
                [
                    'family' => self::PDF_SANS,
                    'style' => 'normal',
                    'weight' => $font['weight'],
                ],
                resource_path('fonts/'.$font['file'])
            );

            if (! $registered) {
                throw new \RuntimeException('Urbanist could not be registered with the PDF renderer.');
            }
        }
    }

    /**
     * Keep saved proposal settings backwards compatible while preventing
     * unsupported desktop fonts from silently becoming a default fallback.
     */
    public static function proposalFamily(?string $requested): string
    {
        return self::PDF_SANS;
    }
}
