<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser;

use XMLReader;

use function in_array;
use function preg_match;

/**
 * @internal
 */
final class Styles extends AbstractXMLDictionary
{
    public const FORMAT_DATE = 1;
    private const FORMAT_DEFAULT = 0;

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

                $this->values[] = match (true) {
                    isset($this->numberFormats[$fmtId]) => $this->numberFormats[$fmtId],
                    in_array(needle: $fmtId, haystack: $this->nativeDateFormats, strict: true) => self::FORMAT_DATE,
                    default => self::FORMAT_DEFAULT,
                };

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
