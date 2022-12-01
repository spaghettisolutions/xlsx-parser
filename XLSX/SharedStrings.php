<?php declare(strict_types = 1);

namespace SimpleToImplement\XLSXParser\XLSX;

use XMLReader;

class SharedStrings extends AbstractXMLDictionary
{
    private int $currentIndex = -1;

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
                        $this->values[$this->currentIndex] = $xml->readString();

                        return;
                }
            }
        }

        $this->valid = false;
        $this->closeXMLReader();
    }
}
