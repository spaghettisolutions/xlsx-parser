<?php declare(strict_types = 1);

namespace SimpleToImplement\XLSXParser;

final class XLSXParser
{
    public function open(string $path): XLSX\XLSXInterface
    {
        return new XLSX\XLSX(
            archive: new XLSX\Archive(archivePath: $path),
        );
    }
}
