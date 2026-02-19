<?php

declare(strict_types=1);

use Youri\vandenBogert\Software\ParserCore\Support\RdfHelper;

describe('RdfHelper::extractLocalName', function () {

    it('extracts local name from fragment URI', function () {
        expect(RdfHelper::extractLocalName('http://example.org/ns#Person'))->toBe('Person');
    });

    it('extracts local name from path URI', function () {
        expect(RdfHelper::extractLocalName('http://example.org/ontology/Person'))->toBe('Person');
    });

    it('extracts local name from URN format', function () {
        expect(RdfHelper::extractLocalName('urn:example:person'))->toBe('person');
    });

    it('returns original string when no delimiter found', function () {
        expect(RdfHelper::extractLocalName('justAString'))->toBe('justAString');
    });

    it('handles standard RDF URIs', function () {
        expect(RdfHelper::extractLocalName('http://www.w3.org/2002/07/owl#Class'))->toBe('Class');
        expect(RdfHelper::extractLocalName('http://www.w3.org/2000/01/rdf-schema#label'))->toBe('label');
        expect(RdfHelper::extractLocalName('http://www.w3.org/1999/02/22-rdf-syntax-ns#type'))->toBe('type');
    });
});

describe('RdfHelper::extractNamespace', function () {

    it('extracts namespace from fragment URI', function () {
        expect(RdfHelper::extractNamespace('http://example.org/ns#Person'))->toBe('http://example.org/ns#');
    });

    it('extracts namespace from path URI', function () {
        expect(RdfHelper::extractNamespace('http://example.org/ontology/Person'))->toBe('http://example.org/ontology/');
    });

    it('returns empty string when no delimiter found', function () {
        expect(RdfHelper::extractNamespace('justAString'))->toBe('');
    });

    it('handles standard RDF namespace URIs', function () {
        expect(RdfHelper::extractNamespace('http://www.w3.org/2002/07/owl#Class'))->toBe('http://www.w3.org/2002/07/owl#');
    });
});

describe('RdfHelper::humanizeLocalName', function () {

    it('converts camelCase to human-readable', function () {
        expect(RdfHelper::humanizeLocalName('firstName'))->toBe('First Name');
    });

    it('converts snake_case to human-readable', function () {
        expect(RdfHelper::humanizeLocalName('has_member'))->toBe('Has Member');
    });

    it('converts PascalCase to human-readable', function () {
        expect(RdfHelper::humanizeLocalName('PersonName'))->toBe('Person Name');
    });

    it('handles single word', function () {
        expect(RdfHelper::humanizeLocalName('person'))->toBe('Person');
    });

    it('handles all uppercase', function () {
        expect(RdfHelper::humanizeLocalName('URI'))->toBe('Uri');
    });
});
