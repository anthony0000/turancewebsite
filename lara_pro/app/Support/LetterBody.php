<?php

namespace App\Support;

use DOMDocument;
use DOMNode;

class LetterBody
{
    private const ALLOWED = ['p', 'br', 'strong', 'em', 'u', 'ul', 'ol', 'li'];

    public static function clean(?string $body): string
    {
        $body = trim((string) $body);

        if ($body === '') {
            return '';
        }

        // Existing plain text submissions remain readable in the PDF.
        if (! preg_match('/<\/?[a-z][^>]*>/i', $body)) {
            return nl2br(e($body), false);
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8" ?><div>'.$body.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $result = '';
        foreach ($document->documentElement->childNodes as $child) {
            $result .= self::render($child);
        }

        return trim($result);
    }

    private static function render(DOMNode $node): string
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            return e($node->nodeValue);
        }

        if ($node->nodeType !== XML_ELEMENT_NODE) {
            return '';
        }

        $tag = strtolower($node->nodeName);
        if (in_array($tag, ['script', 'style', 'iframe', 'svg', 'object', 'template'], true)) {
            return '';
        }

        $tag = match ($tag) {
            'b' => 'strong',
            'i' => 'em',
            'div' => 'p',
            default => $tag,
        };

        if ($tag === 'br') {
            return '<br>';
        }

        $inside = '';
        foreach ($node->childNodes as $child) {
            $inside .= self::render($child);
        }

        return in_array($tag, self::ALLOWED, true) ? "<{$tag}>{$inside}</{$tag}>" : $inside;
    }
}
