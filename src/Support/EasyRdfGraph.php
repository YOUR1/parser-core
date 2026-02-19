<?php

declare(strict_types=1);

namespace Youri\vandenBogert\Software\ParserCore\Support;

use EasyRdf\Graph;
use EasyRdf\Resource;
use Youri\vandenBogert\Software\ParserCore\Contracts\GraphInterface;

/**
 * EasyRdf implementation of GraphInterface.
 *
 * Delegates all graph operations to an underlying EasyRdf\Graph instance.
 * This is the default (and currently only) implementation of GraphInterface.
 */
final class EasyRdfGraph implements GraphInterface
{
    private Graph $graph;

    public function __construct(?Graph $graph = null)
    {
        $this->graph = $graph ?? new Graph();
    }

    public function resource(string $uri, ?string $type = null): Resource
    {
        // Explicit null check for defensive coding: EasyRdf's resource() method
        // behaves differently when $type is passed (even as null) versus omitted,
        // so we branch explicitly to avoid relying on EasyRdf internal defaults.
        if ($type !== null) {
            return $this->graph->resource($uri, $type);
        }

        return $this->graph->resource($uri);
    }

    /**
     * @return array<string, Resource>
     */
    public function resources(): array
    {
        return $this->graph->resources();
    }

    /**
     * @return array<string, Resource>
     */
    public function allOfType(string $type): array
    {
        return $this->graph->allOfType($type);
    }

    /**
     * Get the prefix-to-namespace mapping.
     *
     * @note This delegates to the global EasyRdf\RdfNamespace::namespaces() registry,
     *       not to the graph instance. EasyRdf manages namespaces as global static state,
     *       meaning all graphs share the same namespace map. This is an EasyRdf design
     *       constraint, not a per-graph feature.
     *
     * @return array<string, string>
     */
    public function getNamespaceMap(): array
    {
        return \EasyRdf\RdfNamespace::namespaces();
    }

    public function parse(string $data, string $format, ?string $uri = null): int
    {
        return $this->graph->parse($data, $format, $uri);
    }

    /**
     * Get the underlying EasyRdf\Graph instance.
     *
     * This accessor is available on the concrete class only (not on GraphInterface)
     * to allow transition-period code that still requires direct EasyRdf\Graph access.
     * Once all consumers are migrated to use GraphInterface, this method can be
     * removed or deprecated.
     */
    public function getInnerGraph(): Graph
    {
        return $this->graph;
    }
}
