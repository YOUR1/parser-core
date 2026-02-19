<?php

declare(strict_types=1);

use EasyRdf\Resource;
use Youri\vandenBogert\Software\ParserCore\Contracts\GraphInterface;

describe('GraphInterface', function (): void {
    it('defines the resource() method with correct signature', function (): void {
        $reflection = new ReflectionMethod(GraphInterface::class, 'resource');

        expect($reflection->isPublic())->toBeTrue();

        $parameters = $reflection->getParameters();
        expect($parameters)->toHaveCount(2);

        // First parameter: string $uri
        expect($parameters[0]->getName())->toBe('uri');
        expect($parameters[0]->getType()?->getName())->toBe('string');
        expect($parameters[0]->getType()?->allowsNull())->toBeFalse();

        // Second parameter: ?string $type = null
        expect($parameters[1]->getName())->toBe('type');
        expect($parameters[1]->getType()?->getName())->toBe('string');
        expect($parameters[1]->getType()?->allowsNull())->toBeTrue();
        expect($parameters[1]->isDefaultValueAvailable())->toBeTrue();
        expect($parameters[1]->getDefaultValue())->toBeNull();

        // Return type: Resource
        $returnType = $reflection->getReturnType();
        expect($returnType)->not->toBeNull();
        expect($returnType?->getName())->toBe(Resource::class);
    });

    it('defines the resources() method with correct signature', function (): void {
        $reflection = new ReflectionMethod(GraphInterface::class, 'resources');

        expect($reflection->isPublic())->toBeTrue();
        expect($reflection->getParameters())->toHaveCount(0);

        $returnType = $reflection->getReturnType();
        expect($returnType)->not->toBeNull();
        expect($returnType?->getName())->toBe('array');
    });

    it('defines the allOfType() method with correct signature', function (): void {
        $reflection = new ReflectionMethod(GraphInterface::class, 'allOfType');

        expect($reflection->isPublic())->toBeTrue();

        $parameters = $reflection->getParameters();
        expect($parameters)->toHaveCount(1);

        expect($parameters[0]->getName())->toBe('type');
        expect($parameters[0]->getType()?->getName())->toBe('string');
        expect($parameters[0]->getType()?->allowsNull())->toBeFalse();

        $returnType = $reflection->getReturnType();
        expect($returnType)->not->toBeNull();
        expect($returnType?->getName())->toBe('array');
    });

    it('defines the getNamespaceMap() method with correct signature', function (): void {
        $reflection = new ReflectionMethod(GraphInterface::class, 'getNamespaceMap');

        expect($reflection->isPublic())->toBeTrue();
        expect($reflection->getParameters())->toHaveCount(0);

        $returnType = $reflection->getReturnType();
        expect($returnType)->not->toBeNull();
        expect($returnType?->getName())->toBe('array');
    });

    it('defines the parse() method with correct signature', function (): void {
        $reflection = new ReflectionMethod(GraphInterface::class, 'parse');

        expect($reflection->isPublic())->toBeTrue();

        $parameters = $reflection->getParameters();
        expect($parameters)->toHaveCount(3);

        // First parameter: string $data
        expect($parameters[0]->getName())->toBe('data');
        expect($parameters[0]->getType()?->getName())->toBe('string');
        expect($parameters[0]->getType()?->allowsNull())->toBeFalse();

        // Second parameter: string $format
        expect($parameters[1]->getName())->toBe('format');
        expect($parameters[1]->getType()?->getName())->toBe('string');
        expect($parameters[1]->getType()?->allowsNull())->toBeFalse();

        // Third parameter: ?string $uri = null
        expect($parameters[2]->getName())->toBe('uri');
        expect($parameters[2]->getType()?->getName())->toBe('string');
        expect($parameters[2]->getType()?->allowsNull())->toBeTrue();
        expect($parameters[2]->isDefaultValueAvailable())->toBeTrue();
        expect($parameters[2]->getDefaultValue())->toBeNull();

        // Return type: int
        $returnType = $reflection->getReturnType();
        expect($returnType)->not->toBeNull();
        expect($returnType?->getName())->toBe('int');
    });

    it('defines exactly 5 methods', function (): void {
        $reflection = new ReflectionClass(GraphInterface::class);
        $methods = $reflection->getMethods();

        expect($methods)->toHaveCount(5);

        $methodNames = array_map(fn (ReflectionMethod $m) => $m->getName(), $methods);
        expect($methodNames)->toContain('resource');
        expect($methodNames)->toContain('resources');
        expect($methodNames)->toContain('allOfType');
        expect($methodNames)->toContain('getNamespaceMap');
        expect($methodNames)->toContain('parse');
    });

    it('is an interface', function (): void {
        $reflection = new ReflectionClass(GraphInterface::class);

        expect($reflection->isInterface())->toBeTrue();
    });

    it('can be implemented by an anonymous class', function (): void {
        $implementation = new class implements GraphInterface {
            public function resource(string $uri, ?string $type = null): Resource
            {
                return new Resource($uri);
            }

            public function resources(): array
            {
                return [];
            }

            public function allOfType(string $type): array
            {
                return [];
            }

            public function getNamespaceMap(): array
            {
                return [];
            }

            public function parse(string $data, string $format, ?string $uri = null): int
            {
                return 0;
            }
        };

        expect($implementation)->toBeInstanceOf(GraphInterface::class);
    });
});
