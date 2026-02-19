<?php

declare(strict_types=1);

use Youri\vandenBogert\Software\ParserCore\Exceptions\ParseException;
use Youri\vandenBogert\Software\ParserCore\Exceptions\ParserException;

describe('ParseException', function (): void {
    it('is instantiable with no arguments', function (): void {
        $exception = new ParseException();
        expect($exception)->toBeInstanceOf(ParseException::class);
    });

    it('extends ParserException', function (): void {
        $exception = new ParseException();
        expect($exception)->toBeInstanceOf(ParserException::class);
    });

    it('is an instance of ParserException, RuntimeException, and Throwable', function (): void {
        $exception = new ParseException();
        expect($exception)
            ->toBeInstanceOf(ParserException::class)
            ->toBeInstanceOf(\RuntimeException::class)
            ->toBeInstanceOf(\Throwable::class);
    });

    it('returns the provided message', function (): void {
        $exception = new ParseException('Failed to parse RDF/XML');
        expect($exception->getMessage())->toBe('Failed to parse RDF/XML');
    });

    it('returns the provided code', function (): void {
        $exception = new ParseException('Parse failed', 99);
        expect($exception->getCode())->toBe(99);
    });

    it('returns the previous exception', function (): void {
        $previous = new \RuntimeException('EasyRdf error');
        $exception = new ParseException('Parse failed', 2, $previous);
        expect($exception->getPrevious())->toBe($previous);
    });

    it('has correct default values', function (): void {
        $exception = new ParseException();
        expect($exception->getMessage())->toBe('')
            ->and($exception->getCode())->toBe(0)
            ->and($exception->getPrevious())->toBeNull();
    });

    it('is throwable and catchable as ParserException', function (): void {
        $caught = false;
        try {
            throw new ParseException('test');
        } catch (ParserException $e) {
            $caught = true;
            expect($e)->toBeInstanceOf(ParseException::class);
        }
        expect($caught)->toBeTrue();
    });

    it('is throwable and catchable as RuntimeException', function (): void {
        $caught = false;
        try {
            throw new ParseException('test');
        } catch (\RuntimeException $e) {
            $caught = true;
            expect($e)->toBeInstanceOf(ParseException::class);
        }
        expect($caught)->toBeTrue();
    });
});
