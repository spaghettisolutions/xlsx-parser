<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser;

use InvalidArgumentException;
use Iterator;

use function array_keys;
use function array_search;
use function array_values;
use function is_int;
use function sprintf;

/**
 * @internal
 */
final class XLSX implements Contracts\XLSXInterface
{
    private ?Relationships $relationships = null;
    private ?SharedStrings $sharedStrings = null;
    private ?Styles $styles = null;
    private ?Transformer\Value $valueTransformer = null;
    private ?array $worksheetPaths = null;

    public function __construct(private readonly Archive $archive)
    {
    }

    public function getWorksheets(): array
    {
        return array_keys(array: $this->getWorksheetPaths());
    }

    public function getRows(int $index): Iterator
    {
        return new RowIterator(
            valueTransformer: $this->getValueTransformer(),
            path: $this->archive->extract(filePath: array_values(array: $this->getWorksheetPaths())[$index]),
        );
    }

    public function getIndex(string $name): int
    {
        $result = array_search(needle: $name, haystack: $this->getWorksheets(), strict: true);

        return match (is_int(value: $result)) {
            true => $result,
            default => throw new InvalidArgumentException(message: sprintf('Invalid name: "%s"', $name)),
        };
    }

    private function getRelationships(): Relationships
    {
        return $this->relationships ??= new Relationships(path: $this->archive->extract(filePath: 'xl/_rels/workbook.xml.rels'));
    }

    private function getValueTransformer(): Transformer\Value
    {
        return $this->valueTransformer ??= new Transformer\Value(sharedStrings: $this->getSharedStrings(), styles: $this->getStyles());
    }

    private function getSharedStrings(): SharedStrings
    {
        return $this->sharedStrings ??= new SharedStrings(path: $this->archive->extract(filePath: $this->getRelationships()->getSharedStringsPath()));
    }

    private function getWorksheetPaths(): array
    {
        return $this->worksheetPaths ??= (new Worksheet(path: $this->archive->extract(filePath: 'xl/workbook.xml')))->getWorksheetPaths(relationships: $this->getRelationships());
    }

    private function getStyles(): ?Styles
    {
        return $this->styles ??= new Styles(path: $this->archive->extract(filePath: $this->getRelationships()->getStylesPath()));
    }
}
