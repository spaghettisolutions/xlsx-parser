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
    private const CELL_XFS = 'cellXfs';
    private const NUM_FORMAT = 'numFmt';
    private const NUM_FORMAT_ID = 'numFmtId';
    private const FORMAT_CODE = 'formatCode';
    private const XF = 'xf';

    private array $nativeDateFormats = [14, 15, 16, 17, 18, 19, 20, 21, 22, ];
    private array $numberFormats = [];
    private bool $inXfs = false;
    private bool $needsRewind;

    protected function readNext(): void
    {
        $xml = $this->getXMLReader();

        while ($xml->read()) {
            if ($this->processCellXfs(xml: $xml)) {
                continue;
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
            $this->process(xml: $xml);
        }

        return $this->processRewind(xml: $xml);
    }

    private function processCellXfs(XMLReader $xml): bool
    {
        if (self::CELL_XFS === $xml->name) {
            return match ($xml->nodeType) {
                XMLReader::END_ELEMENT => true,
                XMLReader::ELEMENT => $this->inXfs = true,
                default => false,
            };
        }

        return false;
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
        if ($this->inXfs && XMLReader::ELEMENT === $xml->nodeType && self::XF === $xml->name) {
            $this->values[] = $this->getValue(fmtId: (int) $xml->getAttribute(name: self::NUM_FORMAT_ID));
        }
    }

    private function process(XMLReader $xml): void
    {
        if (XMLReader::ELEMENT === $xml->nodeType) {
            match ($xml->name) {
                self::NUM_FORMAT => $this->numberFormats[$xml->getAttribute(name: self::NUM_FORMAT_ID)] = $this->matchDateFormat(xml: $xml),
                self::CELL_XFS => $this->needsRewind = true,
                default => null,
            };
        }
    }

    private function matchDateFormat(XMLReader $xml): int
    {
        return preg_match(pattern: '{^(\[\$[[:alpha:]]*-[0-9A-F]*\])*[hmsdy]}i', subject: $xml->getAttribute(name: self::FORMAT_CODE)) ? self::FORMAT_DATE : self::FORMAT_DEFAULT;
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
