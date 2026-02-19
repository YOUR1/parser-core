<?php

declare(strict_types=1);

// Class alias bridge for backward compatibility with App\ namespace.
// These aliases map old App\ namespace classes to the new
// Youri\vandenBogert\Software\ParserCore\ namespace.
// All aliases will be removed in v2.0.
//
// Deprecation warnings are triggered ONLY when old namespace classes are actually
// used (via autoload), not on every request.

spl_autoload_register(function (string $class): void {
    /** @var array<string, class-string> $aliases */
    $aliases = [
        'App\Services\Ontology\Parsers\OntologyParserInterface' => \Youri\vandenBogert\Software\ParserCore\Contracts\OntologyParserInterface::class,
        'App\Services\Ontology\Parsers\Contracts\RdfFormatHandlerInterface' => \Youri\vandenBogert\Software\ParserCore\Contracts\RdfFormatHandlerInterface::class,
        'App\Services\Ontology\Parsers\ValueObjects\ParsedRdf' => \Youri\vandenBogert\Software\ParserCore\ValueObjects\ParsedRdf::class,
        'App\Services\Ontology\Exceptions\OntologyImportException' => \Youri\vandenBogert\Software\ParserCore\Exceptions\ParserException::class,
    ];

    if (isset($aliases[$class])) {
        @trigger_error(
            sprintf(
                'Using "%s" is deprecated, use "%s" instead. This alias will be removed in v2.0.',
                $class,
                $aliases[$class],
            ),
            E_USER_DEPRECATED,
        );
        class_alias($aliases[$class], $class);
    }
});
