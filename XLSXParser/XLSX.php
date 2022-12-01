<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser;

use InvalidArgumentException;
use Iterator;

use function array_keys;
use function array_search;
use function array_values;
use function is_int;
use function sprintf;

final class XLSX implements XLSXInterface
{
    public const RELATIONSHIPS_PATH = 'xl/_rels/workbook.xml.rels';
    public const WORKBOOK_PATH = 'xl/workbook.xml';

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
            $path = $this->archive->extract(self::RELATIONSHIPS_PATH);
            $this->relationships = new Relationships(path: $path);
        }

        return $this->relationships;
    }

    private function getValueTransformer(): Transformer\Value
    {
        if (null === $this->valueTransformer) {
            $this->valueTransformer = new Transformer\Value(
                dateTransformer: new Transformer\Date(),
                sharedStrings: $this->getSharedStrings(),
                styles: $this->getStyles(),
            );
        }

        return $this->valueTransformer;
    }

    private function getSharedStrings(): SharedStrings
    {
        if (null === $this->sharedStrings) {
            $path = $this->archive->extract(filePath: $this->relationships->getSharedStringsPath());
            $this->sharedStrings = new SharedStrings(path: $path);
        }

        return $this->sharedStrings;
    }

    private function getWorksheetPaths(): array
    {
        if ([] === $this->worksheetPaths) {
            $path = $this->archive->extract(filePath: self::WORKBOOK_PATH);
            $this->worksheetPaths = (new WorksheetListReader())->getWorksheetPaths(relationships: $this->getRelationships(), path: $path);
        }

        return $this->worksheetPaths;
    }

    private function getStyles(): ?Styles
    {
        if (null === $this->styles) {
            $path = $this->archive->extract(filePath: $this->relationships->getStylesPath());
            $this->styles = new Styles(path: $path);
        }

        return $this->styles;
    }
}
