<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser;

use XMLReader;

use function basename;

/**
 * @internal
 */
final class Relationships extends AbstractXMLResource
{
    private const WORKSHEET = 'worksheet';
    private const STYLES = 'styles';
    private const SHARED_STRINGS = 'sharedStrings';
    private const RELATIONSHIP = 'Relationship';
    private const TARGET = 'Target';
    private const TYPE = 'Type';
    private const ID = 'Id';

    private array $workSheetPaths = [];
    private string $sharedStringPath = '';
    private string $stylePath = '';

    public function __construct(string $path)
    {
        parent::__construct(path: $path);
        $xml = $this->getXMLReader();

        while ($xml->read()) {
            if (XMLReader::ELEMENT === $xml->nodeType && self::RELATIONSHIP === $xml->name) {
                $target = 'xl/' . $xml->getAttribute(name: self::TARGET);

                match (basename(path: (string) $xml->getAttribute(name: self::TYPE))) {
                    self::WORKSHEET => $this->workSheetPaths[$xml->getAttribute(name: self::ID)] = $target,
                    self::STYLES => $this->stylePath = $target,
                    self::SHARED_STRINGS => $this->sharedStringPath = $target,
                    default => null,
                };
            }
        }

        $this->closeXMLReader();
    }

    public function getWorksheetPath(string $rId): string
    {
        return $this->workSheetPaths[$rId];
    }

    public function getSharedStringsPath(): string
    {
        return $this->sharedStringPath;
    }

    public function getStylesPath(): string
    {
        return $this->stylePath;
    }
}
