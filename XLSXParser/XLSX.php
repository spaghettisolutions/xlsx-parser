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

    public function createRowIterator(int $worksheetIndex, array $options = []): Iterator
    {
        return new RowIterator(
            columnIndexTransformer: new Transformer\ColumnIndex(),
            valueTransformer: $this->getValueTransformer(),
            path: $this->archive->extract(filePath: array_values(array: $this->getWorksheetPaths())[$worksheetIndex]),
        );
    }

    public function getWorksheetIndex(string $name): int
    {
        if (is_int(value: $result = array_search(needle: $name, haystack: $this->getWorksheets(), strict: true))) {
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
            $this->worksheetPaths = (new WorksheetReader())->getWorksheetPaths(relationships: $this->getRelationships(), path: $this->archive->extract(filePath: self::WORKBOOK_PATH));
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
