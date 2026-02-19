# Spec Completeness

> Internal contract and implementation coverage for parser-core, the shared foundation library
> for the parser ecosystem (parser-owl, parser-shacl, etc.).
> Last updated: 2026-02-19

## Scope

This library provides no W3C-spec parsing itself. Instead it defines the **contracts, exceptions,
traits, value objects, and support classes** consumed by downstream parser packages. This document
assesses the completeness of those internal contracts and their test coverage.

Package: `youri/parser-core`
PHP requirement: `^8.2`
Runtime dependency: `easyrdf/easyrdf ^1.1`

## Summary

| Area | Defined | Tested | Coverage |
|---|---|---|---|
| Contracts / Interfaces | 3 | 3 | 100% |
| Exception Classes | 4 | 4 | 100% |
| Traits | 1 (15 methods) | 1 (15 methods) | 100% |
| Value Objects | 2 | 2 | 100% |
| Support / Helper Classes | 2 | 2 | 100% |
| Backward-Compat Aliases | 4 aliases | 4 aliases | 100% |
| **Total test cases** | | **278 passing** | **(597 assertions)** |

---

## Contracts / Interfaces

### GraphInterface

Thin wrapper over RDF graph operations, enabling future replacement of the EasyRdf engine.

| Feature | Status | Location | Tests |
|---|---|---|---|
| `resource(string $uri, ?string $type): Resource` | implemented | `Contracts/GraphInterface.php:34` | `Unit/Contracts/GraphInterfaceTest` (signature + anonymous impl) |
| `resources(): array` | implemented | `Contracts/GraphInterface.php:44` | `Unit/Contracts/GraphInterfaceTest` (signature test) |
| `allOfType(string $type): array` | implemented | `Contracts/GraphInterface.php:55` | `Unit/Contracts/GraphInterfaceTest` (signature test) |
| `getNamespaceMap(): array` | implemented | `Contracts/GraphInterface.php:69` | `Unit/Contracts/GraphInterfaceTest` (signature test) |
| `parse(string $data, string $format, ?string $uri): int` | implemented | `Contracts/GraphInterface.php:81` | `Unit/Contracts/GraphInterfaceTest` (signature test) |
| Defines exactly 5 methods | verified | `Contracts/GraphInterface.php` | `Unit/Contracts/GraphInterfaceTest` (method count) |
| Is an interface | verified | `Contracts/GraphInterface.php` | `Unit/Contracts/GraphInterfaceTest` (reflection) |
| Implementable by anonymous class | verified | -- | `Unit/Contracts/GraphInterfaceTest` (anonymous impl) |

### OntologyParserInterface

Contract for ontology parsers that orchestrate format detection, handler delegation, and data extraction.

| Feature | Status | Location | Tests |
|---|---|---|---|
| `parse(string $content, array $options = []): ParsedOntology` | implemented | `Contracts/OntologyParserInterface.php:27` | `Unit/Contracts/OntologyParserInterfaceTest` + `Characterization/OntologyParserInterfaceTest` |
| `canParse(string $content): bool` | implemented | `Contracts/OntologyParserInterface.php:35` | `Unit/Contracts/OntologyParserInterfaceTest` (true, false, empty string) |
| `getSupportedFormats(): array` | implemented | `Contracts/OntologyParserInterface.php:42` | `Unit/Contracts/OntologyParserInterfaceTest` (returns string array) |
| `$options` parameter is optional with default `[]` | verified | `Contracts/OntologyParserInterface.php:27` | `Unit/Contracts/OntologyParserInterfaceTest` (default value check) |
| `parse()` throws `ParserException` on failure | documented | `Contracts/OntologyParserInterface.php:25` | `Unit/Contracts/OntologyParserInterfaceTest` (throwing impl) |
| Defines exactly 3 methods | verified | `Contracts/OntologyParserInterface.php` | `Unit/Contracts/OntologyParserInterfaceTest` (method count) |
| Implementable by anonymous class | verified | -- | `Unit/Contracts/OntologyParserInterfaceTest` + `Characterization/OntologyParserInterfaceTest` |

### RdfFormatHandlerInterface

Contract for format-specific handlers responsible for detecting and parsing a single RDF serialization.

| Feature | Status | Location | Tests |
|---|---|---|---|
| `canHandle(string $content): bool` | implemented | `Contracts/RdfFormatHandlerInterface.php:26` | `Unit/Contracts/RdfFormatHandlerInterfaceTest` + `Characterization/RdfFormatHandlerInterfaceTest` |
| `parse(string $content): ParsedRdf` | implemented | `Contracts/RdfFormatHandlerInterface.php:33` | `Unit/Contracts/RdfFormatHandlerInterfaceTest` (returns ParsedRdf) |
| `getFormatName(): string` | implemented | `Contracts/RdfFormatHandlerInterface.php:40` | `Unit/Contracts/RdfFormatHandlerInterfaceTest` (returns string) |
| `parse()` throws `ParseException` on failure | documented | `Contracts/RdfFormatHandlerInterface.php:31` | `Unit/Contracts/RdfFormatHandlerInterfaceTest` (throwing impl) |
| `canHandle()` must not throw | documented | `Contracts/RdfFormatHandlerInterface.php:24` | `Unit/Contracts/RdfFormatHandlerInterfaceTest` (empty string) |
| Defines exactly 3 methods | verified | `Contracts/RdfFormatHandlerInterface.php` | `Unit/Contracts/RdfFormatHandlerInterfaceTest` (method count) |
| Implementable by anonymous class | verified | -- | `Unit/Contracts/RdfFormatHandlerInterfaceTest` + `Characterization/RdfFormatHandlerInterfaceTest` |

---

## Exception Classes

All exceptions follow a single-root hierarchy: `ParserException` extends `\RuntimeException`,
and three domain-specific exceptions extend `ParserException`.

```
\RuntimeException
  └── ParserException
        ├── FormatDetectionException
        ├── ParseException
        └── ValidationException
```

### ParserException (base)

| Feature | Status | Location | Tests |
|---|---|---|---|
| Extends `\RuntimeException` | implemented | `Exceptions/ParserException.php:7` | `Unit/Exceptions/ParserExceptionTest` (instanceof) |
| Constructor: `(string $message, int $code, ?\Throwable $previous)` | implemented | `Exceptions/ParserException.php:9-14` | `Unit/Exceptions/ParserExceptionTest` (message, code, previous) |
| Default values: `''`, `0`, `null` | implemented | `Exceptions/ParserException.php:10-12` | `Unit/Exceptions/ParserExceptionTest` (defaults test) |
| Accepts `Exception` as previous | verified | -- | `Unit/Exceptions/ParserExceptionTest` (Exception previous) |
| Accepts `Error` as previous | verified | -- | `Unit/Exceptions/ParserExceptionTest` (Error previous) |
| Catchable as `\RuntimeException` | verified | -- | `Unit/Exceptions/ParserExceptionTest` (catch block) |

### FormatDetectionException

| Feature | Status | Location | Tests |
|---|---|---|---|
| Extends `ParserException` | implemented | `Exceptions/FormatDetectionException.php:7` | `Unit/Exceptions/FormatDetectionExceptionTest` (instanceof chain) |
| Constructor with defaults | implemented | `Exceptions/FormatDetectionException.php:9-14` | `Unit/Exceptions/FormatDetectionExceptionTest` (all params + defaults) |
| Catchable as `ParserException` | verified | -- | `Unit/Exceptions/FormatDetectionExceptionTest` + `Unit/Exceptions/ExceptionHierarchyTest` |
| Catchable as `\RuntimeException` | verified | -- | `Unit/Exceptions/FormatDetectionExceptionTest` (catch block) |

### ParseException

| Feature | Status | Location | Tests |
|---|---|---|---|
| Extends `ParserException` | implemented | `Exceptions/ParseException.php:7` | `Unit/Exceptions/ParseExceptionTest` (instanceof chain) |
| Constructor with defaults | implemented | `Exceptions/ParseException.php:9-14` | `Unit/Exceptions/ParseExceptionTest` (all params + defaults) |
| Catchable as `ParserException` | verified | -- | `Unit/Exceptions/ParseExceptionTest` + `Unit/Exceptions/ExceptionHierarchyTest` |
| Catchable as `\RuntimeException` | verified | -- | `Unit/Exceptions/ParseExceptionTest` (catch block) |

### ValidationException

| Feature | Status | Location | Tests |
|---|---|---|---|
| Extends `ParserException` | implemented | `Exceptions/ValidationException.php:7` | `Unit/Exceptions/ValidationExceptionTest` (instanceof chain) |
| Constructor with defaults | implemented | `Exceptions/ValidationException.php:9-14` | `Unit/Exceptions/ValidationExceptionTest` (all params + defaults) |
| Catchable as `ParserException` | verified | -- | `Unit/Exceptions/ValidationExceptionTest` + `Unit/Exceptions/ExceptionHierarchyTest` |
| Catchable as `\RuntimeException` | verified | -- | `Unit/Exceptions/ValidationExceptionTest` (catch block) |

### Cross-Hierarchy Behavior

| Feature | Status | Location | Tests |
|---|---|---|---|
| All 3 children caught via `ParserException` catch | verified | -- | `Unit/Exceptions/ExceptionHierarchyTest` (loop) |
| All 4 exceptions caught via `\RuntimeException` catch | verified | -- | `Unit/Exceptions/ExceptionHierarchyTest` (loop) |
| Siblings are NOT instances of each other | verified | -- | `Unit/Exceptions/ExceptionHierarchyTest` (cross-check) |
| Previous exception chaining across types | verified | -- | `Unit/Exceptions/ExceptionHierarchyTest` (chain test) |

---

## Traits

### ResourceHelperTrait

Shared helper methods for working with EasyRdf `Resource` objects. Used by downstream extractors
and parsers. Contains 15 protected methods.

#### Label Extraction

| Feature | Status | Location | Tests |
|---|---|---|---|
| `getResourceLabel()` -- preferred language | implemented | `Traits/ResourceHelperTrait.php:21-41` | `Unit/Traits/ResourceHelperTraitTest` + `Characterization/ResourceHelperTraitTest` |
| `getResourceLabel()` -- English fallback | implemented | `Traits/ResourceHelperTrait.php:34-36` | `Unit/Traits/ResourceHelperTraitTest` (fallback to en) |
| `getResourceLabel()` -- first-available fallback | implemented | `Traits/ResourceHelperTrait.php:39-40` | `Unit/Traits/ResourceHelperTraitTest` (fallback to first) |
| `getResourceLabel()` -- returns null when empty | implemented | `Traits/ResourceHelperTrait.php:25-27` | `Unit/Traits/ResourceHelperTraitTest` (null test) |
| `getAllResourceLabels()` -- keyed by language tag | implemented | `Traits/ResourceHelperTrait.php:50-61` | `Unit/Traits/ResourceHelperTraitTest` (keyed array) |
| `getAllResourceLabels()` -- uses `'none'` key for untagged | implemented | `Traits/ResourceHelperTrait.php:56` | `Unit/Traits/ResourceHelperTraitTest` (none key) |
| `getAllResourceLabels()` -- only `rdfs:label`, not `skos:prefLabel` | implemented | `Traits/ResourceHelperTrait.php:55` | `Unit/Traits/ResourceHelperTraitTest` (skos exclusion) |

#### Comment Extraction

| Feature | Status | Location | Tests |
|---|---|---|---|
| `getResourceComment()` -- preferred language | implemented | `Traits/ResourceHelperTrait.php:66-86` | `Unit/Traits/ResourceHelperTraitTest` + `Characterization/ResourceHelperTraitTest` |
| `getResourceComment()` -- English fallback | implemented | `Traits/ResourceHelperTrait.php:79-81` | `Unit/Traits/ResourceHelperTraitTest` (en fallback) |
| `getResourceComment()` -- first-available fallback | implemented | `Traits/ResourceHelperTrait.php:84-85` | `Unit/Traits/ResourceHelperTraitTest` (first available) |
| `getResourceComment()` -- returns null when empty | implemented | `Traits/ResourceHelperTrait.php:70-72` | `Unit/Traits/ResourceHelperTraitTest` (null test) |
| `getAllResourceComments()` -- keyed by language tag | implemented | `Traits/ResourceHelperTrait.php:95-106` | `Unit/Traits/ResourceHelperTraitTest` (keyed array) |
| `getAllResourceComments()` -- uses `'none'` key for untagged | implemented | `Traits/ResourceHelperTrait.php:101` | `Unit/Traits/ResourceHelperTraitTest` (none key) |

#### Custom Annotations

| Feature | Status | Location | Tests |
|---|---|---|---|
| `extractCustomAnnotations()` -- returns non-standard properties | implemented | `Traits/ResourceHelperTrait.php:115-177` | `Unit/Traits/ResourceHelperTraitTest` + `Characterization/ResourceHelperTraitTest` |
| Excludes 12 standard RDFS/OWL properties | implemented | `Traits/ResourceHelperTrait.php:119-132` | `Unit/Traits/ResourceHelperTraitTest` (standard exclusion) |
| Excludes `rdf:type` from annotations | implemented | `Traits/ResourceHelperTrait.php:120` | `Unit/Traits/ResourceHelperTraitTest` (rdf:type exclusion) |
| Captures `Literal` values with language tag | implemented | `Traits/ResourceHelperTrait.php:160-163` | `Unit/Traits/ResourceHelperTraitTest` (language capture) |
| Captures `Resource` references as URIs | implemented | `Traits/ResourceHelperTrait.php:165-167` | `Unit/Traits/ResourceHelperTraitTest` (resource reference) |
| Uses prefixed notation via `shortenUri()` | implemented | `Traits/ResourceHelperTrait.php:146-147` | `Unit/Traits/ResourceHelperTraitTest` (prefixed output) |

#### URI Utilities

| Feature | Status | Location | Tests |
|---|---|---|---|
| `shortenUri()` -- converts known namespaces to prefixed form | implemented | `Traits/ResourceHelperTrait.php:182-197` | `Unit/Traits/ResourceHelperTraitTest` + `Characterization/ResourceHelperTraitTest` |
| `shortenUri()` -- returns full URI when no match | implemented | `Traits/ResourceHelperTrait.php:196` | `Unit/Traits/ResourceHelperTraitTest` (unknown namespace) |
| `getLocalName()` -- hash fragment extraction | implemented | `Traits/ResourceHelperTrait.php:356-367` | `Unit/Traits/ResourceHelperTraitTest` + `Characterization/ResourceHelperTraitTest` |
| `getLocalName()` -- slash path extraction | implemented | `Traits/ResourceHelperTrait.php:360-366` | `Unit/Traits/ResourceHelperTraitTest` (slash URI) |
| `getLocalName()` -- returns full string when no delimiter | implemented | `Traits/ResourceHelperTrait.php:364` | `Unit/Traits/ResourceHelperTraitTest` (no delimiter) |
| `getNamespace()` -- hash namespace extraction | implemented | `Traits/ResourceHelperTrait.php:372-384` | `Unit/Traits/ResourceHelperTraitTest` + `Characterization/ResourceHelperTraitTest` |
| `getNamespace()` -- slash namespace extraction | implemented | `Traits/ResourceHelperTrait.php:379-383` | `Unit/Traits/ResourceHelperTraitTest` (slash URI) |
| `getNamespace()` -- returns empty string when no delimiter | implemented | `Traits/ResourceHelperTrait.php:380` | `Unit/Traits/ResourceHelperTraitTest` (no delimiter) |

#### Resource Value Access

| Feature | Status | Location | Tests |
|---|---|---|---|
| `getResourceValue()` -- returns URI for Resource values | implemented | `Traits/ResourceHelperTrait.php:204-217` | `Unit/Traits/ResourceHelperTraitTest` + `Characterization/ResourceHelperTraitTest` |
| `getResourceValue()` -- returns string for Literal values | implemented | `Traits/ResourceHelperTrait.php:216` | `Unit/Traits/ResourceHelperTraitTest` (literal) |
| `getResourceValue()` -- returns null when absent | implemented | `Traits/ResourceHelperTrait.php:208-209` | `Unit/Traits/ResourceHelperTraitTest` (null) |
| `getResourceValues()` -- returns array of URIs/strings | implemented | `Traits/ResourceHelperTrait.php:224-237` | `Unit/Traits/ResourceHelperTraitTest` + `Characterization/ResourceHelperTraitTest` |
| `getResourceValues()` -- returns empty array when absent | implemented | `Traits/ResourceHelperTrait.php:226` | `Unit/Traits/ResourceHelperTraitTest` (empty) |
| `getNamedResourceValues()` -- excludes blank nodes | implemented | `Traits/ResourceHelperTrait.php:244-255` | `Unit/Traits/ResourceHelperTraitTest` + `Characterization/ResourceHelperTraitTest` |
| `getNamedResourceValues()` -- excludes literal values | implemented | `Traits/ResourceHelperTrait.php:249` | `Unit/Traits/ResourceHelperTraitTest` (literal exclusion) |

#### Blank Node & OWL Expression Handling

| Feature | Status | Location | Tests |
|---|---|---|---|
| `isBlankNode()` -- delegates to `Resource::isBNode()` | implemented | `Traits/ResourceHelperTrait.php:260-263` | `Unit/Traits/ResourceHelperTraitTest` + `Characterization/ResourceHelperTraitTest` |
| `isAnonymousOwlExpression()` -- checks OWL types on blank nodes | implemented | `Traits/ResourceHelperTrait.php:268-295` | `Unit/Traits/ResourceHelperTraitTest` + `Characterization/ResourceHelperTraitTest` |
| Detects `owl:Restriction`, `owl:Class`, `owl:unionOf`, `owl:intersectionOf`, `owl:complementOf` | implemented | `Traits/ResourceHelperTrait.php:276-282` | `Unit/Traits/ResourceHelperTraitTest` (5 OWL type checks) |
| Detects `owl:unionOf` / `owl:intersectionOf` properties | implemented | `Traits/ResourceHelperTrait.php:290-291` | `Unit/Traits/ResourceHelperTraitTest` (property-based detection) |
| `extractComplexClassExpression()` -- named class URI | implemented | `Traits/ResourceHelperTrait.php:300-305` | `Unit/Traits/ResourceHelperTraitTest` + `Characterization/ResourceHelperTraitTest` |
| `extractComplexClassExpression()` -- restriction description | implemented | `Traits/ResourceHelperTrait.php:308-312` | `Unit/Traits/ResourceHelperTraitTest` (restriction) |
| `extractComplexClassExpression()` -- union description | implemented | `Traits/ResourceHelperTrait.php:315-319` | `Unit/Traits/ResourceHelperTraitTest` (union) |
| `extractComplexClassExpression()` -- returns null for unresolvable | implemented | `Traits/ResourceHelperTrait.php:321` | `Unit/Traits/ResourceHelperTraitTest` (null) |
| `extractUnionMembers()` -- traverses RDF list | implemented | `Traits/ResourceHelperTrait.php:329-350` | `Unit/Traits/ResourceHelperTraitTest` + `Characterization/ResourceHelperTraitTest` |
| `extractUnionMembers()` -- returns empty for no `owl:unionOf` | implemented | `Traits/ResourceHelperTrait.php:334-336` | `Unit/Traits/ResourceHelperTraitTest` (empty) |

---

## Value Objects

### ParsedOntology

Immutable value object representing a fully parsed ontology. Returned by `OntologyParserInterface::parse()`.

| Feature | Status | Location | Tests |
|---|---|---|---|
| 7 readonly promoted properties | implemented | `ValueObjects/ParsedOntology.php:23-31` | `Unit/ValueObjects/ParsedOntologyTest` (reflection readonly check) |
| `classes: array` (default `[]`) | implemented | `ValueObjects/ParsedOntology.php:24` | `Unit/ValueObjects/ParsedOntologyTest` (default + populated) |
| `properties: array` (default `[]`) | implemented | `ValueObjects/ParsedOntology.php:25` | `Unit/ValueObjects/ParsedOntologyTest` (default + populated) |
| `prefixes: array` (default `[]`) | implemented | `ValueObjects/ParsedOntology.php:26` | `Unit/ValueObjects/ParsedOntologyTest` (default + populated) |
| `shapes: array` (default `[]`) | implemented | `ValueObjects/ParsedOntology.php:27` | `Unit/ValueObjects/ParsedOntologyTest` (default + populated) |
| `restrictions: array` (default `[]`) | implemented | `ValueObjects/ParsedOntology.php:28` | `Unit/ValueObjects/ParsedOntologyTest` (default + populated) |
| `metadata: array` (default `[]`) | implemented | `ValueObjects/ParsedOntology.php:29` | `Unit/ValueObjects/ParsedOntologyTest` (default + populated) |
| `rawContent: string` (default `''`) | implemented | `ValueObjects/ParsedOntology.php:30` | `Unit/ValueObjects/ParsedOntologyTest` (default + populated) |
| All defaults are `[]` / `''`, never `null` | verified | `ValueObjects/ParsedOntology.php:24-30` | `Unit/ValueObjects/ParsedOntologyTest` (null-safety test) |
| Constructor supports partial named arguments | verified | -- | `Unit/ValueObjects/ParsedOntologyTest` (partial args test) |
| Throws `\Error` on property reassignment | verified | -- | `Unit/ValueObjects/ParsedOntologyTest` (reassignment test) |
| `final class` | implemented | `ValueObjects/ParsedOntology.php:14` | -- |
| Realistic FOAF ontology data construction | verified | -- | `Unit/ValueObjects/ParsedOntologyTest` (FOAF test) |

### ParsedRdf

Immutable value object wrapping an `EasyRdf\Graph` with format metadata. Produced by format handlers.

| Feature | Status | Location | Tests |
|---|---|---|---|
| 4 readonly promoted properties | implemented | `ValueObjects/ParsedRdf.php:20-25` | `Unit/ValueObjects/ParsedRdfTest` + `Characterization/ParsedRdfTest` |
| `graph: Graph` | implemented | `ValueObjects/ParsedRdf.php:21` | `Unit/ValueObjects/ParsedRdfTest` (identity check) |
| `format: string` | implemented | `ValueObjects/ParsedRdf.php:22` | `Unit/ValueObjects/ParsedRdfTest` (string check) |
| `rawContent: string` | implemented | `ValueObjects/ParsedRdf.php:23` | `Unit/ValueObjects/ParsedRdfTest` (string check) |
| `metadata: array` (default `[]`) | implemented | `ValueObjects/ParsedRdf.php:24` | `Unit/ValueObjects/ParsedRdfTest` + `Characterization/ParsedRdfTest` |
| `getResourceCount(): int` | implemented | `ValueObjects/ParsedRdf.php:32-35` | `Unit/ValueObjects/ParsedRdfTest` + `Characterization/ParsedRdfTest` |
| `isEmpty(): bool` | implemented | `ValueObjects/ParsedRdf.php:42-45` | `Unit/ValueObjects/ParsedRdfTest` + `Characterization/ParsedRdfTest` |
| `getResources(): array` | implemented | `ValueObjects/ParsedRdf.php:52-55` | `Unit/ValueObjects/ParsedRdfTest` + `Characterization/ParsedRdfTest` |
| `toArray()` returns `format`, `resource_count`, `metadata` | implemented | `ValueObjects/ParsedRdf.php:63-71` | `Unit/ValueObjects/ParsedRdfTest` + `Characterization/ParsedRdfTest` |
| `toArray()` excludes `rawContent` and `graph` | implemented | `ValueObjects/ParsedRdf.php:63-71` | `Unit/ValueObjects/ParsedRdfTest` + `Characterization/ParsedRdfTest` |
| Throws `\Error` on property reassignment | verified | -- | `Unit/ValueObjects/ParsedRdfTest` + `Characterization/ParsedRdfTest` |
| `final class` | implemented | `ValueObjects/ParsedRdf.php:16` | -- |

---

## Support / Helper Classes

### EasyRdfGraph

Default (and currently only) implementation of `GraphInterface`. Delegates all operations to
an underlying `EasyRdf\Graph` instance.

| Feature | Status | Location | Tests |
|---|---|---|---|
| Implements `GraphInterface` | implemented | `Support/EasyRdfGraph.php:17` | `Unit/Support/EasyRdfGraphTest` (instanceof) |
| `final class` | implemented | `Support/EasyRdfGraph.php:17` | `Unit/Support/EasyRdfGraphTest` (reflection isFinal) |
| Default construction creates empty graph | implemented | `Support/EasyRdfGraph.php:21-24` | `Unit/Support/EasyRdfGraphTest` (empty resources) |
| Accepts injected `Graph` instance | implemented | `Support/EasyRdfGraph.php:21` | `Unit/Support/EasyRdfGraphTest` (identity check) |
| `resource()` -- delegates, handles null type separately | implemented | `Support/EasyRdfGraph.php:26-36` | `Unit/Support/EasyRdfGraphTest` (with and without type) |
| `resources()` -- returns all resources | implemented | `Support/EasyRdfGraph.php:41-44` | `Unit/Support/EasyRdfGraphTest` (URI verification) |
| `allOfType()` -- returns typed resources | implemented | `Support/EasyRdfGraph.php:49-52` | `Unit/Support/EasyRdfGraphTest` (owl:Class filter) |
| `allOfType()` -- empty result for missing types | implemented | `Support/EasyRdfGraph.php:49-52` | `Unit/Support/EasyRdfGraphTest` (empty array) |
| `getNamespaceMap()` -- delegates to global `RdfNamespace::namespaces()` | implemented | `Support/EasyRdfGraph.php:64-67` | `Unit/Support/EasyRdfGraphTest` (standard + custom ns) |
| `parse()` -- delegates with triple count return | implemented | `Support/EasyRdfGraph.php:69-72` | `Unit/Support/EasyRdfGraphTest` (4 triples from Turtle) |
| `parse()` -- supports base URI parameter | implemented | `Support/EasyRdfGraph.php:69-72` | `Unit/Support/EasyRdfGraphTest` (relative URI resolution) |
| `parse()` -- propagates exceptions for invalid format | verified | -- | `Unit/Support/EasyRdfGraphTest` (EasyRdf\Exception) |
| `getInnerGraph()` -- concrete-class-only accessor | implemented | `Support/EasyRdfGraph.php:82-85` | `Unit/Support/EasyRdfGraphTest` (identity + type check) |

### RdfHelper

Static utility class for IRI manipulation.

| Feature | Status | Location | Tests |
|---|---|---|---|
| `extractLocalName()` -- fragment URI (`#`) | implemented | `Support/RdfHelper.php:11` | `Unit/Support/RdfHelperTest` + `Characterization/RdfHelperTest` |
| `extractLocalName()` -- path URI (`/`) | implemented | `Support/RdfHelper.php:11` | `Unit/Support/RdfHelperTest` + `Characterization/RdfHelperTest` |
| `extractLocalName()` -- URN format (`urn:`) | implemented | `Support/RdfHelper.php:14-16` | `Unit/Support/RdfHelperTest` + `Characterization/RdfHelperTest` |
| `extractLocalName()` -- returns full string when no delimiter | implemented | `Support/RdfHelper.php:18` | `Unit/Support/RdfHelperTest` + `Characterization/RdfHelperTest` |
| `extractLocalName()` -- standard W3C URIs | verified | -- | `Unit/Support/RdfHelperTest` + `Characterization/RdfHelperTest` |
| `extractNamespace()` -- fragment URI | implemented | `Support/RdfHelper.php:21-29` | `Unit/Support/RdfHelperTest` + `Characterization/RdfHelperTest` |
| `extractNamespace()` -- path URI | implemented | `Support/RdfHelper.php:21-29` | `Unit/Support/RdfHelperTest` + `Characterization/RdfHelperTest` |
| `extractNamespace()` -- returns `''` when no delimiter | implemented | `Support/RdfHelper.php:24-26` | `Unit/Support/RdfHelperTest` + `Characterization/RdfHelperTest` |
| `humanizeLocalName()` -- camelCase to words | implemented | `Support/RdfHelper.php:31-37` | `Unit/Support/RdfHelperTest` + `Characterization/RdfHelperTest` |
| `humanizeLocalName()` -- snake_case to words | implemented | `Support/RdfHelper.php:34` | `Unit/Support/RdfHelperTest` + `Characterization/RdfHelperTest` |
| `humanizeLocalName()` -- PascalCase to words | implemented | `Support/RdfHelper.php:33` | `Unit/Support/RdfHelperTest` + `Characterization/RdfHelperTest` |
| `humanizeLocalName()` -- single word | implemented | `Support/RdfHelper.php:36` | `Unit/Support/RdfHelperTest` + `Characterization/RdfHelperTest` |
| `humanizeLocalName()` -- all uppercase | implemented | `Support/RdfHelper.php:36` | `Unit/Support/RdfHelperTest` + `Characterization/RdfHelperTest` |
| `final class` | implemented | `Support/RdfHelper.php:7` | -- |

---

## Backward-Compatibility Alias Bridge

The `aliases.php` file provides a `spl_autoload_register` bridge mapping 4 old `App\` namespace
classes to new `Youri\vandenBogert\Software\ParserCore\` classes. All aliases will be removed in v2.0.

| Feature | Status | Location | Tests |
|---|---|---|---|
| `App\...\OntologyParserInterface` resolves | implemented | `aliases.php:16` | `Unit/AliasesTest` (alias resolution) |
| `App\...\RdfFormatHandlerInterface` resolves | implemented | `aliases.php:17` | `Unit/AliasesTest` (alias resolution) |
| `App\...\ParsedRdf` resolves | implemented | `aliases.php:18` | `Unit/AliasesTest` (alias resolution) |
| `App\...\OntologyImportException` resolves to `ParserException` | implemented | `aliases.php:19` | `Unit/AliasesTest` (alias resolution) |
| `instanceof` works with old namespace names | verified | -- | `Unit/AliasesTest` (4 instanceof checks) |
| Old interfaces are implementable | verified | -- | `Unit/AliasesTest` (anonymous impl) |
| `E_USER_DEPRECATED` triggered on-demand (not at autoload) | implemented | `aliases.php:23-30` | `Unit/AliasesTest` (subprocess deprecation capture) |
| Deprecation messages include old and new FQCN | implemented | `aliases.php:26-28` | `Unit/AliasesTest` (message content check) |
| Deprecation messages mention v2.0 removal | implemented | `aliases.php:28` | `Unit/AliasesTest` (v2.0 mention check) |
| No aliases for internal classes (`GraphInterface`, `ResourceHelperTrait`, `RdfHelper`) | verified | `aliases.php` | `Unit/AliasesTest` (negative checks) |

---

## Test Structure

| Test Suite | File Count | Description |
|---|---|---|
| `tests/Unit/Contracts/` | 3 files | Signature verification, method counts, anonymous implementations |
| `tests/Unit/Exceptions/` | 5 files | Individual exception tests + cross-hierarchy behavior |
| `tests/Unit/Support/` | 2 files | `EasyRdfGraph` delegation + `RdfHelper` static methods |
| `tests/Unit/Traits/` | 1 file | All 15 `ResourceHelperTrait` methods |
| `tests/Unit/ValueObjects/` | 2 files | `ParsedOntology` + `ParsedRdf` readonly properties and methods |
| `tests/Unit/AliasesTest.php` | 1 file | Backward-compatibility alias bridge |
| `tests/Unit/CoreSmokeTest.php` | 1 file | Basic instantiation smoke test |
| `tests/Characterization/` | 6 files | Pre-extraction characterization tests (legacy App\ namespace usage) |
| **Total** | **21 files** | **278 tests, 597 assertions, 6 deprecation warnings** |

---

## Architecture Notes

1. **Interface-first design** -- All three contracts (`GraphInterface`, `OntologyParserInterface`,
   `RdfFormatHandlerInterface`) are pure interfaces with no default methods, enabling clean
   implementation by downstream packages.

2. **Single exception root** -- `ParserException extends \RuntimeException` provides a single
   catch-all for all parser ecosystem errors, with domain-specific subtypes for granular handling.

3. **EasyRdf coupling (intentional)** -- `GraphInterface` method signatures reference `EasyRdf\Resource`
   directly. This is documented as an intentional MVP decision; a `ResourceInterface` abstraction
   is deferred to post-MVP.

4. **Global namespace state** -- `EasyRdfGraph::getNamespaceMap()` delegates to
   `EasyRdf\RdfNamespace::namespaces()`, which is global static state shared across all graph
   instances. This is an EasyRdf design constraint, not a per-graph feature.

5. **Trait over abstract class** -- `ResourceHelperTrait` is a trait (not an abstract class) to
   allow composition in classes that already extend other base classes.

6. **Characterization tests preserved** -- The `tests/Characterization/` suite tests behavior via
   old `App\` namespace aliases, documenting pre-extraction behavior. These co-exist with the
   `tests/Unit/` suite that tests via the new namespace directly.
