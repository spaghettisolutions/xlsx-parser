<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser;

use Error;
use Exception;
use InvalidArgumentException;
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
        if (null === $this->xml) {
            $this->xml = $this->createXMLReader();
        }

        return $this->xml;
    }

    protected function createXMLReader(): XMLReader
    {
        $xml = new XMLReader();

        try {
            $xml->open(uri: $this->path);
        } catch (Exception|Error $exception) {
            throw new InvalidArgumentException(message: sprintf('Not a XLSX file: %s', $this->path), previous: $exception);
        }

        return $xml;
    }

    protected function closeXMLReader(): void
    {
        if (null !== $this->xml) {
            $this->xml->close();
            $this->xml = null;
        }
    }
}
