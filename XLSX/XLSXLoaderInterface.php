<?php declare(strict_types = 1);

namespace SimpleToImplement\XLSXParser\XLSX;

interface XLSXLoaderInterface
{
    public function open(string $path): XLSXInterface;
}
