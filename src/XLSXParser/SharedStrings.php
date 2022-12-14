<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser;

use XMLReader;

use function str_replace;
use function trim;

/**
 * @internal
 */
final class SharedStrings extends AbstractXMLDictionary
{
    private int $currentIndex = -1;

    protected function readNext(): void
    {
        $xml = $this->getXMLReader();

        while ($xml->read()) {
            if (XMLReader::ELEMENT === $xml->nodeType) {
                $this->process(xml: $xml);
            }
        }

        $this->valid = false;
        $this->closeXMLReader();
    }

    private function process(XMLReader $xml): void
    {
        match ($xml->name) {
            'si' => $this->currentIndex++,
            't' => $this->values[$this->currentIndex] = trim(string: str_replace(search: "\u{a0}", replace: ' ', subject: $xml->readString()), characters: ' '),
            default => null,
        };
    }
}
