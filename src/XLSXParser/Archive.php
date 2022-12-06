<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use RuntimeException;
use ZipArchive;

use function file_exists;
use function rmdir;
use function sprintf;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

/**
 * @internal
 */
final class Archive
{
    private string $tmpPath;
    private ?ZipArchive $zip = null;

    public function __construct(private readonly string $archivePath)
    {
        $this->tmpPath = tempnam(directory: sys_get_temp_dir(), prefix: 'spaghetti_xlsx_parser_archive');
        unlink(filename: $this->tmpPath);
    }

    public function __destruct()
    {
        $this->deleteTmp();
        $this->closeArchive();
    }

    public function extract(string $filePath): string
    {
        $tmpPath = sprintf('%s/%s', $this->tmpPath, $filePath);

        if (!file_exists(filename: $tmpPath)) {
            $this->getArchive()->extractTo(pathto: $this->tmpPath, files: $filePath);
        }

        return $tmpPath;
    }

    private function getArchive(): ZipArchive
    {
        if (null === $this->zip) {
            $this->zip = new ZipArchive();
            $errorCode = $this->zip->open(filename: $this->archivePath);

            if (true !== $errorCode) {
                $this->zip = null;
                throw new RuntimeException(message: 'Error opening file: ' . $this->getErrorMessage(errorCode: $errorCode));
            }
        }

        return $this->zip;
    }

    private function closeArchive(): void
    {
        if (null !== $this->zip) {
            $this->zip->close();
            $this->zip = null;
        }
    }

    private function deleteTmp(): void
    {
        if (!file_exists(filename: $this->tmpPath)) {
            return;
        }

        $files = new RecursiveIteratorIterator(
            iterator: new RecursiveDirectoryIterator(directory: $this->tmpPath, flags: FilesystemIterator::SKIP_DOTS),
            mode: RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir(directory: $file->getRealPath());
                continue;
            }

            unlink(filename: $file->getRealPath());
        }

        rmdir(directory: $this->tmpPath);
    }

    private function getErrorMessage(int $errorCode): string
    {
        return sprintf('An error has occured: %s::%s (%d)', ZipArchive::class, $this->getZipErrorString(value: $errorCode), $errorCode);
    }

    private function getZipErrorString(int $value): string
    {
        $map = array_flip(array: (new ReflectionClass(objectOrClass: ZipArchive::class))->getConstants());

        return array_key_exists(key: $value, array: $map) ? $map[$value] : 'UNKNOWN';
    }
}
