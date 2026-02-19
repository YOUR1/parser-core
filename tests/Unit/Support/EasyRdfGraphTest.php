<?php

declare(strict_types=1);

use EasyRdf\Graph;
use EasyRdf\Resource;
use Youri\vandenBogert\Software\ParserCore\Contracts\GraphInterface;
use Youri\vandenBogert\Software\ParserCore\Support\EasyRdfGraph;

$turtleContent = <<<'TURTLE'
@prefix owl: <http://www.w3.org/2002/07/owl#> .
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
@prefix ex: <http://example.org/> .

ex:Person a owl:Class ;
    rdfs:label "Person"@en .

ex:name a owl:DatatypeProperty ;
    rdfs:domain ex:Person .
TURTLE;

describe('EasyRdfGraph', function () use ($turtleContent): void {
    it('implements GraphInterface', function (): void {
        $easyRdfGraph = new EasyRdfGraph();

        expect($easyRdfGraph)->toBeInstanceOf(GraphInterface::class);
    });

    it('is a final class', function (): void {
        $reflection = new ReflectionClass(EasyRdfGraph::class);

        expect($reflection->isFinal())->toBeTrue();
    });

    it('creates an empty graph with default construction', function (): void {
        $easyRdfGraph = new EasyRdfGraph();

        expect($easyRdfGraph->resources())->toBeArray();
        expect($easyRdfGraph->resources())->toBeEmpty();
        expect($easyRdfGraph->getNamespaceMap())->toBeArray();
    });

    it('delegates to injected EasyRdf Graph instance', function (): void {
        $innerGraph = new Graph();
        $easyRdfGraph = new EasyRdfGraph($innerGraph);

        expect($easyRdfGraph->getInnerGraph())->toBe($innerGraph);
    });

    it('delegates parse() to EasyRdf and returns triple count', function () use ($turtleContent): void {
        $easyRdfGraph = new EasyRdfGraph();
        $tripleCount = $easyRdfGraph->parse($turtleContent, 'turtle');

        expect($tripleCount)->toBeInt();
        // The Turtle content defines exactly 4 triples:
        // 1. ex:Person a owl:Class
        // 2. ex:Person rdfs:label "Person"@en
        // 3. ex:name a owl:DatatypeProperty
        // 4. ex:name rdfs:domain ex:Person
        expect($tripleCount)->toBe(4);
    });

    it('returns all resources from underlying graph after parsing', function () use ($turtleContent): void {
        $easyRdfGraph = new EasyRdfGraph();
        $easyRdfGraph->parse($turtleContent, 'turtle');

        $resources = $easyRdfGraph->resources();

        expect($resources)->toBeArray();
        expect($resources)->not->toBeEmpty();

        // Verify at least our named resources are present
        $uris = array_map(fn (Resource $r) => $r->getUri(), $resources);
        expect($uris)->toContain('http://example.org/Person');
        expect($uris)->toContain('http://example.org/name');
    });

    it('returns a Resource for a given URI via resource()', function () use ($turtleContent): void {
        $easyRdfGraph = new EasyRdfGraph();
        $easyRdfGraph->parse($turtleContent, 'turtle');

        $resource = $easyRdfGraph->resource('http://example.org/Person');

        expect($resource)->toBeInstanceOf(Resource::class);
        expect($resource->getUri())->toBe('http://example.org/Person');
    });

    it('returns a Resource with type assigned via resource($uri, $type)', function (): void {
        $easyRdfGraph = new EasyRdfGraph();
        $resource = $easyRdfGraph->resource('http://example.org/NewThing', 'owl:Class');

        expect($resource)->toBeInstanceOf(Resource::class);
        expect($resource->getUri())->toBe('http://example.org/NewThing');
        expect($resource->isA('owl:Class'))->toBeTrue();
    });

    it('returns only resources of the specified type via allOfType()', function () use ($turtleContent): void {
        $easyRdfGraph = new EasyRdfGraph();
        $easyRdfGraph->parse($turtleContent, 'turtle');

        $classes = $easyRdfGraph->allOfType('owl:Class');

        expect($classes)->toBeArray();
        expect($classes)->toHaveCount(1);
        expect($classes[0])->toBeInstanceOf(Resource::class);
        expect($classes[0]->getUri())->toBe('http://example.org/Person');
    });

    it('returns empty array when no resources of that type exist via allOfType()', function () use ($turtleContent): void {
        $easyRdfGraph = new EasyRdfGraph();
        $easyRdfGraph->parse($turtleContent, 'turtle');

        $result = $easyRdfGraph->allOfType('owl:AnnotationProperty');

        expect($result)->toBeArray();
        expect($result)->toBeEmpty();
    });

    it('returns standard namespaces from getNamespaceMap()', function (): void {
        $easyRdfGraph = new EasyRdfGraph();
        $namespaces = $easyRdfGraph->getNamespaceMap();

        // getNamespaceMap() delegates to EasyRdf\RdfNamespace::namespaces()
        // which returns all globally registered namespaces (including defaults)
        expect($namespaces)->toBeArray();
        expect($namespaces)->not->toBeEmpty();
        expect($namespaces)->toHaveKey('rdf');
        expect($namespaces)->toHaveKey('rdfs');
        expect($namespaces)->toHaveKey('owl');
        expect($namespaces)->toHaveKey('xsd');
    });

    it('includes custom registered namespaces in getNamespaceMap()', function (): void {
        // Register a custom namespace
        \EasyRdf\RdfNamespace::set('testns', 'http://test.example.org/');

        $easyRdfGraph = new EasyRdfGraph();
        $namespaces = $easyRdfGraph->getNamespaceMap();

        expect($namespaces)->toHaveKey('testns');
        expect($namespaces['testns'])->toBe('http://test.example.org/');

        // Cleanup
        \EasyRdf\RdfNamespace::delete('testns');
    });

    it('returns the underlying EasyRdf Graph instance via getInnerGraph()', function (): void {
        $easyRdfGraph = new EasyRdfGraph();
        $innerGraph = $easyRdfGraph->getInnerGraph();

        expect($innerGraph)->toBeInstanceOf(Graph::class);
    });

    it('returns the same Graph object passed to constructor via getInnerGraph()', function (): void {
        $injectedGraph = new Graph();
        $easyRdfGraph = new EasyRdfGraph($injectedGraph);

        expect($easyRdfGraph->getInnerGraph())->toBe($injectedGraph);
    });

    it('delegates parse() with base URI parameter', function (): void {
        $turtleWithRelativeUri = <<<'TURTLE'
        @prefix ex: <http://example.org/> .

        <#Thing> a ex:Class .
        TURTLE;

        $easyRdfGraph = new EasyRdfGraph();
        $tripleCount = $easyRdfGraph->parse($turtleWithRelativeUri, 'turtle', 'http://base.example.org/');

        expect($tripleCount)->toBeInt();
        expect($tripleCount)->toBe(1);

        // Verify the base URI was used to resolve the relative URI
        $resource = $easyRdfGraph->resource('http://base.example.org/#Thing');
        expect($resource)->toBeInstanceOf(Resource::class);
        expect($resource->getUri())->toBe('http://base.example.org/#Thing');
    });

    it('propagates exception from parse() with invalid format', function (): void {
        $easyRdfGraph = new EasyRdfGraph();

        expect(fn () => $easyRdfGraph->parse('<data/>', 'not_a_real_format'))
            ->toThrow(\EasyRdf\Exception::class);
    });
});
