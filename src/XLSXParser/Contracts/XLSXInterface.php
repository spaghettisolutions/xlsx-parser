<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser\Contracts;

use Iterator;

interface XLSXInterface
{
    public function getIndex(string $name): int;

    public function getRows(int $index): Iterator;

    public function getWorksheets(): array;
}
