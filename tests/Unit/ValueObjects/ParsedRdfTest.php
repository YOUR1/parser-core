<?php

declare(strict_types=1);

use EasyRdf\Graph;
use Youri\vandenBogert\Software\ParserCore\ValueObjects\ParsedRdf;

describe('ParsedRdf Value Object', function (): void {

    it('constructs with all four promoted readonly properties', function (): void {
        $graph = new Graph();
        $graph->addLiteral('http://example.org/resource1', 'rdfs:label', 'Test');

        $parsed = new ParsedRdf(
            graph: $graph,
            format: 'turtle',
            rawContent: '@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .',
            metadata: ['source' => 'test'],
        );

        expect($parsed->graph)->toBe($graph);
        expect($parsed->format)->toBe('turtle');
        expect($parsed->rawContent)->toBe('@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .');
        expect($parsed->metadata)->toBe(['source' => 'test']);
    });

    it('defaults metadata to empty array when omitted', function (): void {
        $graph = new Graph();

        $parsed = new ParsedRdf(
            graph: $graph,
            format: 'rdfxml',
            rawContent: '<rdf:RDF></rdf:RDF>',
        );

        expect($parsed->metadata)->toBe([]);
    });

    it('allows readonly property access for all four properties', function (): void {
        $graph = new Graph();
        $format = 'jsonld';
        $rawContent = '{}';
        $metadata = ['version' => '1.0'];

        $parsed = new ParsedRdf($graph, $format, $rawContent, $metadata);

        expect($parsed->graph)->toBeInstanceOf(Graph::class);
        expect($parsed->format)->toBeString();
        expect($parsed->rawContent)->toBeString();
        expect($parsed->metadata)->toBeArray();
    });

    it('has all properties marked as readonly via Reflection', function (): void {
        $reflection = new ReflectionClass(ParsedRdf::class);
        $properties = ['graph', 'format', 'rawContent', 'metadata'];

        foreach ($properties as $propertyName) {
            $property = $reflection->getProperty($propertyName);
            expect($property->isReadOnly())->toBeTrue(
                "Property '{$propertyName}' should be readonly"
            );
        }
    });

    it('returns resource count of 0 for empty graph', function (): void {
        $graph = new Graph();
        $parsed = new ParsedRdf($graph, 'turtle', '');

        expect($parsed->getResourceCount())->toBe(0);
    });

    it('returns correct resource count for populated graph', function (): void {
        $graph = new Graph();
        $graph->addLiteral('http://example.org/resource1', 'rdfs:label', 'Resource 1');
        $graph->addLiteral('http://example.org/resource2', 'rdfs:label', 'Resource 2');

        $parsed = new ParsedRdf($graph, 'turtle', '');

        expect($parsed->getResourceCount())->toBe(2);
    });

    it('returns isEmpty true for empty graph', function (): void {
        $graph = new Graph();
        $parsed = new ParsedRdf($graph, 'turtle', '');

        expect($parsed->isEmpty())->toBeTrue();
    });

    it('returns isEmpty false for populated graph', function (): void {
        $graph = new Graph();
        $graph->addLiteral('http://example.org/resource1', 'rdfs:label', 'Test');

        $parsed = new ParsedRdf($graph, 'turtle', '');

        expect($parsed->isEmpty())->toBeFalse();
    });

    it('returns resources from the graph', function (): void {
        $graph = new Graph();
        $graph->addLiteral('http://example.org/resource1', 'rdfs:label', 'Test');

        $parsed = new ParsedRdf($graph, 'turtle', '');

        $resources = $parsed->getResources();
        expect($resources)->toBeArray();
        expect($resources)->not->toBeEmpty();
    });

    it('returns expected toArray structure with only format, resource_count, metadata', function (): void {
        $graph = new Graph();
        $graph->addLiteral('http://example.org/resource1', 'rdfs:label', 'Test');

        $parsed = new ParsedRdf(
            graph: $graph,
            format: 'turtle',
            rawContent: '@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .',
            metadata: ['source' => 'file.ttl'],
        );

        $array = $parsed->toArray();

        expect($array)->toHaveKeys(['format', 'resource_count', 'metadata']);
        expect($array)->toHaveCount(3);
        expect($array['format'])->toBe('turtle');
        expect($array['resource_count'])->toBeInt();
        expect($array['metadata'])->toBe(['source' => 'file.ttl']);
    });

    it('does not include rawContent or graph in toArray', function (): void {
        $graph = new Graph();

        $parsed = new ParsedRdf(
            graph: $graph,
            format: 'turtle',
            rawContent: 'some content',
            metadata: [],
        );

        $array = $parsed->toArray();

        expect($array)->not->toHaveKey('rawContent');
        expect($array)->not->toHaveKey('raw_content');
        expect($array)->not->toHaveKey('graph');
    });

    it('throws Error when attempting to reassign a readonly property', function (): void {
        $graph = new Graph();
        $parsed = new ParsedRdf($graph, 'turtle', '');

        expect(fn () => $parsed->format = 'rdfxml')->toThrow(\Error::class);
    });

});
