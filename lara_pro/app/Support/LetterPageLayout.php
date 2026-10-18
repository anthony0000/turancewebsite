<?php

namespace App\Support;

use DOMDocument;
use DOMElement;

final class LetterPageLayout
{
    /**
     * Keep every page inside the letterhead's 68–249 mm writing area.
     * The limits leave room for font and line-wrap differences in Dompdf.
     *
     * @return array<int, array{body: string, first: bool, last: bool}>
     */
    public static function pages(array $letter): array
    {
        $firstLimit = max(15, 30
            - min(5, substr_count((string) ($letter['recipient_address'] ?? ''), "\n"))
            - (filled($letter['recipient_role'] ?? null) ? 1 : 0)
            - (filled($letter['recipient_company'] ?? null) ? 1 : 0)
            - (filled($letter['subject'] ?? null) ? 2 : 0));
        $laterLimit = 36;
        $pages = [['blocks' => [], 'units' => 0]];

        foreach (self::blocks((string) $letter['body']) as $block) {
            foreach (self::splitLargeBlock($block, min($firstLimit, $laterLimit) - 5) as $part) {
                $index = count($pages) - 1;
                $limit = $index === 0 ? $firstLimit : $laterLimit;
                $units = self::units($part);
                if ($pages[$index]['units'] > 0 && $pages[$index]['units'] + $units > $limit) {
                    $pages[] = ['blocks' => [], 'units' => 0];
                    $index++;
                }
                $pages[$index]['blocks'][] = $part;
                $pages[$index]['units'] += $units;
            }
        }

        $last = count($pages) - 1;
        if ($pages[$last]['units'] > ($last === 0 ? $firstLimit : $laterLimit) - 5) {
            $moved = array_pop($pages[$last]['blocks']);
            if ($moved !== null) {
                $pages[$last]['units'] -= self::units($moved);
                $pages[] = ['blocks' => [$moved], 'units' => self::units($moved)];
            }
        }

        return array_map(static fn (array $page, int $index): array => [
            'body' => implode('', $page['blocks']),
            'first' => $index === 0,
            'last' => $index === count($pages) - 1,
        ], $pages, array_keys($pages));
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
    private static function splitLargeBlock(string $block, int $limit): array
    {
        if (self::units($block) <= $limit) {
            return [$block];
        }

        $text = trim(html_entity_decode(strip_tags($block), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $words = preg_split('/\s+/u', $text) ?: [];
        $parts = [];
        $current = '';
        $maxCharacters = ($limit - 2) * 90;
        foreach ($words as $word) {
            if ($current !== '' && mb_strlen($current.' '.$word) > $maxCharacters) {
                $parts[] = '<p>'.e($current).'</p>';
                $current = '';
            }
            $current .= ($current === '' ? '' : ' ').$word;
        }
        if ($current !== '') {
            $parts[] = '<p>'.e($current).'</p>';
        }

        return $parts;
    }

    private static function units(string $html): int
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $lineBreaks = substr_count($html, '<br');

        return max(1, (int) ceil(mb_strlen($text) / 90) + $lineBreaks + 1);
    }
}
