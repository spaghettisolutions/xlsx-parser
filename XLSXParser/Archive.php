<?php declare(strict_types = 1);

namespace SimpleToImplement\XLSXParser;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use ZipArchive;

use function file_exists;
use function rmdir;
use function sprintf;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

final class Archive
{
    private string $tmpPath;
    private ?ZipArchive $zip = null;

    public function __construct(private readonly string $archivePath)
    {
        $this->tmpPath = tempnam(directory: sys_get_temp_dir(), prefix: 'xlsx_parser_archive');
        unlink(filename: $this->tmpPath);
    }

    public function __destruct()
    {
        $this->deleteTmp();
        $this->closeArchive();
    }

    public function extract(string $filePath): string
    {
        if (!file_exists(filename: $tmpPath = sprintf('%s/%s', $this->tmpPath, $filePath))) {
            $this->getArchive()->extractTo(pathto: $this->tmpPath, files: $filePath);
        }

        return $tmpPath;
    }

    private function getArchive(): ZipArchive
    {
        if (null === $this->zip) {
            $this->zip = new ZipArchive();
            if (true !== $errorCode = $this->zip->open(filename: $this->archivePath)) {
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
            } else {
                unlink(filename: $file->getRealPath());
            }
        }
        rmdir(directory: $this->tmpPath);
    }

    private function getErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            ZipArchive::ER_CHANGED => 'Entry has been changed',
            ZipArchive::ER_CLOSE => 'Closing zip archive failed',
            ZipArchive::ER_COMPNOTSUPP => 'Compression method not supported',
            ZipArchive::ER_CRC => 'CRC error',
            ZipArchive::ER_DELETED => 'Entry has been deleted',
            ZipArchive::ER_EOF => 'Premature EOF',
            ZipArchive::ER_EXISTS => 'File already exists',
            ZipArchive::ER_INCONS => 'Inconsistent zip archive',
            ZipArchive::ER_INTERNAL => 'Internal error',
            ZipArchive::ER_INVAL => 'Invalid argument',
            ZipArchive::ER_MEMORY => 'Malloc failure',
            ZipArchive::ER_MULTIDISK => 'Multi-disk zip archives not supported',
            ZipArchive::ER_NOENT => 'No such file',
            ZipArchive::ER_NOZIP => 'Not a zip archive',
            ZipArchive::ER_OPEN => 'Can\'t open file',
            ZipArchive::ER_READ => 'Read error',
            ZipArchive::ER_REMOVE => 'Can\'t remove file',
            ZipArchive::ER_RENAME => 'Renaming temporary file failed',
            ZipArchive::ER_SEEK => 'Seek error',
            ZipArchive::ER_TMPOPEN => 'Failure to create temporary file',
            ZipArchive::ER_WRITE => 'Write error',
            ZipArchive::ER_ZIPCLOSED => 'Zip archive is closed',
            ZipArchive::ER_ZLIB => 'Zlib error',
            default => sprintf('An unknown error has occurred: %d', $errorCode),
        };
    }
}
