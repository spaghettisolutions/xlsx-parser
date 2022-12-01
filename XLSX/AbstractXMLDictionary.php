<?php declare(strict_types = 1);

namespace SimpleToImplement\XLSXParser\XLSX;

use InvalidArgumentException;

use function sprintf;

abstract class AbstractXMLDictionary extends AbstractXMLResource
{
    protected bool $valid = true;
    protected array $values = [];

    public function get(int $index): mixed
    {
        while ($this->valid && !isset($this->values[$index])) {
            $this->readNext();
        }
        if (!isset($this->values[$index])) {
            throw new InvalidArgumentException(message: sprintf('No value with index %s', $index));
        }

        return $this->values[$index];
    }

    abstract protected function readNext();
}
