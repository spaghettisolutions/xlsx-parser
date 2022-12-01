<?php declare(strict_types = 1);

namespace SimpleToImplement\XLSXParser;

use XMLReader;

abstract class AbstractXMLResource
{
    private ?XMLReader $xml = null;

    public function __construct(private readonly string $path)
    {
    }

    public function __destruct()
    {
        if (null !== $this->xml) {
            $this->closeXMLReader();
        }
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
        $xml->open(uri: $this->path);

        return $xml;
    }

    protected function closeXMLReader(): void
    {
        $this->xml->close();
        $this->xml = null;
    }
}