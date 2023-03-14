<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser;

use XMLReader;

use function strtr;
use function trim;

/**
 * @internal
 */
final class SharedStrings extends AbstractXMLDictionary
{
    private const INDEX = 'si';
    private const VALUE = 't';

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
            self::INDEX => $this->currentIndex++,
            self::VALUE => $this->values[$this->currentIndex] = trim(string: strtr($xml->readString(), ["\u{a0}" => ' ']), characters: ' '),
            default => null,
        };
    }
}
