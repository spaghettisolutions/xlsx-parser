<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser;

/**
 * @internal
 */
abstract class AbstractXMLDictionary extends AbstractXMLResource
{
    protected bool $valid = true;
    protected array $values = [];

    public function get(int $index): mixed
    {
        while ($this->valid && !isset($this->values[$index])) {
            $this->readNext();
        }

        return $this->values[$index];
    }

    abstract protected function readNext();
}
