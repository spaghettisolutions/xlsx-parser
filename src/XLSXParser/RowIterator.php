<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser;

use Iterator;
use XMLReader;

use function count;

/**
 * @internal
 */
final class RowIterator implements Iterator
{
    private ?XMLReader $xml = null;
    private array $currentValue;
    private bool $valid;
    private ?int $currentKey;
    private readonly Transformer\ColumnIndex $columnIndexTransformer;

    public function __construct(
        private readonly Transformer\Value $valueTransformer,
        private readonly string $path,
        ?Transformer\ColumnIndex $columnIndexTransformer = null,
    ) {
        $this->columnIndexTransformer = $columnIndexTransformer ?? new Transformer\ColumnIndex();
    }

    public function current(): array
    {
        return $this->currentValue;
    }

    public function key(): int
    {
        return $this->currentKey;
    }

    public function next(): void
    {
        $this->valid = false;

        $style = $type = $columnIndex = $row = $currentKey = null;

        while ($this->xml->read()) {
            if (XMLReader::ELEMENT === $this->xml->nodeType) {
                $this->process($columnIndex, $currentKey, $row, $type, $style);

                continue;
            }

            if (XMLReader::END_ELEMENT === $this->xml->nodeType) {
                switch ($this->xml->name) {
                    case 'row':
                        $currentValue = $row->getData();
                        if (count(value: $currentValue)) {
                            $this->currentKey = $currentKey;
                            $this->currentValue = $currentValue;
                            $this->valid = true;

                            return;
                        }
                        break;
                    case 'sheetData':
                        break 2;
                }
            }
        }
    }

    public function rewind(): void
    {
        $this->xml?->close();

        $this->xml = false === ($xml = XMLReader::open(uri: $this->path)) ? null : $xml;

        $this->next();
    }

    public function valid(): bool
    {
        return $this->valid;
    }

    private function process(?int &$columnIndex, ?int &$currentKey, ?Row &$row, ?string &$type, ?string &$style): void
    {
        switch ($this->xml->name) {
            case 'row':
                $currentKey = (int) $this->xml->getAttribute(name: 'r');
                $row = new Row();
                break;
            case 'c':
                $columnIndex = $this->columnIndexTransformer->transform(name: $this->xml->getAttribute(name: 'r'));
                $style = $this->xml->getAttribute(name: 's') ?? '';
                $type = $this->xml->getAttribute(name: 't') ?? '';
                break;
            case 'v':
                $row->addValue(
                    columnIndex: $columnIndex,
                    value: $this->valueTransformer->transform(value: $this->xml->readString(), type: $type, style: $style),
                );
                break;
            case 'is':
                $row->addValue(columnIndex: $columnIndex, value: $this->xml->readString());
                break;
        }
    }
}