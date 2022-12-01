<?php declare(strict_types = 1);

namespace SimpleToImplement\XLSXParser\XLSX;

use Iterator;

interface XLSXInterface
{
    final public const TYPE = 'xlsx';

    /** @return string[] */
    public function getWorksheets(): array;

    public function createRowIterator(int $worksheetIndex, array $options = []): Iterator;

    public function getWorksheetIndex(string $name): int;
}
