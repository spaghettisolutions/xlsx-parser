<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser\Exception;

use InvalidArgumentException;
use Throwable;

use function sprintf;

final class InvalidIndexException extends InvalidArgumentException
{
    public function __construct(string $name, ?Throwable $previous = null)
    {
        parent::__construct(message: sprintf('Invalid name: "%s"', $name), previous: $previous);
    }
}
