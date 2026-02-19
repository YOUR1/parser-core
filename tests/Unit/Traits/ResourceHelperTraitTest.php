<?php

declare(strict_types=1);

use Youri\vandenBogert\Software\ParserCore\Traits\ResourceHelperTrait;
use EasyRdf\Graph;
use EasyRdf\Resource;

class NewResourceHelperTestHelper
{
    use ResourceHelperTrait;

    public function callGetResourceLabel(Resource $resource, ?string $preferredLang = null): ?string { return $this->getResourceLabel($resource, $preferredLang); }
    public function callGetAllResourceLabels(Resource $resource): array { return $this->getAllResourceLabels($resource); }
    public function callGetResourceComment(Resource $resource, ?string $preferredLang = null): ?string { return $this->getResourceComment($resource, $preferredLang); }
    public function callGetAllResourceComments(Resource $resource): array { return $this->getAllResourceComments($resource); }
    public function callExtractCustomAnnotations(Resource $resource): array { return $this->extractCustomAnnotations($resource); }
    public function callShortenUri(string $uri): string { return $this->shortenUri($uri); }
    public function callGetResourceValue(Resource $resource, string $property): mixed { return $this->getResourceValue($resource, $property); }
    public function callGetResourceValues(Resource $resource, string $property): array { return $this->getResourceValues($resource, $property); }
    public function callGetNamedResourceValues(Resource $resource, string $property): array { return $this->getNamedResourceValues($resource, $property); }
    public function callIsBlankNode(Resource $resource): bool { return $this->isBlankNode($resource); }
    public function callIsAnonymousOwlExpression(Resource $resource): bool { return $this->isAnonymousOwlExpression($resource); }
    public function callExtractComplexClassExpression(Resource $resource): ?string { return $this->extractComplexClassExpression($resource); }
    public function callExtractUnionMembers(Resource $resource): array { return $this->extractUnionMembers($resource); }
    public function callGetLocalName(string $uri): string { return $this->getLocalName($uri); }
    public function callGetNamespace(string $uri): string { return $this->getNamespace($uri); }
}

beforeEach(function () {
    $this->helper = new NewResourceHelperTestHelper();
});

describe('ResourceHelperTrait::getResourceLabel', function () {
    it('returns preferred language label when available', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        $resource->addLiteral('rdfs:label', 'Person', 'en');
        $resource->addLiteral('rdfs:label', 'Persoon', 'nl');
        expect($this->helper->callGetResourceLabel($resource, 'nl'))->toBe('Persoon');
    });

    it('falls back to English when preferred language not found', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        $resource->addLiteral('rdfs:label', 'Person', 'en');
        expect($this->helper->callGetResourceLabel($resource, 'de'))->toBe('Person');
    });

    it('falls back to first available when no English label', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        $resource->addLiteral('rdfs:label', 'Persoon', 'nl');
        expect($this->helper->callGetResourceLabel($resource, 'de'))->toBe('Persoon');
    });

    it('returns null when no labels exist', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        expect($this->helper->callGetResourceLabel($resource))->toBeNull();
    });

    it('returns English label when no preferred language specified', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        $resource->addLiteral('rdfs:label', 'Person', 'en');
        $resource->addLiteral('rdfs:label', 'Persoon', 'nl');
        expect($this->helper->callGetResourceLabel($resource))->toBe('Person');
    });
});

describe('ResourceHelperTrait::getAllResourceLabels', function () {
    it('returns array keyed by language tag', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        $resource->addLiteral('rdfs:label', 'Person', 'en');
        $resource->addLiteral('rdfs:label', 'Persoon', 'nl');
        $labels = $this->helper->callGetAllResourceLabels($resource);
        expect($labels)->toHaveKey('en')->and($labels['en'])->toBe('Person')->and($labels)->toHaveKey('nl')->and($labels['nl'])->toBe('Persoon');
    });

    it('returns empty array when no labels exist', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        expect($this->helper->callGetAllResourceLabels($resource))->toBe([]);
    });

    it('uses none key for labels without language tag', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        $resource->addLiteral('rdfs:label', 'Person');
        $labels = $this->helper->callGetAllResourceLabels($resource);
        expect($labels)->toHaveKey('none')->and($labels['none'])->toBe('Person');
    });

    it('only extracts rdfs:label and not skos:prefLabel', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        $resource->addLiteral('rdfs:label', 'Person', 'en');
        $resource->addLiteral('skos:prefLabel', 'Preferred Person', 'en');
        $labels = $this->helper->callGetAllResourceLabels($resource);
        expect($labels)->toHaveCount(1)->and($labels['en'])->toBe('Person');
    });
});

describe('ResourceHelperTrait::getResourceComment', function () {
    it('returns preferred language comment when available', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        $resource->addLiteral('rdfs:comment', 'A person', 'en');
        $resource->addLiteral('rdfs:comment', 'Een persoon', 'nl');
        expect($this->helper->callGetResourceComment($resource, 'nl'))->toBe('Een persoon');
    });

    it('falls back to English', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        $resource->addLiteral('rdfs:comment', 'A person', 'en');
        expect($this->helper->callGetResourceComment($resource, 'de'))->toBe('A person');
    });

    it('returns null when no comments exist', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        expect($this->helper->callGetResourceComment($resource))->toBeNull();
    });

    it('falls back to first available when no English comment', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        $resource->addLiteral('rdfs:comment', 'Een persoon', 'nl');
        expect($this->helper->callGetResourceComment($resource, 'de'))->toBe('Een persoon');
    });
});

describe('ResourceHelperTrait::getAllResourceComments', function () {
    it('returns array keyed by language tag', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        $resource->addLiteral('rdfs:comment', 'A person', 'en');
        $resource->addLiteral('rdfs:comment', 'Een persoon', 'nl');
        $comments = $this->helper->callGetAllResourceComments($resource);
        expect($comments)->toHaveKey('en')->and($comments['en'])->toBe('A person')->and($comments)->toHaveKey('nl')->and($comments['nl'])->toBe('Een persoon');
    });

    it('returns empty array when no comments', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        expect($this->helper->callGetAllResourceComments($resource))->toBe([]);
    });

    it('uses none key for comments without language tag', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        $resource->addLiteral('rdfs:comment', 'A person');
        $comments = $this->helper->callGetAllResourceComments($resource);
        expect($comments)->toHaveKey('none')->and($comments['none'])->toBe('A person');
    });
});

describe('ResourceHelperTrait::extractCustomAnnotations', function () {
    it('returns non-standard annotations', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        $resource->addLiteral('rdfs:label', 'Person', 'en');
        $resource->addLiteral('skos:prefLabel', 'Preferred Person', 'en');
        $annotations = $this->helper->callExtractCustomAnnotations($resource);
        $skosPrefLabels = array_filter($annotations, fn ($a) => str_contains($a['property'], 'prefLabel'));
        expect($skosPrefLabels)->not->toBeEmpty();
    });

    it('skips standard RDFS/OWL properties', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        $resource->addLiteral('rdfs:label', 'Person', 'en');
        $resource->addLiteral('rdfs:comment', 'A person', 'en');
        $annotations = $this->helper->callExtractCustomAnnotations($resource);
        $standardAnnotations = array_filter($annotations, fn ($a) => str_contains($a['property'], 'label') || str_contains($a['property'], 'comment'));
        expect($standardAnnotations)->toBeEmpty();
    });

    it('returns empty array when only standard properties exist', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        $resource->addLiteral('rdfs:label', 'Person', 'en');
        $annotations = $this->helper->callExtractCustomAnnotations($resource);
        expect($annotations)->toBeEmpty();
    });

    it('captures resource references as annotation values', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        $seeAlso = $graph->resource('http://example.org/PersonDoc');
        $resource->addResource('rdfs:seeAlso', $seeAlso);
        $annotations = $this->helper->callExtractCustomAnnotations($resource);
        $seeAlsoAnnotations = array_filter($annotations, fn ($a) => str_contains($a['property'], 'seeAlso'));
        expect($seeAlsoAnnotations)->not->toBeEmpty();
        $annotation = array_values($seeAlsoAnnotations)[0];
        expect($annotation)->toHaveKey('value')->and($annotation['value'])->toBe('http://example.org/PersonDoc');
    });

    it('captures language tag for literal annotations', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        $resource->addLiteral('skos:prefLabel', 'Preferred Person', 'en');
        $annotations = $this->helper->callExtractCustomAnnotations($resource);
        $prefLabels = array_filter($annotations, fn ($a) => str_contains($a['property'], 'prefLabel'));
        expect($prefLabels)->not->toBeEmpty();
        $annotation = array_values($prefLabels)[0];
        expect($annotation)->toHaveKey('language')->and($annotation['language'])->toBe('en');
    });

    it('excludes rdf:type from annotations', function () {
        $graph = new \EasyRdf\Graph();
        $resource = $graph->resource('http://example.org/MyClass', 'owl:Class');
        // rdf:type is set implicitly by the second arg above
        $result = $this->helper->callExtractCustomAnnotations($resource);
        expect($result)->toBeEmpty();
    });
});

describe('ResourceHelperTrait::shortenUri', function () {
    it('converts full URI to prefixed notation when namespace is registered', function () {
        expect($this->helper->callShortenUri('http://www.w3.org/2002/07/owl#Class'))->toBe('owl:Class');
    });

    it('converts rdfs URI to prefixed notation', function () {
        expect($this->helper->callShortenUri('http://www.w3.org/2000/01/rdf-schema#label'))->toBe('rdfs:label');
    });

    it('returns full URI when no registered namespace matches', function () {
        expect($this->helper->callShortenUri('http://completely-unknown-namespace.example.org/SomeClass'))->toBe('http://completely-unknown-namespace.example.org/SomeClass');
    });
});

describe('ResourceHelperTrait::getResourceValue', function () {
    it('returns URI string for Resource values', function () {
        $graph = new Graph();
        $person = $graph->resource('http://example.org/Person', 'owl:Class');
        $animal = $graph->resource('http://example.org/Animal', 'owl:Class');
        $person->addResource('rdfs:subClassOf', $animal);
        expect($this->helper->callGetResourceValue($person, 'rdfs:subClassOf'))->toBe('http://example.org/Animal');
    });

    it('returns string for Literal values', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        $resource->addLiteral('rdfs:label', 'Person', 'en');
        expect($this->helper->callGetResourceValue($resource, 'rdfs:label'))->toBe('Person');
    });

    it('returns null when property is absent', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        expect($this->helper->callGetResourceValue($resource, 'rdfs:label'))->toBeNull();
    });
});

describe('ResourceHelperTrait::getResourceValues', function () {
    it('returns array of URIs for multiple Resource values', function () {
        $graph = new Graph();
        $person = $graph->resource('http://example.org/Person', 'owl:Class');
        $thing = $graph->resource('http://example.org/Thing', 'owl:Class');
        $agent = $graph->resource('http://example.org/Agent', 'owl:Class');
        $person->addResource('rdfs:subClassOf', $thing);
        $person->addResource('rdfs:subClassOf', $agent);
        $values = $this->helper->callGetResourceValues($person, 'rdfs:subClassOf');
        expect($values)->toBeArray()->and($values)->toContain('http://example.org/Thing')->and($values)->toContain('http://example.org/Agent');
    });

    it('returns empty array when property has no values', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        expect($this->helper->callGetResourceValues($resource, 'rdfs:subClassOf'))->toBe([]);
    });
});

describe('ResourceHelperTrait::getNamedResourceValues', function () {
    it('returns only named resource URIs', function () {
        $graph = new Graph();
        $person = $graph->resource('http://example.org/Person', 'owl:Class');
        $thing = $graph->resource('http://example.org/Thing', 'owl:Class');
        $person->addResource('rdfs:subClassOf', $thing);
        $values = $this->helper->callGetNamedResourceValues($person, 'rdfs:subClassOf');
        expect($values)->toBe(['http://example.org/Thing']);
    });

    it('excludes blank nodes from results', function () {
        $graph = new Graph();
        $person = $graph->resource('http://example.org/Person', 'owl:Class');
        $thing = $graph->resource('http://example.org/Thing', 'owl:Class');
        $bnode = $graph->newBNode('owl:Restriction');
        $person->addResource('rdfs:subClassOf', $thing);
        $person->addResource('rdfs:subClassOf', $bnode);
        $values = $this->helper->callGetNamedResourceValues($person, 'rdfs:subClassOf');
        expect($values)->toBe(['http://example.org/Thing']);
    });

    it('returns empty array when no named resources', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        expect($this->helper->callGetNamedResourceValues($resource, 'rdfs:subClassOf'))->toBe([]);
    });

    it('excludes literal values from getNamedResourceValues', function () {
        $graph = new \EasyRdf\Graph();
        $resource = $graph->resource('http://example.org/MyClass');
        $resource->add('rdfs:seeAlso', $graph->resource('http://example.org/Other'));
        $resource->addLiteral('rdfs:seeAlso', 'some string value');
        $result = $this->helper->callGetNamedResourceValues($resource, 'rdfs:seeAlso');
        expect($result)->toBe(['http://example.org/Other']);
    });
});

describe('ResourceHelperTrait::isBlankNode', function () {
    it('returns true for blank nodes', function () {
        $graph = new Graph();
        $bnode = $graph->newBNode();
        expect($this->helper->callIsBlankNode($bnode))->toBeTrue();
    });

    it('returns false for named resources', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person');
        expect($this->helper->callIsBlankNode($resource))->toBeFalse();
    });
});

describe('ResourceHelperTrait::isAnonymousOwlExpression', function () {
    it('returns true for blank node with owl:Restriction type', function () {
        $graph = new Graph();
        $bnode = $graph->newBNode('owl:Restriction');
        expect($this->helper->callIsAnonymousOwlExpression($bnode))->toBeTrue();
    });

    it('returns true for blank node with owl:Class type', function () {
        $graph = new Graph();
        $bnode = $graph->newBNode('owl:Class');
        expect($this->helper->callIsAnonymousOwlExpression($bnode))->toBeTrue();
    });

    it('returns false for named resources', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        expect($this->helper->callIsAnonymousOwlExpression($resource))->toBeFalse();
    });

    it('returns false for blank nodes without OWL types', function () {
        $graph = new Graph();
        $bnode = $graph->newBNode();
        expect($this->helper->callIsAnonymousOwlExpression($bnode))->toBeFalse();
    });

    it('returns true for blank node with owl:unionOf property', function () {
        $graph = new Graph();
        $bnode = $graph->newBNode();
        $list = $graph->newBNode();
        $bnode->addResource('owl:unionOf', $list);
        expect($this->helper->callIsAnonymousOwlExpression($bnode))->toBeTrue();
    });

    it('returns true for blank node with owl:intersectionOf property', function () {
        $graph = new Graph();
        $bnode = $graph->newBNode();
        $list = $graph->newBNode();
        $bnode->addResource('owl:intersectionOf', $list);
        expect($this->helper->callIsAnonymousOwlExpression($bnode))->toBeTrue();
    });
});

describe('ResourceHelperTrait::extractComplexClassExpression', function () {
    it('returns URI for named classes', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        expect($this->helper->callExtractComplexClassExpression($resource))->toBe('http://example.org/Person');
    });

    it('returns description string for owl:Restriction blank nodes', function () {
        $graph = new Graph();
        $restriction = $graph->newBNode('owl:Restriction');
        $onProp = $graph->resource('http://example.org/hasName');
        $restriction->addResource('owl:onProperty', $onProp);
        $result = $this->helper->callExtractComplexClassExpression($restriction);
        expect($result)->toContain('Restriction on')->and($result)->toContain('http://example.org/hasName');
    });

    it('returns null for unresolvable blank nodes', function () {
        $graph = new Graph();
        $bnode = $graph->newBNode();
        expect($this->helper->callExtractComplexClassExpression($bnode))->toBeNull();
    });

    it('returns union description for union blank nodes with bug fix', function () {
        $graph = new Graph();
        $union = $graph->newBNode('owl:Class');
        $member1 = $graph->resource('http://example.org/Person');
        $member2 = $graph->resource('http://example.org/Animal');
        $listNode2 = $graph->newBNode();
        $listNode2->addResource('rdf:first', $member2);
        $listNode2->addResource('rdf:rest', $graph->resource('http://www.w3.org/1999/02/22-rdf-syntax-ns#nil'));
        $listNode1 = $graph->newBNode();
        $listNode1->addResource('rdf:first', $member1);
        $listNode1->addResource('rdf:rest', $listNode2);
        $union->addResource('owl:unionOf', $listNode1);
        $result = $this->helper->callExtractComplexClassExpression($union);
        expect($result)->toContain('Union of:')->and($result)->toContain('http://example.org/Person')->and($result)->toContain('http://example.org/Animal');
    });
});

describe('ResourceHelperTrait::extractUnionMembers', function () {
    it('returns empty array when no owl:unionOf', function () {
        $graph = new Graph();
        $resource = $graph->resource('http://example.org/Person', 'owl:Class');
        expect($this->helper->callExtractUnionMembers($resource))->toBe([]);
    });

    it('returns member URIs for properly constructed owl:unionOf', function () {
        $graph = new Graph();
        $union = $graph->newBNode('owl:Class');
        $member1 = $graph->resource('http://example.org/Person');
        $member2 = $graph->resource('http://example.org/Animal');
        $listNode2 = $graph->newBNode();
        $listNode2->addResource('rdf:first', $member2);
        $listNode2->addResource('rdf:rest', $graph->resource('http://www.w3.org/1999/02/22-rdf-syntax-ns#nil'));
        $listNode1 = $graph->newBNode();
        $listNode1->addResource('rdf:first', $member1);
        $listNode1->addResource('rdf:rest', $listNode2);
        $union->addResource('owl:unionOf', $listNode1);
        $members = $this->helper->callExtractUnionMembers($union);
        expect($members)->toContain('http://example.org/Person')->and($members)->toContain('http://example.org/Animal')->and($members)->toHaveCount(2);
    });
});

describe('ResourceHelperTrait::getLocalName', function () {
    it('extracts part after hash fragment', function () {
        expect($this->helper->callGetLocalName('http://example.org/ns#Person'))->toBe('Person');
    });

    it('extracts part after last slash', function () {
        expect($this->helper->callGetLocalName('http://example.org/ontology/Person'))->toBe('Person');
    });

    it('uses hash delimiter even when slash follows it', function () {
        expect($this->helper->callGetLocalName('http://example.org/ns#local/part'))->toBe('local/part');
    });

    it('returns full string for getLocalName when no delimiter found', function () {
        expect($this->helper->callGetLocalName('justAString'))->toBe('justAString');
    });
});

describe('ResourceHelperTrait::getNamespace', function () {
    it('extracts part before and including hash', function () {
        expect($this->helper->callGetNamespace('http://example.org/ns#Person'))->toBe('http://example.org/ns#');
    });

    it('extracts part before and including last slash', function () {
        expect($this->helper->callGetNamespace('http://example.org/ontology/Person'))->toBe('http://example.org/ontology/');
    });

    it('returns empty string for getNamespace when no delimiter found', function () {
        expect($this->helper->callGetNamespace('justAString'))->toBe('');
    });
});
