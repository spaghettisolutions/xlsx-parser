<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser;

use XMLReader;

use function in_array;
use function preg_match;

final class Styles extends AbstractXMLDictionary
{
    public const FORMAT_DEFAULT = 0;
    public const FORMAT_DATE = 1;

    private array $nativeDateFormats = [14, 15, 16, 17, 18, 19, 20, 21, 22];
    private array $numberFormats = [];
    private bool $inXfs = false;

    protected function readNext(): void
    {
        $xml = $this->getXMLReader();

        while ($xml->read()) {
            if (XMLReader::END_ELEMENT === $xml->nodeType && 'cellXfs' === $xml->name) {
                break;
            }
            if (XMLReader::ELEMENT === $xml->nodeType && 'cellXfs' === $xml->name) {
                $this->inXfs = true;
            } elseif ($this->inXfs && XMLReader::ELEMENT === $xml->nodeType && 'xf' === $xml->name) {
                $fmtId = $xml->getAttribute(name: 'numFmtId');
                if (isset($this->numberFormats[$fmtId])) {
                    $value = $this->numberFormats[$fmtId];
                } elseif (in_array(needle: $fmtId, haystack: $this->nativeDateFormats, strict: true)) {
                    $value = self::FORMAT_DATE;
                } else {
                    $value = self::FORMAT_DEFAULT;
                }
                $this->values[] = $value;

                return;
            }
        }

        $this->valid = false;
        $this->closeXMLReader();
    }

    protected function createXMLReader(): XMLReader
    {
        $xml = parent::createXMLReader();
        $needsRewind = false;

        while ($xml->read()) {
            if (XMLReader::END_ELEMENT === $xml->nodeType && 'numFmts' === $xml->name) {
                break;
            }
            if (XMLReader::ELEMENT === $xml->nodeType) {
                switch ($xml->name) {
                    case 'numFmt':
                        $this->numberFormats[$xml->getAttribute(name: 'numFmtId')] =
                            preg_match(
                                pattern: '{^(\[\$[[:alpha:]]*-[0-9A-F]*\])*[hmsdy]}i',
                                subject: $xml->getAttribute(name: 'formatCode'),
                            ) ? self::FORMAT_DATE : self::FORMAT_DEFAULT;
                        break;
                    case 'cellXfs':
                        $needsRewind = true;
                        break;
                }
            }
        }

        if ($needsRewind) {
            $xml->close();
            $xml = parent::createXMLReader();
        }

        return $xml;
    }
}
