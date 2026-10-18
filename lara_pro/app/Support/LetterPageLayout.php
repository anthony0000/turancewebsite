<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use Barryvdh\DomPDF\Facade\Pdf;

final class LetterPageLayout
{
    /** @return array<int, array{body: string, first: bool, last: bool}> */
    public static function pages(array $letter): array
    {
        $blocks = self::blocks((string) ($letter['body'] ?? ''));
        $pages = [];
        $cursor = 0;
        $fitCache = [];
        $fits = static function (string $body, bool $first, bool $last) use ($letter, &$fitCache): bool {
            $key = ($first ? '1' : '0').($last ? '1' : '0').sha1($body);

            return $fitCache[$key] ??= self::fitsOnPage($letter, $body, $first, $last);
        };

        while ($cursor < count($blocks)) {
            $first = $pages === [];
            $remaining = array_slice($blocks, $cursor);
            if ($fits(implode('', $remaining), $first, true)) {
                $pages[] = ['body' => implode('', $remaining), 'first' => $first, 'last' => true];
                break;
            }

            $maximum = count($remaining) - 1;
            $low = 1;
            $high = $maximum;
            $take = 0;
            while ($low <= $high) {
                $middle = intdiv($low + $high, 2);
                if ($fits(implode('', array_slice($remaining, 0, $middle)), $first, false)) {
                    $take = $middle;
                    $low = $middle + 1;
                } else {
                    $high = $middle - 1;
                }
            }

            if ($take === 0) {
                $parts = self::splitBlock($blocks[$cursor]);
                if (count($parts) > 1) {
                    array_splice($blocks, $cursor, 1, $parts);
                    continue;
                }
                $take = 1;
            }

            $pages[] = [
                'body' => implode('', array_slice($blocks, $cursor, $take)),
                'first' => $first,
                'last' => false,
            ];
            $cursor += $take;
        }

        return $pages ?: [['body' => '', 'first' => true, 'last' => true]];
    }

    private static function fitsOnPage(array $letter, string $body, bool $first, bool $last): bool
    {
        $letter = array_merge([
            'document_type' => 'Official Letter',
            'date_label' => '',
            'recipient_name' => '',
            'recipient_role' => '',
            'recipient_company' => '',
            'recipient_address' => '',
            'subject' => '',
            'greeting' => 'Dear Sir/Madam,',
            'closing' => 'Kind regards,',
            'signatory_name' => '',
            'signatory_title' => '',
        ], $letter);
        $measurements = [];
        $pdf = Pdf::loadView('admin.letters.pdf', [
            'letter' => $letter,
            'pages' => [['body' => $body, 'first' => $first, 'last' => $last]],
            'backgroundSrc' => null,
        ])->setPaper('a4', 'portrait');
        $dompdf = $pdf->getDomPDF();
        DocumentTypography::registerDompdfFonts($dompdf);
        $dompdf->setCallbacks([['event' => 'end_frame', 'f' => static function ($frame) use (&$measurements): void {
            $node = $frame->get_node();
            if (! $node instanceof DOMElement) {
                return;
            }
            $classes = explode(' ', $node->getAttribute('class'));
            foreach (['letter-content', 'letter-body', 'letter-signoff'] as $class) {
                if (in_array($class, $classes, true)) {
                    $measurements[$class] = $frame->get_position('y') + $frame->get_padding_box()['h'];
                }
            }
        }]]);
        $dompdf->render();

        $end = $measurements[$last ? 'letter-signoff' : 'letter-body'] ?? INF;
        $safeBottom = $measurements['letter-content'] ?? -INF;

        return $dompdf->getCanvas()->get_page_count() === 1 && $end <= $safeBottom - 3;
    }

    /** @return list<string> */
    private static function blocks(string $body): array
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8" ?><div>'.$body.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $blocks = [];
        $inline = '';
        $flushInline = static function () use (&$inline, &$blocks): void {
            if (trim(strip_tags($inline)) !== '') {
                $blocks[] = '<p>'.$inline.'</p>';
            }
            $inline = '';
        };

        foreach ($document->documentElement->childNodes as $node) {
            if ($node instanceof DOMElement && in_array($node->tagName, ['ul', 'ol'], true)) {
                $flushInline();
                foreach ($node->childNodes as $item) {
                    if ($item instanceof DOMElement && $item->tagName === 'li') {
                        $blocks[] = '<'.$node->tagName.'>'.$document->saveHTML($item).'</'.$node->tagName.'>';
                    }
                }
            } elseif ($node instanceof DOMElement && $node->tagName === 'p') {
                $flushInline();
                $blocks[] = $document->saveHTML($node);
            } else {
                $inline .= $node instanceof DOMElement ? $document->saveHTML($node) : e($node->textContent);
            }
        }
        $flushInline();

        return $blocks;
    }

    /** @return list<string> */
    private static function splitBlock(string $block): array
    {
        $text = trim(html_entity_decode(strip_tags(preg_replace('/<br\s*\/?\s*>/i', ' ', $block)), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $words = preg_split('/\s+/u', $text) ?: [];
        if (count($words) < 2) {
            if (mb_strlen($text) < 2) {
                return [$block];
            }
            $middle = intdiv(mb_strlen($text), 2);

            return [
                '<p>'.e(mb_substr($text, 0, $middle)).'</p>',
                '<p>'.e(mb_substr($text, $middle)).'</p>',
            ];
        }

        $middle = intdiv(count($words), 2);

        return [
            '<p>'.e(implode(' ', array_slice($words, 0, $middle))).'</p>',
            '<p>'.e(implode(' ', array_slice($words, $middle))).'</p>',
        ];
    }
}
