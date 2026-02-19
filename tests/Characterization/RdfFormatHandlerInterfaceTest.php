<?php

declare(strict_types=1);

use Youri\vandenBogert\Software\ParserCore\Contracts\RdfFormatHandlerInterface;
use Youri\vandenBogert\Software\ParserCore\ValueObjects\ParsedRdf;
use EasyRdf\Graph;

describe('RdfFormatHandlerInterface', function () {

    it('defines canHandle method that accepts string and returns bool', function () {
        $reflection = new ReflectionClass(RdfFormatHandlerInterface::class);
        $method = $reflection->getMethod('canHandle');

        expect($method->getNumberOfParameters())->toBe(1)
            ->and($method->getParameters()[0]->getType()->getName())->toBe('string')
            ->and($method->getReturnType()->getName())->toBe('bool');
    });

    it('defines parse method that accepts string and returns ParsedRdf', function () {
        $reflection = new ReflectionClass(RdfFormatHandlerInterface::class);
        $method = $reflection->getMethod('parse');

        expect($method->getNumberOfParameters())->toBe(1)
            ->and($method->getParameters()[0]->getType()->getName())->toBe('string')
            ->and($method->getReturnType()->getName())->toBe(ParsedRdf::class);
    });

    it('defines getFormatName method that returns string', function () {
        $reflection = new ReflectionClass(RdfFormatHandlerInterface::class);
        $method = $reflection->getMethod('getFormatName');

        expect($method->getNumberOfParameters())->toBe(0)
            ->and($method->getReturnType()->getName())->toBe('string');
    });

    it('can be implemented by an anonymous class', function () {
        $handler = new class implements RdfFormatHandlerInterface {
            public function canHandle(string $content): bool
            {
                return str_contains($content, '@prefix');
            }

            public function parse(string $content): ParsedRdf
            {
                return new ParsedRdf(new Graph(), 'test', $content);
            }

            public function getFormatName(): string
            {
                return 'test-format';
            }
        };

        expect($handler)->toBeInstanceOf(RdfFormatHandlerInterface::class)
            ->and($handler->canHandle('@prefix ex:'))->toBeTrue()
            ->and($handler->canHandle('not turtle'))->toBeFalse()
            ->and($handler->getFormatName())->toBe('test-format')
            ->and($handler->parse('content'))->toBeInstanceOf(ParsedRdf::class);
    });
});