<?php

declare(strict_types=1);

namespace Youri\vandenBogert\Software\ParserCore\Contracts;

use Youri\vandenBogert\Software\ParserCore\Exceptions\ParseException;
use Youri\vandenBogert\Software\ParserCore\ValueObjects\ParsedRdf;

/**
 * Interface for RDF format-specific handlers.
 *
 * Each format handler is responsible for detecting and parsing a single
 * RDF serialization format (e.g., Turtle, RDF/XML, JSON-LD, N-Triples).
 * Implementations MUST be final classes.
 */
interface RdfFormatHandlerInterface
{
    /**
     * Check if this handler can parse the given content.
     *
     * Implementations should use lightweight heuristics (string pattern matching,
     * first N lines inspection) to quickly determine format compatibility.
     * This method MUST NOT throw exceptions.
     */
    public function canHandle(string $content): bool;

    /**
     * Parse RDF content and return a ParsedRdf value object.
     *
     * @throws ParseException When the content is recognized but parsing fails
     */
    public function parse(string $content): ParsedRdf;

    /**
     * Get the format name this handler supports.
     *
     * Returns a lowercase identifier string (e.g., 'turtle', 'rdf/xml', 'json-ld', 'n-triples').
     */
    public function getFormatName(): string;
}
