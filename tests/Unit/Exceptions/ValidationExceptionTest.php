<?php

declare(strict_types=1);

use Youri\vandenBogert\Software\ParserCore\Exceptions\ParserException;
use Youri\vandenBogert\Software\ParserCore\Exceptions\ValidationException;

describe('ValidationException', function (): void {
    it('is instantiable with no arguments', function (): void {
        $exception = new ValidationException();
        expect($exception)->toBeInstanceOf(ValidationException::class);
    });

    it('extends ParserException', function (): void {
        $exception = new ValidationException();
        expect($exception)->toBeInstanceOf(ParserException::class);
    });

    it('is an instance of ParserException, RuntimeException, and Throwable', function (): void {
        $exception = new ValidationException();
        expect($exception)
            ->toBeInstanceOf(ParserException::class)
            ->toBeInstanceOf(\RuntimeException::class)
            ->toBeInstanceOf(\Throwable::class);
    });

    it('returns the provided message', function (): void {
        $exception = new ValidationException('Missing required property');
        expect($exception->getMessage())->toBe('Missing required property');
    });

    it('returns the provided code', function (): void {
        $exception = new ValidationException('Validation failed', 7);
        expect($exception->getCode())->toBe(7);
    });

    it('returns the previous exception', function (): void {
        $previous = new \RuntimeException('Structural error');
        $exception = new ValidationException('Validation failed', 3, $previous);
        expect($exception->getPrevious())->toBe($previous);
    });

    it('has correct default values', function (): void {
        $exception = new ValidationException();
        expect($exception->getMessage())->toBe('')
            ->and($exception->getCode())->toBe(0)
            ->and($exception->getPrevious())->toBeNull();
    });

    it('is throwable and catchable as ParserException', function (): void {
        $caught = false;
        try {
            throw new ValidationException('test');
        } catch (ParserException $e) {
            $caught = true;
            expect($e)->toBeInstanceOf(ValidationException::class);
        }
        expect($caught)->toBeTrue();
    });

    it('is throwable and catchable as RuntimeException', function (): void {
        $caught = false;
        try {
            throw new ValidationException('test');
        } catch (\RuntimeException $e) {
            $caught = true;
            expect($e)->toBeInstanceOf(ValidationException::class);
        }
        expect($caught)->toBeTrue();
    });
});
