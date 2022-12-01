<?php declare(strict_types = 1);

namespace SimpleToImplement\XLSXParser;

use function count;

final class Row
{
    private array $values = [];

    public function addValue(int $columnIndex, mixed $value): void
    {
        if ('' !== $value) {
            $this->values[$columnIndex] = $value;
        }
    }

    public function getData(): array
    {
        $data = [];
        foreach ($this->values as $columnIndex => $value) {
            while (count(value: $data) < $columnIndex) {
                $data[] = '';
            }
            $data[] = $value;
        }

        return $data;
    }
}
