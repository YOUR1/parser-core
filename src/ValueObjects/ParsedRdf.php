<?php

declare(strict_types=1);

namespace Youri\vandenBogert\Software\ParserCore\ValueObjects;

use EasyRdf\Graph;

/**
 * Value object representing parsed RDF data.
 *
 * Wraps an EasyRdf\Graph with format metadata. Produced by format handlers,
 * consumed by extractors.
 */
final class ParsedRdf
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        public readonly Graph $graph,
        public readonly string $format,
        public readonly string $rawContent,
        public readonly array $metadata = [],
    ) {}

    /**
     * Get the number of resources in the graph.
     *
     * @return int
     */
    public function getResourceCount(): int
    {
        return count($this->graph->resources());
    }

    /**
     * Check if the graph is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->getResourceCount() === 0;
    }

    /**
     * Get all resources from the graph.
     *
     * @return array<\EasyRdf\Resource>
     */
    public function getResources(): array
    {
        return $this->graph->resources();
    }

    /**
     * Convert to array representation.
     *
     * Note: rawContent and graph are intentionally excluded.
     *
     * @return array{format: string, resource_count: int, metadata: array<string, mixed>}
     */
    public function toArray(): array
    {
        return [
            'format' => $this->format,
            'resource_count' => $this->getResourceCount(),
            'metadata' => $this->metadata,
        ];
    }
}
