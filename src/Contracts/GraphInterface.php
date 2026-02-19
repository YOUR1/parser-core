<?php

declare(strict_types=1);

namespace Youri\vandenBogert\Software\ParserCore\Contracts;

use EasyRdf\Resource;

/**
 * Thin wrapper interface over RDF graph operations.
 *
 * Provides the minimal set of graph-level methods used by downstream packages
 * (extractors, parsers, traits). Enables future replacement of the underlying
 * RDF graph engine (currently EasyRdf) without touching consumer code.
 *
 * This interface intentionally wraps ONLY the methods currently in use.
 * Additional methods should be added only when concrete usage is identified.
 *
 * @note The coupling to EasyRdf\Resource in method signatures is intentional for
 *       the MVP implementation. A ResourceInterface abstraction is deferred to
 *       post-MVP. See the architecture document for rationale.
 */
interface GraphInterface
{
    /**
     * Get or create a resource in the graph by URI.
     *
     * Used by OwlParser to look up resources by URI for OWL-specific processing.
     * When $type is provided, the resource is assigned that RDF type.
     *
     * @param string      $uri  The URI of the resource
     * @param string|null $type Optional RDF type URI to assign (e.g., 'owl:Class')
     */
    public function resource(string $uri, ?string $type = null): Resource;

    /**
     * Get all resources in the graph.
     *
     * Used by ClassExtractor, PropertyExtractor, PrefixExtractor, ShapeExtractor,
     * and RdfParser to iterate over all resources for type-based filtering.
     *
     * @return array<string, Resource>
     */
    public function resources(): array;

    /**
     * Get all resources of a specific RDF type.
     *
     * Used by OwlParser to find all instances of owl:Ontology, owl:NamedIndividual,
     * owl:AllDifferent, and rdfs:Datatype.
     *
     * @param string $type The RDF type URI (e.g., 'owl:Ontology')
     * @return array<string, Resource>
     */
    public function allOfType(string $type): array;

    /**
     * Get the prefix-to-namespace mapping.
     *
     * Used by PrefixExtractor to retrieve namespace declarations that were
     * registered during parsing.
     *
     * @note This returns globally registered namespaces (from the RDF namespace
     *       registry), not per-graph namespaces. All graphs share the same
     *       namespace map.
     *
     * @return array<string, string> Prefix => namespace URI mapping
     */
    public function getNamespaceMap(): array;

    /**
     * Parse RDF content into the graph.
     *
     * Used by format handlers to populate the graph from serialized RDF content.
     *
     * @param string      $data   The RDF content to parse
     * @param string      $format The format identifier (e.g., 'turtle', 'rdfxml', 'ntriples', 'jsonld')
     * @param string|null $uri    Optional base URI for relative URI resolution
     * @return int The number of triples parsed
     */
    public function parse(string $data, string $format, ?string $uri = null): int;
}
