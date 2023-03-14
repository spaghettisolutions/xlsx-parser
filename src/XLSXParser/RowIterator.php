<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser;

use Iterator;
use XMLReader;

/**
 * @internal
 */
final class RowIterator implements Iterator
{
    private const ROW = 'row';
    private const ROW_INDEX = 'r';
    private const COLUMN = 'c';
    private const VALUE = 'v';
    private const STYLE = 's';
    private const TYPE = 't';

    private ?Row $row = null;
    private XMLReader $xml;
    private array $currentValue;
    private bool $valid;
    private int $currentKey;
    private int $index;
    private readonly Transformer\Column $columnTransformer;
    private string $style;
    private string $type;

    public function __construct(
        private readonly Transformer\Value $valueTransformer,
        private readonly string $path,
        ?Transformer\Column $columnTransformer = null,
    ) {
        $this->columnTransformer = $columnTransformer ?? new Transformer\Column();
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

        while ($this->xml->read()) {
            $this->processEndElement();

            if ($this->valid) {
                return;
            }

            $this->process();
        }
    }

    public function rewind(): void
    {
        $xml = new XMLReader();

        $this->xml = false === $xml->open(uri: $this->path) ? null : $xml;

        $this->next();
    }

    public function valid(): bool
    {
        return $this->valid;
    }

    private function processEndElement(): void
    {
        if (XMLReader::END_ELEMENT === $this->xml->nodeType) {
            $this->processEndValue();
        }
    }

    private function processEndValue(): void
    {
        if (self::ROW === $this->xml->name) {
            $currentValue = $this->row?->getData();
            if ([] !== $currentValue) {
                $this->currentValue = $currentValue;
                $this->valid = true;
            }
        }
    }

    private function process(): void
    {
        if (XMLReader::ELEMENT === $this->xml->nodeType) {
            match ($this->xml->name) {
                self::ROW => $this->processRow(),
                self::COLUMN => $this->processColumn(),
                self::VALUE => $this->processValue(),
                default => $this->processDefault(),
            };
        }
    }

    private function processRow(): void
    {
        $this->currentKey = (int) $this->xml->getAttribute(name: self::ROW_INDEX);
        $this->row = new Row();
    }

    private function processColumn(): void
    {
        $this->index = $this->columnTransformer->transform(name: $this->xml->getAttribute(name: self::ROW_INDEX));
        $this->style = $this->xml->getAttribute(name: self::STYLE) ?? '';
        $this->type = $this->xml->getAttribute(name: self::TYPE) ?? '';
    }

    private function processValue(): void
    {
        $this->row?->addValue(columnIndex: $this->index, value: $this->valueTransformer->transform(value: $this->xml->readString(), type: $this->type, style: $this->style), );
    }

    private function processDefault(): void
    {
        $this->row?->addValue(columnIndex: $this->index, value: $this->xml->readString());
    }
}
