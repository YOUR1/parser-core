<?php

declare(strict_types=1);

namespace Youri\vandenBogert\Software\ParserCore\ValueObjects;

/**
 * Value object representing a fully parsed ontology.
 *
 * Replaces the untyped array return from OntologyParserInterface::parse().
 * All array properties default to [] (never null).
 */
final class ParsedOntology
{
    /**
     * @param array<string, array<string, mixed>> $classes
     * @param array<string, array<string, mixed>> $properties
     * @param array<string, string> $prefixes
     * @param array<string, array<string, mixed>> $shapes
     * @param array<string, array<string, mixed>> $restrictions
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        public readonly array $classes = [],
        public readonly array $properties = [],
        public readonly array $prefixes = [],
        public readonly array $shapes = [],
        public readonly array $restrictions = [],
        public readonly array $metadata = [],
        public readonly string $rawContent = '',
    ) {}
}
