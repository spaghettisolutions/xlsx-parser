<?php declare(strict_types = 1);

namespace SimpleToImplement\XLSXParser\XLSX;

use XMLReader;

final class WorksheetListReader
{
    public function getWorksheetPaths(Relationships $relationships, $path): array
    {
        $xml = new XMLReader();
        $xml->open(uri: $path);
        $paths = [];

        while ($xml->read()) {
            if (XMLReader::ELEMENT === $xml->nodeType && 'sheet' === $xml->name) {
                $rId = $xml->getAttributeNs(
                    name: 'id',
                    namespace: 'http://schemas.openxmlformats.org/officeDocument/2006/relationships',
                );
                $paths[$xml->getAttribute(name: 'name')] = $relationships->getWorksheetPath(id: $rId);
            }
        }

        return $paths;
    }
}
