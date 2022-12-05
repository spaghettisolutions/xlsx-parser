<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser\Contracts;

interface XLSXLoaderInterface
{
    public function open(string $path): XLSXInterface;
}
