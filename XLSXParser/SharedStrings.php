<?php declare(strict_types = 1);

namespace SimpleToImplement\XLSXParser;

use XMLReader;

use function str_replace;
use function trim;

final class SharedStrings extends AbstractXMLDictionary
{
    private int $currentIndex = -1;

    public function __construct(string $path)
    {
        parent::__construct(path: $path);
    }

    protected function readNext(): void
    {
        $xml = $this->getXMLReader();
        while ($xml->read()) {
            if (XMLReader::ELEMENT === $xml->nodeType) {
                switch ($xml->name) {
                    case 'si':
                        $this->currentIndex++;
                        break;
                    case 't':
                        $this->values[$this->currentIndex] = trim(string: str_replace(search: "\u{a0}", replace: ' ', subject: $xml->readString()), characters: ' ');

                        return;
                }
            }
        }

        $this->valid = false;
        $this->closeXMLReader();
    }
}
