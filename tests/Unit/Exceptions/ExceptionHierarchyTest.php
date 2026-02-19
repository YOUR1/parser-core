<?php

declare(strict_types=1);

use Youri\vandenBogert\Software\ParserCore\Exceptions\FormatDetectionException;
use Youri\vandenBogert\Software\ParserCore\Exceptions\ParseException;
use Youri\vandenBogert\Software\ParserCore\Exceptions\ParserException;
use Youri\vandenBogert\Software\ParserCore\Exceptions\ValidationException;

describe('Cross-hierarchy exception behavior', function (): void {
    it('catches all child exceptions via ParserException catch block', function (): void {
        $exceptions = [
            new FormatDetectionException('format'),
            new ParseException('parse'),
            new ValidationException('validation'),
        ];

        foreach ($exceptions as $exception) {
            $caught = false;
            try {
                throw $exception;
            } catch (ParserException $e) {
                $caught = true;
            }
            expect($caught)->toBeTrue();
        }
    });

    it('catches all four exception types via RuntimeException catch block', function (): void {
        $exceptions = [
            new ParserException('base'),
            new FormatDetectionException('format'),
            new ParseException('parse'),
            new ValidationException('validation'),
        ];

        foreach ($exceptions as $exception) {
            $caught = false;
            try {
                throw $exception;
            } catch (\RuntimeException $e) {
                $caught = true;
            }
            expect($caught)->toBeTrue();
        }
    });

    it('ensures sibling exceptions are not instances of each other', function (): void {
        $formatException = new FormatDetectionException();
        $parseException = new ParseException();
        $validationException = new ValidationException();

        expect($formatException)->not->toBeInstanceOf(ParseException::class)
            ->and($formatException)->not->toBeInstanceOf(ValidationException::class)
            ->and($parseException)->not->toBeInstanceOf(FormatDetectionException::class)
            ->and($parseException)->not->toBeInstanceOf(ValidationException::class)
            ->and($validationException)->not->toBeInstanceOf(FormatDetectionException::class)
            ->and($validationException)->not->toBeInstanceOf(ParseException::class);
    });

    it('supports previous exception chaining across exception types', function (): void {
        $root = new \InvalidArgumentException('Invalid argument');
        $middle = new \RuntimeException('Runtime failure', 0, $root);
        $top = new ParseException('Parse failed', 0, $middle);

        expect($top->getPrevious())->toBe($middle)
            ->and($top->getPrevious()?->getPrevious())->toBe($root)
            ->and($top->getPrevious()?->getPrevious())->toBeInstanceOf(\InvalidArgumentException::class);
    });
});
