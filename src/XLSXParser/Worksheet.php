<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser;

use XMLReader;

/**
 * @internal
 */
final class Worksheet extends AbstractXMLResource
{
    private const SHEET = 'sheet';
    private const ID = 'id';
    private const NAME = 'name';

    public function getWorksheetPaths(Relationships $relationships): array
    {
        $xml = $this->getXMLReader();
        $paths = [];

        while ($xml->read()) {
            if (XMLReader::ELEMENT === $xml->nodeType && self::SHEET === $xml->name) {
                $rId = $xml->getAttributeNs(name: self::ID, namespace: 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
                $paths[$xml->getAttribute(name: self::NAME)] = $relationships->getWorksheetPath(rId: $rId);
            }
        }

        return $paths;
    }
}
