<?php

declare(strict_types=1);

use App\Services\Ontology\Parsers\OntologyParserInterface;
use Youri\vandenBogert\Software\ParserCore\ValueObjects\ParsedOntology;

describe('OntologyParserInterface', function () {

    it('defines parse method that accepts string and optional options array, returns ParsedOntology', function () {
        $reflection = new ReflectionClass(OntologyParserInterface::class);
        $method = $reflection->getMethod('parse');

        expect($method->getNumberOfRequiredParameters())->toBe(1)
            ->and($method->getNumberOfParameters())->toBe(2)
            ->and($method->getParameters()[0]->getType()->getName())->toBe('string')
            ->and($method->getParameters()[1]->getType()->getName())->toBe('array')
            ->and($method->getParameters()[1]->isOptional())->toBeTrue()
            ->and($method->getReturnType()->getName())->toBe(ParsedOntology::class);
    });

    it('defines canParse method that accepts string and returns bool', function () {
        $reflection = new ReflectionClass(OntologyParserInterface::class);
        $method = $reflection->getMethod('canParse');

        expect($method->getNumberOfParameters())->toBe(1)
            ->and($method->getParameters()[0]->getType()->getName())->toBe('string')
            ->and($method->getReturnType()->getName())->toBe('bool');
    });

    it('defines getSupportedFormats method that returns array', function () {
        $reflection = new ReflectionClass(OntologyParserInterface::class);
        $method = $reflection->getMethod('getSupportedFormats');

        expect($method->getNumberOfParameters())->toBe(0)
            ->and($method->getReturnType()->getName())->toBe('array');
    });

    it('can be implemented by an anonymous class', function () {
        $parser = new class implements OntologyParserInterface {
            public function parse(string $content, array $options = []): ParsedOntology
            {
                return new ParsedOntology();
            }

            public function canParse(string $content): bool
            {
                return true;
            }

            public function getSupportedFormats(): array
            {
                return ['turtle', 'rdfxml'];
            }
        };

        expect($parser)->toBeInstanceOf(OntologyParserInterface::class)
            ->and($parser->parse('content'))->toBeInstanceOf(ParsedOntology::class)
            ->and($parser->canParse('content'))->toBeTrue()
            ->and($parser->getSupportedFormats())->toBe(['turtle', 'rdfxml']);
    });
});