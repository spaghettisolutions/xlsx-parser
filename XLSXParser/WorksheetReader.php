<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser;

use XMLReader;

/**
 * @internal
 */
final class WorksheetReader extends AbstractXMLResource
{
    public function getWorksheetPaths(Relationships $relationships): array
    {
        $xml = $this->getXMLReader();
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
