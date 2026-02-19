<?php

declare(strict_types=1);

namespace Youri\vandenBogert\Software\ParserCore\Contracts;

use Youri\vandenBogert\Software\ParserCore\Exceptions\ParserException;
use Youri\vandenBogert\Software\ParserCore\ValueObjects\ParsedOntology;

/**
 * Interface for ontology parsers.
 *
 * Ontology parsers orchestrate format detection, handler delegation, and data extraction.
 * The parse() method returns a typed ParsedOntology value object containing extracted
 * classes, properties, prefixes, shapes, restrictions, and metadata.
 */
interface OntologyParserInterface
{
    /**
     * Parse ontology content and return structured data.
     *
     * @param string $content The raw ontology content to parse
     * @param array<string, string|int|bool|null> $options Optional parameters (e.g., 'format', 'language')
     *
     * @throws ParserException When parsing fails (FormatDetectionException, ParseException, or ValidationException)
     */
    public function parse(string $content, array $options = []): ParsedOntology;

    /**
     * Check if this parser can handle the given content.
     *
     * Returns true if any registered handler can detect a supported format.
     * This method MUST NOT throw exceptions.
     */
    public function canParse(string $content): bool;

    /**
     * Get all formats supported by this parser.
     *
     * @return list<string> Array of format identifier strings
     */
    public function getSupportedFormats(): array;
}
