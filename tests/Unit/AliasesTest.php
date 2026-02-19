<?php

declare(strict_types=1);

use Youri\vandenBogert\Software\ParserCore\Contracts\OntologyParserInterface;
use Youri\vandenBogert\Software\ParserCore\Contracts\RdfFormatHandlerInterface;
use Youri\vandenBogert\Software\ParserCore\ValueObjects\ParsedRdf;
use Youri\vandenBogert\Software\ParserCore\Exceptions\ParserException;

describe('class_alias bridge', function () {

    describe('alias resolution', function () {
        it('resolves OntologyParserInterface from old namespace', function () {
            expect(interface_exists('App\Services\Ontology\Parsers\OntologyParserInterface'))->toBeTrue();
        });

        it('resolves RdfFormatHandlerInterface from old namespace', function () {
            expect(interface_exists('App\Services\Ontology\Parsers\Contracts\RdfFormatHandlerInterface'))->toBeTrue();
        });

        it('resolves ParsedRdf from old namespace', function () {
            expect(class_exists('App\Services\Ontology\Parsers\ValueObjects\ParsedRdf'))->toBeTrue();
        });

        it('resolves OntologyImportException from old namespace', function () {
            expect(class_exists('App\Services\Ontology\Exceptions\OntologyImportException'))->toBeTrue();
        });
    });

    describe('instanceof compatibility', function () {
        it('allows instanceof check with old ParsedRdf name', function () {
            $graph = new \EasyRdf\Graph();
            $parsedRdf = new ParsedRdf(graph: $graph, format: 'turtle', rawContent: '');
            expect($parsedRdf)->toBeInstanceOf('App\Services\Ontology\Parsers\ValueObjects\ParsedRdf');
        });

        it('allows instanceof check with old OntologyImportException name', function () {
            $exception = new ParserException('test');
            expect($exception)->toBeInstanceOf('App\Services\Ontology\Exceptions\OntologyImportException');
        });

        it('allows implementing old RdfFormatHandlerInterface name', function () {
            $handler = new class implements \App\Services\Ontology\Parsers\Contracts\RdfFormatHandlerInterface {
                public function canHandle(string $content): bool { return true; }
                public function parse(string $content): ParsedRdf {
                    return new ParsedRdf(new \EasyRdf\Graph(), 'test', $content);
                }
                public function getFormatName(): string { return 'test'; }
            };
            expect($handler)->toBeInstanceOf(RdfFormatHandlerInterface::class);
        });

        it('allows implementing old OntologyParserInterface name', function () {
            $parser = new class implements \App\Services\Ontology\Parsers\OntologyParserInterface {
                public function parse(string $content, array $options = []): \Youri\vandenBogert\Software\ParserCore\ValueObjects\ParsedOntology {
                    return new \Youri\vandenBogert\Software\ParserCore\ValueObjects\ParsedOntology();
                }
                public function canParse(string $content): bool { return true; }
                public function getSupportedFormats(): array { return ['test']; }
            };
            expect($parser)->toBeInstanceOf(OntologyParserInterface::class);
        });
    });

    describe('deprecation warnings', function () {
        // Captures E_USER_DEPRECATED from aliases.php in a subprocess.
        // Deprecations are triggered on-demand when old namespace classes are actually
        // referenced (via spl_autoload_register), not at file load time.
        $captureDeprecations = function (): array {
            static $cache = null;
            if ($cache !== null) {
                return $cache;
            }

            $projectRoot = dirname(__DIR__, 2);
            $script = <<<'PHP'
<?php
$deprecations = [];
set_error_handler(function (int $errno, string $errstr) use (&$deprecations) {
    if ($errno === E_USER_DEPRECATED) {
        $deprecations[] = $errstr;
    }
    return true;
});
require $argv[1] . '/vendor/autoload.php';
// Trigger aliases by actually referencing old namespace classes
class_exists('App\Services\Ontology\Parsers\OntologyParserInterface');
class_exists('App\Services\Ontology\Parsers\Contracts\RdfFormatHandlerInterface');
class_exists('App\Services\Ontology\Parsers\ValueObjects\ParsedRdf');
class_exists('App\Services\Ontology\Exceptions\OntologyImportException');
echo json_encode($deprecations);
PHP;
            $tempFile = tempnam(sys_get_temp_dir(), 'alias_test_');
            file_put_contents($tempFile, $script);
            $output = shell_exec('php ' . escapeshellarg($tempFile) . ' ' . escapeshellarg($projectRoot));
            unlink($tempFile);

            $cache = json_decode($output, true) ?? [];

            return $cache;
        };

        it('triggers E_USER_DEPRECATED on-demand for all 4 aliases', function () use ($captureDeprecations) {
            expect($captureDeprecations())->toBeArray()->toHaveCount(4);
        });

        it('includes old and new FQCN in each deprecation message', function () use ($captureDeprecations) {
            $deprecations = $captureDeprecations();

            $mappings = [
                ['App\Services\Ontology\Parsers\OntologyParserInterface', 'Youri\vandenBogert\Software\ParserCore\Contracts\OntologyParserInterface'],
                ['App\Services\Ontology\Parsers\Contracts\RdfFormatHandlerInterface', 'Youri\vandenBogert\Software\ParserCore\Contracts\RdfFormatHandlerInterface'],
                ['App\Services\Ontology\Parsers\ValueObjects\ParsedRdf', 'Youri\vandenBogert\Software\ParserCore\ValueObjects\ParsedRdf'],
                ['App\Services\Ontology\Exceptions\OntologyImportException', 'Youri\vandenBogert\Software\ParserCore\Exceptions\ParserException'],
            ];

            foreach ($mappings as [$oldFqcn, $newFqcn]) {
                $found = array_filter($deprecations, fn (string $msg) => str_contains($msg, $oldFqcn) && str_contains($msg, $newFqcn));
                expect($found)->not->toBeEmpty("Expected deprecation containing both {$oldFqcn} and {$newFqcn}");
            }
        });

        it('mentions v2.0 removal in each deprecation message', function () use ($captureDeprecations) {
            $deprecations = $captureDeprecations();

            foreach ($deprecations as $msg) {
                expect($msg)->toContain('v2.0');
            }
        });

        it('does NOT trigger deprecation warnings at autoload time', function () {
            $projectRoot = dirname(__DIR__, 2);
            $script = <<<'PHP'
<?php
$deprecations = [];
set_error_handler(function (int $errno, string $errstr) use (&$deprecations) {
    if ($errno === E_USER_DEPRECATED) {
        $deprecations[] = $errstr;
    }
    return true;
});
require $argv[1] . '/vendor/autoload.php';
// Do NOT reference any old namespace classes
echo json_encode($deprecations);
PHP;
            $tempFile = tempnam(sys_get_temp_dir(), 'alias_test_');
            file_put_contents($tempFile, $script);
            $output = shell_exec('php ' . escapeshellarg($tempFile) . ' ' . escapeshellarg($projectRoot));
            unlink($tempFile);

            $deprecations = json_decode($output, true) ?? [];
            expect($deprecations)->toBeArray()->toHaveCount(0);
        });
    });

    describe('no aliases for internal classes', function () {
        it('does not alias GraphInterface', function () {
            expect(interface_exists('App\Services\Ontology\Parsers\Contracts\GraphInterface', false))->toBeFalse();
        });

        it('does not alias ResourceHelperTrait', function () {
            expect(class_exists('App\Services\Ontology\Parsers\Traits\ResourceHelperTrait', false))->toBeFalse();
        });

        it('does not alias RdfHelper', function () {
            expect(class_exists('App\Support\RdfHelper', false))->toBeFalse();
        });
    });
});