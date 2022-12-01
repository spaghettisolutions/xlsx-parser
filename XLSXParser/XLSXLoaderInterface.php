<?php declare(strict_types = 1);

namespace SimpleToImplement\XLSXParser;

interface XLSXLoaderInterface
{
    public function open(string $path): XLSXInterface;
}
