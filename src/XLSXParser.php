<?php declare(strict_types = 1);

namespace Spaghetti;

use Spaghetti\XLSXParser\Contracts\XLSXInterface;
use Spaghetti\XLSXParser\Contracts\XLSXParserInterface;

final class XLSXParser implements XLSXParserInterface
{
    public function open(string $path): XLSXInterface
    {
        return new XLSXParser\XLSX(
            archive: new XLSXParser\Archive(archivePath: $path),
        );
    }
}
