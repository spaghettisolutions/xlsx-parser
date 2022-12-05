<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser\Contracts;

use Iterator;

interface XLSXInterface
{
    public function createRowIterator(int $worksheetIndex, array $options = []): Iterator;

    public function getWorksheetIndex(string $name): int;

    public function getWorksheets(): array;
}
