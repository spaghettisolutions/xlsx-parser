<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser;

use XMLReader;

use function basename;

/**
 * @internal
 */
final class Relationships extends AbstractXMLResource
{
    private array $workSheetPaths = [];
    private string $sharedStringPath = '';
    private string $stylePath = '';

    public function __construct(string $path)
    {
        parent::__construct(path: $path);
        $xml = $this->getXMLReader();

        while ($xml->read()) {
            if (XMLReader::ELEMENT === $xml->nodeType && 'Relationship' === $xml->name) {
                $target = 'xl/' . $xml->getAttribute(name: 'Target');

                match (basename(path: (string) $xml->getAttribute(name: 'Type'))) {
                    'worksheet' => $this->workSheetPaths[$xml->getAttribute(name: 'Id')] = $target,
                    'styles' => $this->stylePath = $target,
                    'sharedStrings' => $this->sharedStringPath = $target,
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
