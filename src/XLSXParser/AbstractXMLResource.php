<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser;

use InvalidArgumentException;
use Throwable;
use XMLReader;

use function sprintf;

/**
 * @internal
 */
abstract class AbstractXMLResource
{
    private ?XMLReader $xml = null;

    public function __construct(private readonly string $path)
    {
    }

    public function __destruct()
    {
        $this->closeXMLReader();
    }

    protected function getXMLReader(): XMLReader
    {
        return $this->xml ??= $this->createXMLReader();
    }

    protected function createXMLReader(): XMLReader
    {
        return $this->validateXMLReader(xml: new XMLReader());
    }

    protected function closeXMLReader(): void
    {
        $this->xml?->close();
        $this->xml = null;
    }

    private function validateXMLReader(XMLReader $xml): XMLReader
    {
        try {
            $xml->open(uri: $this->path);
        } catch (Throwable $throwable) {
            throw new InvalidArgumentException(message: sprintf('Not a XLSX file: %s', $this->path), previous: $throwable);
        }

        return $xml;
    }
}
