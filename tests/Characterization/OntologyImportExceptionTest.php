<?php

declare(strict_types=1);

use App\Services\Ontology\Exceptions\OntologyImportException;

describe('OntologyImportException', function () {

    it('extends base Exception class', function () {
        $exception = new OntologyImportException('test');

        expect($exception)->toBeInstanceOf(\Exception::class);
    });

    it('constructs with a message string', function () {
        $exception = new OntologyImportException('Something went wrong');

        expect($exception->getMessage())->toBe('Something went wrong');
    });

    it('constructs with message and code', function () {
        $exception = new OntologyImportException('Error occurred', 42);

        expect($exception->getMessage())->toBe('Error occurred')
            ->and($exception->getCode())->toBe(42);
    });

    it('constructs with message, code, and previous exception', function () {
        $previous = new \RuntimeException('Root cause');
        $exception = new OntologyImportException('Wrapper', 0, $previous);

        expect($exception->getMessage())->toBe('Wrapper')
            ->and($exception->getCode())->toBe(0)
            ->and($exception->getPrevious())->toBe($previous)
            ->and($exception->getPrevious()->getMessage())->toBe('Root cause');
    });

    it('returns null for getPrevious when no previous exception', function () {
        $exception = new OntologyImportException('No cause');

        expect($exception->getPrevious())->toBeNull();
    });

    it('defaults code to 0', function () {
        $exception = new OntologyImportException('Just a message');

        expect($exception->getCode())->toBe(0);
    });
});
