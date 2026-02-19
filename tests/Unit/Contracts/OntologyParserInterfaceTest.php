<?php

declare(strict_types=1);

use Youri\vandenBogert\Software\ParserCore\Contracts\OntologyParserInterface;
use Youri\vandenBogert\Software\ParserCore\Exceptions\ParserException;
use Youri\vandenBogert\Software\ParserCore\ValueObjects\ParsedOntology;

describe('OntologyParserInterface', function (): void {
    beforeEach(function (): void {
        $this->mock = new class implements OntologyParserInterface {
            /** @var array<string, string|int|bool|null> */
            public array $lastOptions = [];

            public function parse(string $content, array $options = []): ParsedOntology
            {
                $this->lastOptions = $options;

                return new ParsedOntology(
                    prefixes: ['ex' => 'http://example.org/'],
                    metadata: ['source' => 'test'],
                    rawContent: $content,
                );
            }

            public function canParse(string $content): bool
            {
                return str_contains($content, '@prefix');
            }

            public function getSupportedFormats(): array
            {
                return ['turtle', 'rdf/xml', 'json-ld', 'n-triples'];
            }
        };
    });

    it('is an interface', function (): void {
        $reflection = new ReflectionClass(OntologyParserInterface::class);

        expect($reflection->isInterface())->toBeTrue();
    });

    it('defines exactly 3 methods', function (): void {
        $reflection = new ReflectionClass(OntologyParserInterface::class);
        $methods = $reflection->getMethods();

        expect($methods)->toHaveCount(3);

        $methodNames = array_map(fn (ReflectionMethod $m) => $m->getName(), $methods);
        expect($methodNames)->toContain('parse');
        expect($methodNames)->toContain('canParse');
        expect($methodNames)->toContain('getSupportedFormats');
    });

    it('defines parse() with correct signature', function (): void {
        $reflection = new ReflectionMethod(OntologyParserInterface::class, 'parse');

        expect($reflection->isPublic())->toBeTrue();

        $parameters = $reflection->getParameters();
        expect($parameters)->toHaveCount(2);

        // First parameter: string $content
        expect($parameters[0]->getName())->toBe('content');
        expect($parameters[0]->getType()?->getName())->toBe('string');
        expect($parameters[0]->getType()?->allowsNull())->toBeFalse();

        // Second parameter: array $options = []
        expect($parameters[1]->getName())->toBe('options');
        expect($parameters[1]->getType()?->getName())->toBe('array');
        expect($parameters[1]->getType()?->allowsNull())->toBeFalse();

        // Return type: ParsedOntology
        $returnType = $reflection->getReturnType();
        expect($returnType)->not->toBeNull();
        expect($returnType?->getName())->toBe(ParsedOntology::class);
    });

    it('defines canParse() with correct signature', function (): void {
        $reflection = new ReflectionMethod(OntologyParserInterface::class, 'canParse');

        expect($reflection->isPublic())->toBeTrue();

        $parameters = $reflection->getParameters();
        expect($parameters)->toHaveCount(1);

        // Parameter: string $content
        expect($parameters[0]->getName())->toBe('content');
        expect($parameters[0]->getType()?->getName())->toBe('string');
        expect($parameters[0]->getType()?->allowsNull())->toBeFalse();

        // Return type: bool
        $returnType = $reflection->getReturnType();
        expect($returnType)->not->toBeNull();
        expect($returnType?->getName())->toBe('bool');
    });

    it('defines getSupportedFormats() with correct signature', function (): void {
        $reflection = new ReflectionMethod(OntologyParserInterface::class, 'getSupportedFormats');

        expect($reflection->isPublic())->toBeTrue();
        expect($reflection->getParameters())->toHaveCount(0);

        // Return type: array
        $returnType = $reflection->getReturnType();
        expect($returnType)->not->toBeNull();
        expect($returnType?->getName())->toBe('array');
    });

    it('has optional $options parameter with default empty array', function (): void {
        $reflection = new ReflectionMethod(OntologyParserInterface::class, 'parse');
        $parameters = $reflection->getParameters();

        // $options is the second parameter
        $optionsParam = $parameters[1];
        expect($optionsParam->isOptional())->toBeTrue();
        expect($optionsParam->isDefaultValueAvailable())->toBeTrue();
        expect($optionsParam->getDefaultValue())->toBe([]);
    });

    it('can be implemented by an anonymous class', function (): void {
        expect($this->mock)->toBeInstanceOf(OntologyParserInterface::class);
    });

    it('parse() returns a ParsedOntology instance with populated fields', function (): void {
        $result = $this->mock->parse('some ontology content');

        expect($result)->toBeInstanceOf(ParsedOntology::class);
        expect($result->prefixes)->toBe(['ex' => 'http://example.org/']);
        expect($result->metadata)->toBe(['source' => 'test']);
        expect($result->rawContent)->toBe('some ontology content');
    });

    it('parse() accepts optional $options array', function (): void {
        // Call without options (uses default)
        $this->mock->parse('content');
        expect($this->mock->lastOptions)->toBe([]);

        // Call with options
        $this->mock->parse('content', ['format' => 'turtle', 'language' => 'en']);
        expect($this->mock->lastOptions)->toBe(['format' => 'turtle', 'language' => 'en']);
    });

    it('canParse() returns true for parseable content', function (): void {
        expect($this->mock->canParse('@prefix ex: <http://example.org/> .'))->toBeTrue();
    });

    it('canParse() returns false for non-parseable content', function (): void {
        expect($this->mock->canParse('random text that is not ontology'))->toBeFalse();
    });

    it('canParse does not throw on empty string', function (): void {
        expect($this->mock->canParse(''))->toBeFalse();
    });

    it('getSupportedFormats() returns an array of strings', function (): void {
        $formats = $this->mock->getSupportedFormats();

        expect($formats)->toBeArray();
        expect($formats)->toHaveCount(4);
        expect($formats)->each->toBeString();
        expect($formats)->toContain('turtle');
        expect($formats)->toContain('rdf/xml');
        expect($formats)->toContain('json-ld');
        expect($formats)->toContain('n-triples');
    });

    it('parse() can throw ParserException when parsing fails', function (): void {
        $failingParser = new class implements OntologyParserInterface {
            public function parse(string $content, array $options = []): ParsedOntology
            {
                throw new ParserException('Failed to parse ontology');
            }

            public function canParse(string $content): bool
            {
                return true;
            }

            public function getSupportedFormats(): array
            {
                return [];
            }
        };

        expect(fn () => $failingParser->parse('invalid content'))
            ->toThrow(ParserException::class, 'Failed to parse ontology');
    });
});
