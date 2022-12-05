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
                $type = basename(path: (string) $xml->getAttribute(name: 'Type'));
                $this->storeRelationshipTarget(type: $type, rId: $xml->getAttribute(name: 'Id'), target: 'xl/' . $xml->getAttribute(name: 'Target'));
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

    private function storeRelationshipTarget(string $type, string $rId, string $target): void
    {
        switch ($type) {
            case 'worksheet':
                $this->workSheetPaths[$rId] = $target;
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
