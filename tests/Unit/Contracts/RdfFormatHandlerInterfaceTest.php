<?php

declare(strict_types=1);

use EasyRdf\Graph;
use Youri\vandenBogert\Software\ParserCore\Contracts\RdfFormatHandlerInterface;
use Youri\vandenBogert\Software\ParserCore\Exceptions\ParseException;
use Youri\vandenBogert\Software\ParserCore\ValueObjects\ParsedRdf;

describe('RdfFormatHandlerInterface', function (): void {
    beforeEach(function (): void {
        $this->mock = new class implements RdfFormatHandlerInterface {
            public function canHandle(string $content): bool
            {
                return str_contains($content, '@prefix');
            }

            public function parse(string $content): ParsedRdf
            {
                return new ParsedRdf(new Graph(), 'turtle', $content);
            }

            public function getFormatName(): string
            {
                return 'turtle';
            }
        };
    });

    it('is an interface', function (): void {
        $reflection = new ReflectionClass(RdfFormatHandlerInterface::class);

        expect($reflection->isInterface())->toBeTrue();
    });

    it('defines exactly 3 methods', function (): void {
        $reflection = new ReflectionClass(RdfFormatHandlerInterface::class);
        $methods = $reflection->getMethods();

        expect($methods)->toHaveCount(3);

        $methodNames = array_map(fn (ReflectionMethod $m) => $m->getName(), $methods);
        expect($methodNames)->toContain('canHandle');
        expect($methodNames)->toContain('parse');
        expect($methodNames)->toContain('getFormatName');
    });

    it('defines canHandle() with correct signature', function (): void {
        $reflection = new ReflectionMethod(RdfFormatHandlerInterface::class, 'canHandle');

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

    it('defines parse() with correct signature', function (): void {
        $reflection = new ReflectionMethod(RdfFormatHandlerInterface::class, 'parse');

        expect($reflection->isPublic())->toBeTrue();

        $parameters = $reflection->getParameters();
        expect($parameters)->toHaveCount(1);

        // Parameter: string $content
        expect($parameters[0]->getName())->toBe('content');
        expect($parameters[0]->getType()?->getName())->toBe('string');
        expect($parameters[0]->getType()?->allowsNull())->toBeFalse();

        // Return type: ParsedRdf
        $returnType = $reflection->getReturnType();
        expect($returnType)->not->toBeNull();
        expect($returnType?->getName())->toBe(ParsedRdf::class);
    });

    it('defines getFormatName() with correct signature', function (): void {
        $reflection = new ReflectionMethod(RdfFormatHandlerInterface::class, 'getFormatName');

        expect($reflection->isPublic())->toBeTrue();
        expect($reflection->getParameters())->toHaveCount(0);

        // Return type: string
        $returnType = $reflection->getReturnType();
        expect($returnType)->not->toBeNull();
        expect($returnType?->getName())->toBe('string');
    });

    it('can be implemented by an anonymous class', function (): void {
        expect($this->mock)->toBeInstanceOf(RdfFormatHandlerInterface::class);
    });

    it('canHandle() returns true for matching content', function (): void {
        expect($this->mock->canHandle('@prefix ex: <http://example.org/> .'))->toBeTrue();
    });

    it('canHandle() returns false for non-matching content', function (): void {
        expect($this->mock->canHandle('<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">'))->toBeFalse();
    });

    it('canHandle does not throw on empty string', function (): void {
        expect($this->mock->canHandle(''))->toBeFalse();
    });

    it('parse() returns a ParsedRdf instance', function (): void {
        $result = $this->mock->parse('some rdf content');

        expect($result)->toBeInstanceOf(ParsedRdf::class);
        expect($result->format)->toBe('turtle');
        expect($result->rawContent)->toBe('some rdf content');
    });

    it('getFormatName() returns a string', function (): void {
        expect($this->mock->getFormatName())->toBe('turtle');
        expect($this->mock->getFormatName())->toBeString();
    });

    it('parse() can throw ParseException when parsing fails', function (): void {
        $failingHandler = new class implements RdfFormatHandlerInterface {
            public function canHandle(string $content): bool
            {
                return true;
            }

            public function parse(string $content): ParsedRdf
            {
                throw new ParseException('Failed to parse content');
            }

            public function getFormatName(): string
            {
                return 'test';
            }
        };

        expect(fn () => $failingHandler->parse('invalid content'))
            ->toThrow(ParseException::class, 'Failed to parse content');
    });
});
