<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser;

use XMLReader;

use function basename;

/**
 * @internal
 */
final class Relationships extends AbstractXMLResource
{
    private array $workSheetPaths;
    private string $sharedStringPath;
    private string $stylePath;

    public function __construct(string $path)
    {
        parent::__construct(path: $path);
        $xml = $this->getXMLReader();

        while ($xml->read()) {
            if (XMLReader::ELEMENT === $xml->nodeType && 'Relationship' === $xml->name) {
                $type = basename(path: (string) $xml->getAttribute(name: 'Type'));
                $this->storeRelationShipByType(type: $type, id: $xml->getAttribute(name: 'Id'), target: 'xl/' . $xml->getAttribute(name: 'Target'));
            }
        }

        $this->closeXMLReader();
    }

    public function getWorksheetPath(string $id): string
    {
        return $this->workSheetPaths[$id];
    }

    public function getSharedStringsPath(): string
    {
        return $this->sharedStringPath;
    }

    public function getStylesPath(): string
    {
        return $this->stylePath;
    }

    private function storeRelationShipByType(string $type, string $id, string $target): void
    {
        switch ($type) {
            case 'worksheet':
                $this->workSheetPaths[$id] = $target;
                break;
            case 'styles':
                $this->stylePath = $target;
                break;
            case 'sharedStrings':
                $this->sharedStringPath = $target;
                break;
        }
    }
}
