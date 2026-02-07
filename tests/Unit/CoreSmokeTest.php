<?php

use App\Services\Ontology\Exceptions\OntologyImportException;
use App\Support\RdfHelper;

it('can instantiate core exception', function () {
    $ex = new OntologyImportException('x');
    expect($ex->getMessage())->toBe('x');
});

it('extracts local rdf names', function () {
    expect(RdfHelper::extractLocalName('http://example.org/ns#Person'))->toBe('Person');
});
