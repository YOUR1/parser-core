<?php

declare(strict_types=1);

use Youri\vandenBogert\Software\ParserCore\Exceptions\FormatDetectionException;
use Youri\vandenBogert\Software\ParserCore\Exceptions\ParserException;

describe('FormatDetectionException', function (): void {
    it('is instantiable with no arguments', function (): void {
        $exception = new FormatDetectionException();
        expect($exception)->toBeInstanceOf(FormatDetectionException::class);
    });

    it('extends ParserException', function (): void {
        $exception = new FormatDetectionException();
        expect($exception)->toBeInstanceOf(ParserException::class);
    });

    it('is an instance of ParserException, RuntimeException, and Throwable', function (): void {
        $exception = new FormatDetectionException();
        expect($exception)
            ->toBeInstanceOf(ParserException::class)
            ->toBeInstanceOf(\RuntimeException::class)
            ->toBeInstanceOf(\Throwable::class);
    });

    it('returns the provided message', function (): void {
        $exception = new FormatDetectionException('No handler found');
        expect($exception->getMessage())->toBe('No handler found');
    });

    it('returns the provided code', function (): void {
        $exception = new FormatDetectionException('Detection failed', 42);
        expect($exception->getCode())->toBe(42);
    });

    it('returns the previous exception', function (): void {
        $previous = new \RuntimeException('Root cause');
        $exception = new FormatDetectionException('Detection failed', 1, $previous);
        expect($exception->getPrevious())->toBe($previous);
    });

    it('has correct default values', function (): void {
        $exception = new FormatDetectionException();
        expect($exception->getMessage())->toBe('')
            ->and($exception->getCode())->toBe(0)
            ->and($exception->getPrevious())->toBeNull();
    });

    it('is throwable and catchable as ParserException', function (): void {
        $caught = false;
        try {
            throw new FormatDetectionException('test');
        } catch (ParserException $e) {
            $caught = true;
            expect($e)->toBeInstanceOf(FormatDetectionException::class);
        }
        expect($caught)->toBeTrue();
    });

    it('is throwable and catchable as RuntimeException', function (): void {
        $caught = false;
        try {
            throw new FormatDetectionException('test');
        } catch (\RuntimeException $e) {
            $caught = true;
            expect($e)->toBeInstanceOf(FormatDetectionException::class);
        }
        expect($caught)->toBeTrue();
    });
});
