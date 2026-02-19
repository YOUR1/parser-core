<?php

declare(strict_types=1);

use Youri\vandenBogert\Software\ParserCore\ValueObjects\ParsedOntology;

describe('ParsedOntology Value Object', function (): void {

    it('constructs with all arguments provided', function (): void {
        $ontology = new ParsedOntology(
            classes: ['http://example.org/Class1' => ['label' => 'Class 1']],
            properties: ['http://example.org/prop1' => ['label' => 'Property 1']],
            prefixes: ['ex' => 'http://example.org/'],
            shapes: ['http://example.org/Shape1' => ['targetClass' => 'http://example.org/Class1']],
            restrictions: ['http://example.org/r1' => ['onProperty' => 'http://example.org/prop1']],
            metadata: ['version' => '1.0'],
            rawContent: '@prefix ex: <http://example.org/> .',
        );

        expect($ontology->classes)->toBe(['http://example.org/Class1' => ['label' => 'Class 1']]);
        expect($ontology->properties)->toBe(['http://example.org/prop1' => ['label' => 'Property 1']]);
        expect($ontology->prefixes)->toBe(['ex' => 'http://example.org/']);
        expect($ontology->shapes)->toBe(['http://example.org/Shape1' => ['targetClass' => 'http://example.org/Class1']]);
        expect($ontology->restrictions)->toBe(['http://example.org/r1' => ['onProperty' => 'http://example.org/prop1']]);
        expect($ontology->metadata)->toBe(['version' => '1.0']);
        expect($ontology->rawContent)->toBe('@prefix ex: <http://example.org/> .');
    });

    it('constructs with no arguments using all defaults', function (): void {
        $ontology = new ParsedOntology();

        expect($ontology->classes)->toBe([]);
        expect($ontology->properties)->toBe([]);
        expect($ontology->prefixes)->toBe([]);
        expect($ontology->shapes)->toBe([]);
        expect($ontology->restrictions)->toBe([]);
        expect($ontology->metadata)->toBe([]);
        expect($ontology->rawContent)->toBe('');
    });

    it('constructs with partial named arguments', function (): void {
        $ontology = new ParsedOntology(
            classes: ['http://example.org/Person' => ['label' => 'Person']],
            rawContent: '@prefix ex: <http://example.org/> .',
        );

        expect($ontology->classes)->toBe(['http://example.org/Person' => ['label' => 'Person']]);
        expect($ontology->rawContent)->toBe('@prefix ex: <http://example.org/> .');
        expect($ontology->properties)->toBe([]);
        expect($ontology->prefixes)->toBe([]);
        expect($ontology->shapes)->toBe([]);
        expect($ontology->restrictions)->toBe([]);
        expect($ontology->metadata)->toBe([]);
    });

    it('allows readonly property access for all seven properties', function (): void {
        $ontology = new ParsedOntology();

        expect($ontology->classes)->toBeArray();
        expect($ontology->properties)->toBeArray();
        expect($ontology->prefixes)->toBeArray();
        expect($ontology->shapes)->toBeArray();
        expect($ontology->restrictions)->toBeArray();
        expect($ontology->metadata)->toBeArray();
        expect($ontology->rawContent)->toBeString();
    });

    it('has all properties marked as readonly via Reflection', function (): void {
        $reflection = new ReflectionClass(ParsedOntology::class);
        $propertyNames = ['classes', 'properties', 'prefixes', 'shapes', 'restrictions', 'metadata', 'rawContent'];

        foreach ($propertyNames as $propertyName) {
            $property = $reflection->getProperty($propertyName);
            expect($property->isReadOnly())->toBeTrue(
                "Property '{$propertyName}' should be readonly"
            );
        }
    });

    it('never has null array properties - always empty array when not provided', function (): void {
        $ontology = new ParsedOntology();

        expect($ontology->classes)->not->toBeNull();
        expect($ontology->properties)->not->toBeNull();
        expect($ontology->prefixes)->not->toBeNull();
        expect($ontology->shapes)->not->toBeNull();
        expect($ontology->restrictions)->not->toBeNull();
        expect($ontology->metadata)->not->toBeNull();

        expect($ontology->classes)->toBe([]);
        expect($ontology->properties)->toBe([]);
        expect($ontology->prefixes)->toBe([]);
        expect($ontology->shapes)->toBe([]);
        expect($ontology->restrictions)->toBe([]);
        expect($ontology->metadata)->toBe([]);
    });

    it('throws Error when attempting to reassign a readonly property', function (): void {
        $ontology = new ParsedOntology(
            classes: ['http://example.org/Class1' => ['label' => 'Class 1']],
        );

        expect(fn () => $ontology->classes = [])->toThrow(\Error::class);
    });

    it('constructs with realistic ontology data', function (): void {
        $classes = [
            'http://xmlns.com/foaf/0.1/Person' => [
                'label' => ['en' => 'Person'],
                'comment' => ['en' => 'A person.'],
                'subClassOf' => ['http://xmlns.com/foaf/0.1/Agent'],
            ],
            'http://xmlns.com/foaf/0.1/Organization' => [
                'label' => ['en' => 'Organization'],
                'comment' => ['en' => 'An organization.'],
                'subClassOf' => ['http://xmlns.com/foaf/0.1/Agent'],
            ],
        ];

        $properties = [
            'http://xmlns.com/foaf/0.1/name' => [
                'label' => ['en' => 'name'],
                'domain' => ['http://xmlns.com/foaf/0.1/Agent'],
                'range' => ['http://www.w3.org/2001/XMLSchema#string'],
            ],
            'http://xmlns.com/foaf/0.1/mbox' => [
                'label' => ['en' => 'personal mailbox'],
                'domain' => ['http://xmlns.com/foaf/0.1/Agent'],
            ],
        ];

        $prefixes = [
            'foaf' => 'http://xmlns.com/foaf/0.1/',
            'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
            'owl' => 'http://www.w3.org/2002/07/owl#',
            'xsd' => 'http://www.w3.org/2001/XMLSchema#',
        ];

        $shapes = [
            'http://example.org/PersonShape' => [
                'targetClass' => 'http://xmlns.com/foaf/0.1/Person',
                'properties' => [
                    ['path' => 'http://xmlns.com/foaf/0.1/name', 'minCount' => 1],
                ],
            ],
        ];

        $restrictions = [
            'http://example.org/r1' => [
                'onProperty' => 'http://xmlns.com/foaf/0.1/name',
                'cardinality' => 1,
            ],
        ];

        $metadata = [
            'ontologyIRI' => 'http://xmlns.com/foaf/0.1/',
            'versionIRI' => 'http://xmlns.com/foaf/0.1/20140114',
        ];

        $rawContent = '@prefix foaf: <http://xmlns.com/foaf/0.1/> .';

        $ontology = new ParsedOntology(
            classes: $classes,
            properties: $properties,
            prefixes: $prefixes,
            shapes: $shapes,
            restrictions: $restrictions,
            metadata: $metadata,
            rawContent: $rawContent,
        );

        expect($ontology->classes)->toHaveCount(2);
        expect($ontology->classes)->toHaveKey('http://xmlns.com/foaf/0.1/Person');
        expect($ontology->classes)->toHaveKey('http://xmlns.com/foaf/0.1/Organization');
        expect($ontology->properties)->toHaveCount(2);
        expect($ontology->prefixes)->toHaveCount(4);
        expect($ontology->prefixes['foaf'])->toBe('http://xmlns.com/foaf/0.1/');
        expect($ontology->shapes)->toHaveCount(1);
        expect($ontology->restrictions)->toHaveCount(1);
        expect($ontology->metadata['ontologyIRI'])->toBe('http://xmlns.com/foaf/0.1/');
        expect($ontology->rawContent)->toBe($rawContent);
    });

});
