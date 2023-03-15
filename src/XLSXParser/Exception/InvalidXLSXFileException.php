<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser\Exception;

use InvalidArgumentException;
use Throwable;

use function sprintf;

final class InvalidXLSXFileException extends InvalidArgumentException
{
    public function __construct(string $path, ?Throwable $previous = null)
    {
        parent::__construct(message: sprintf('Not a XLSX file: %s', $path), previous: $previous);
    }
}
