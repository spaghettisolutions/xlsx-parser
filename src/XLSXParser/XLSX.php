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
    private const RELATIONSHIPS_PATH = 'xl/_rels/workbook.xml.rels';
    private const WORKBOOK_PATH = 'xl/workbook.xml';

    private ?Relationships $relationships = null;
    private ?SharedStrings $sharedStrings = null;
    private ?Styles $styles = null;
    private ?Transformer\Value $valueTransformer = null;
    private array $worksheetPaths = [];

    public function __construct(
        private readonly Archive $archive,
    ) {
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

        if (is_int(value: $result)) {
            return $result;
        }

        throw new InvalidArgumentException(message: sprintf('Invalid name: "%s"', $name));
    }

    private function getRelationships(): ?Relationships
    {
        if (null === $this->relationships) {
            $this->relationships = new Relationships(path: $this->archive->extract(filePath: self::RELATIONSHIPS_PATH));
        }

        return $this->relationships;
    }

    private function getValueTransformer(): Transformer\Value
    {
        if (null === $this->valueTransformer) {
            $this->valueTransformer = new Transformer\Value(
                sharedStrings: $this->getSharedStrings(),
                styles: $this->getStyles(),
            );
        }

        return $this->valueTransformer;
    }

    private function getSharedStrings(): SharedStrings
    {
        if (null === $this->sharedStrings) {
            $this->sharedStrings = new SharedStrings(path: $this->archive->extract(filePath: $this->relationships->getSharedStringsPath()));
        }

        return $this->sharedStrings;
    }

    private function getWorksheetPaths(): array
    {
        if ([] === $this->worksheetPaths) {
            $this->worksheetPaths = (new Worksheet(path: $this->archive->extract(filePath: self::WORKBOOK_PATH)))->getWorksheetPaths(relationships: $this->getRelationships());
        }

        return $this->worksheetPaths;
    }

    private function getStyles(): ?Styles
    {
        if (null === $this->styles) {
            $this->styles = new Styles(path: $this->archive->extract(filePath: $this->relationships->getStylesPath()));
        }

        return $this->styles;
    }
}
