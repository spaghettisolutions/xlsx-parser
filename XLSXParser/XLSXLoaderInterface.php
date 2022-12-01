<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser;

interface XLSXLoaderInterface
{
    public function open(string $path): XLSXInterface;
}
