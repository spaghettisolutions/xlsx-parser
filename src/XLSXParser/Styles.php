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

    private array $nativeDateFormats = [14, 15, 16, 17, 18, 19, 20, 21, 22, ];
    private array $numberFormats = [];
    private bool $inXfs = false;
    private bool $needsRewind;

    protected function readNext(): void
    {
        $xml = $this->getXMLReader();

        while ($xml->read()) {
            if ('cellXfs' === $xml->name) {
                switch ($xml->nodeType) {
                    case XMLReader::END_ELEMENT:
                        break 2;
                    case XMLReader::ELEMENT:
                        $this->inXfs = true;
                        continue 2;
                }
            }

            $this->xfs(xml: $xml);
        }

        $this->valid = false;
        $this->closeXMLReader();
    }

    protected function createXMLReader(): XMLReader
    {
        $xml = parent::createXMLReader();
        $this->needsRewind = false;

        while ($xml->read()) {
            if (XMLReader::END_ELEMENT === $xml->nodeType && 'numFmts' === $xml->name) {
                break;
            }

            $this->process(xml: $xml);
        }

        return $this->processRewind(xml: $xml);
    }

    private function processRewind(XMLReader $xml): XMLReader
    {
        if ($this->needsRewind) {
            $xml->close();
            $xml = parent::createXMLReader();
        }

        return $xml;
    }

    private function xfs(XMLReader $xml): void
    {
        if ($this->inXfs && XMLReader::ELEMENT === $xml->nodeType && 'xf' === $xml->name) {
            $this->values[] = $this->getValue(fmtId: (int) $xml->getAttribute(name: 'numFmtId'));
        }
    }

    private function process(XMLReader $xml): void
    {
        if (XMLReader::ELEMENT === $xml->nodeType) {
            match ($xml->name) {
                'cellXfs' => $this->needsRewind = true,
                'numFmt' => $this->numberFormats[$xml->getAttribute(name: 'numFmtId')] = $this->matchDateFormat(xml: $xml),
                default => null,
            };
        }
    }

    private function matchDateFormat(XMLReader $xml): int
    {
        return preg_match(pattern: '{^(\[\$[[:alpha:]]*-[0-9A-F]*\])*[hmsdy]}i', subject: $xml->getAttribute(name: 'formatCode')) ? self::FORMAT_DATE : self::FORMAT_DEFAULT;
    }

    private function getValue(int $fmtId): int
    {
        return match (true) {
            in_array(needle: $fmtId, haystack: $this->nativeDateFormats, strict: true) => self::FORMAT_DATE,
            isset($this->numberFormats[$fmtId]) => $this->numberFormats[$fmtId],
            default => self::FORMAT_DEFAULT,
        };
    }
}
