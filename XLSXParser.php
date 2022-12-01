<?php declare(strict_types = 1);

namespace Spaghetti;

final class XLSXParser
{
    public function open(string $path): XLSXParser\XLSXInterface
    {
        return new XLSXParser\XLSX(
            archive: new XLSXParser\Archive(archivePath: $path),
        );
    }
}
