<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser\Exception;

use ReflectionClass;
use ReflectionProperty;
use RuntimeException;
use Throwable;
use ZipArchive;

use function array_flip;
use function array_key_exists;
use function sprintf;

/**
 * @internal
 */
final class InvalidArchiveException extends RuntimeException
{
    public function __construct(int $code, ?Throwable $previous = null)
    {
        parent::__construct(message: 'Error opening file: ' . $this->getErrorMessage(errorCode: $code), previous: $previous);
    }

    private function getErrorMessage(int $errorCode): string
    {
        return sprintf('An error has occured: %s::%s (%d)', ZipArchive::class, $this->getZipErrorString(value: $errorCode), $errorCode);
    }

    private function getZipErrorString(int $value): string
    {
        $map = array_flip(array: (new ReflectionClass(objectOrClass: ZipArchive::class))->getConstants(filter: ReflectionProperty::IS_PUBLIC));

        return array_key_exists(key: $value, array: $map) ? $map[$value] : 'UNKNOWN';
    }
}
