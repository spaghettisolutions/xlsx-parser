<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser;

use Iterator;
use XMLReader;

/**
 * @internal
 */
final class RowIterator implements Iterator
{
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
        if ('row' === $this->xml->name) {
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
                'row' => $this->processRow(),
                'c' => $this->processColumn(),
                'v' => $this->processValue(),
                default => $this->processDefault(),
            };
        }
    }

    private function processRow(): void
    {
        $this->currentKey = (int) $this->xml->getAttribute(name: 'r');
        $this->row = new Row();
    }

    private function processColumn(): void
    {
        $this->index = $this->columnTransformer->transform(name: $this->xml->getAttribute(name: 'r'));
        $this->style = $this->xml->getAttribute(name: 's') ?? '';
        $this->type = $this->xml->getAttribute(name: 't') ?? '';
    }

    private function processValue(): void
    {
        $this->row?->addValue(
            columnIndex: $this->index,
            value: $this->valueTransformer->transform(value: $this->xml->readString(), type: $this->type, style: $this->style),
        );
    }

    private function processDefault(): void
    {
        $this->row?->addValue(columnIndex: $this->index, value: $this->xml->readString());
    }
}
