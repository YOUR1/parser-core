<?php

declare(strict_types=1);

namespace Youri\vandenBogert\Software\ParserCore\Support;

final class RdfHelper
{
    public static function extractLocalName(string $iri): string
    {
        if (preg_match('/[#\/]([^#\/]+)$/', $iri, $matches)) {
            return $matches[1];
        }
        if (str_starts_with($iri, 'urn:') && preg_match('/:([^:]+)$/', $iri, $matches)) {
            return $matches[1];
        }

        return $iri;
    }

    public static function extractNamespace(string $iri): string
    {
        $localName = self::extractLocalName($iri);
        if ($localName === $iri) {
            return '';
        }

        return substr($iri, 0, -strlen($localName));
    }

    public static function humanizeLocalName(string $localName): string
    {
        $spaced = preg_replace('/([a-z])([A-Z])/', '$1 $2', $localName) ?? $localName;
        $spaced = str_replace('_', ' ', $spaced);

        return ucwords(strtolower($spaced));
    }
}
