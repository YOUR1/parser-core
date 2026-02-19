<?php

declare(strict_types=1);

use App\Services\Ontology\Parsers\ValueObjects\ParsedRdf;
use EasyRdf\Graph;

describe('ParsedRdf Value Object', function () {

    it('constructs with all four promoted readonly properties', function () {
        $graph = new Graph();
        $parsedRdf = new ParsedRdf($graph, 'turtle', '@prefix ex: <http://example.org/> .', ['source' => 'test']);

        expect($parsedRdf->graph)->toBe($graph)
            ->and($parsedRdf->format)->toBe('turtle')
            ->and($parsedRdf->rawContent)->toBe('@prefix ex: <http://example.org/> .')
            ->and($parsedRdf->metadata)->toBe(['source' => 'test']);
    });

    it('defaults metadata to empty array when omitted', function () {
        $graph = new Graph();
        $parsedRdf = new ParsedRdf($graph, 'turtle', '');

        expect($parsedRdf->metadata)->toBe([]);
    });

    it('returns correct resource count for a graph with known resources', function () {
        $graph = new Graph();
        $graph->resource('http://example.org/Person', 'owl:Class');
        $graph->resource('http://example.org/Animal', 'owl:Class');

        $parsedRdf = new ParsedRdf($graph, 'turtle', '');

        // EasyRdf counts 3: Person, Animal, and the owl:Class type resource itself
        expect($parsedRdf->getResourceCount())->toBe(3);
    });

    it('returns isEmpty true for empty graph', function () {
        $graph = new Graph();
        $parsedRdf = new ParsedRdf($graph, 'turtle', '');

        expect($parsedRdf->isEmpty())->toBeTrue();
    });

    it('returns isEmpty false for populated graph', function () {
        $graph = new Graph();
        $graph->resource('http://example.org/Person', 'owl:Class');

        $parsedRdf = new ParsedRdf($graph, 'turtle', '');

        expect($parsedRdf->isEmpty())->toBeFalse();
    });

    it('returns resources from the graph', function () {
        $graph = new Graph();
        $graph->resource('http://example.org/Person', 'owl:Class');

        $parsedRdf = new ParsedRdf($graph, 'turtle', '');
        $resources = $parsedRdf->getResources();

        expect($resources)->toBeArray()
            ->and($resources)->not->toBeEmpty();
    });

    it('returns expected toArray structure', function () {
        $graph = new Graph();
        $graph->resource('http://example.org/Person', 'owl:Class');

        $parsedRdf = new ParsedRdf($graph, 'turtle', 'raw content here', ['key' => 'value']);
        $array = $parsedRdf->toArray();

        expect($array)->toHaveKeys(['format', 'resource_count', 'metadata'])
            ->and($array['format'])->toBe('turtle')
            ->and($array['metadata'])->toBe(['key' => 'value'])
            ->and($array['resource_count'])->toBeInt();
    });

    it('does not include rawContent or graph in toArray', function () {
        $graph = new Graph();
        $parsedRdf = new ParsedRdf($graph, 'turtle', 'raw content');
        $array = $parsedRdf->toArray();

        expect($array)->not->toHaveKey('rawContent')
            ->and($array)->not->toHaveKey('raw_content')
            ->and($array)->not->toHaveKey('graph');
    });

    it('has readonly properties that cannot be reassigned', function () {
        $graph = new Graph();
        $parsedRdf = new ParsedRdf($graph, 'turtle', 'content');

        $reflection = new ReflectionClass($parsedRdf);
        $formatProp = $reflection->getProperty('format');

        expect($formatProp->isReadOnly())->toBeTrue();
    });
});
