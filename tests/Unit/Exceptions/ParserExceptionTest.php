<?php

declare(strict_types=1);

use Youri\vandenBogert\Software\ParserCore\Exceptions\ParserException;

describe('ParserException', function (): void {
    it('is instantiable with no arguments', function (): void {
        $exception = new ParserException();
        expect($exception)->toBeInstanceOf(ParserException::class);
    });

    it('extends RuntimeException', function (): void {
        $exception = new ParserException();
        expect($exception)->toBeInstanceOf(\RuntimeException::class);
    });

    it('is an instance of RuntimeException and Throwable', function (): void {
        $exception = new ParserException();
        expect($exception)
            ->toBeInstanceOf(\RuntimeException::class)
            ->toBeInstanceOf(\Throwable::class);
    });

    it('returns the provided message', function (): void {
        $exception = new ParserException('Something went wrong');
        expect($exception->getMessage())->toBe('Something went wrong');
    });

    it('returns the provided code', function (): void {
        $exception = new ParserException('Error', 42);
        expect($exception->getCode())->toBe(42);
    });

    it('returns the previous exception', function (): void {
        $previous = new \RuntimeException('Root cause');
        $exception = new ParserException('Wrapped error', 0, $previous);
        expect($exception->getPrevious())->toBe($previous);
    });

    it('has correct default values', function (): void {
        $exception = new ParserException();
        expect($exception->getMessage())->toBe('')
            ->and($exception->getCode())->toBe(0)
            ->and($exception->getPrevious())->toBeNull();
    });

    it('accepts Exception as previous throwable', function (): void {
        $previous = new \Exception('An exception');
        $exception = new ParserException('Wrapped', 0, $previous);
        expect($exception->getPrevious())->toBe($previous);
    });

    it('accepts Error as previous throwable', function (): void {
        $previous = new \Error('A fatal error');
        $exception = new ParserException('Wrapped error', 0, $previous);
        expect($exception->getPrevious())->toBe($previous);
    });

    it('is throwable and catchable as RuntimeException', function (): void {
        $caught = false;
        try {
            throw new ParserException('test');
        } catch (\RuntimeException $e) {
            $caught = true;
            expect($e)->toBeInstanceOf(ParserException::class);
        }
        expect($caught)->toBeTrue();
    });

    it('is throwable and catchable as ParserException', function (): void {
        $caught = false;
        try {
            throw new ParserException('test');
        } catch (ParserException $e) {
            $caught = true;
            expect($e->getMessage())->toBe('test');
        }
        expect($caught)->toBeTrue();
    });
});
