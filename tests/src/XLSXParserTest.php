<?php declare(strict_types = 1);

namespace Spaghetti\Tests;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Spaghetti\XLSXParser;

use function dirname;

class XLSXParserTest extends TestCase
{
    public function testOpen(): void
    {
        $workbook = (new XLSXParser())->open(path: dirname(path: __DIR__) . '/assets/workbook.xlsx');
        $this->assertEquals(expected: ['worksheet', ], actual: $workbook->getWorksheets());
        $values = [];
        foreach ($workbook->getRows(index: $workbook->getIndex(name: 'worksheet')) as $key => $row) {
            $values[$key] = $row;
        }

        $this->assertCount(expectedCount: 201, haystack: $values);
        $this->assertEquals(expected: 'Alfred', actual: $values[6][0]);
        $this->assertEquals(expected: new DateTimeImmutable(datetime: '2022-12-05'), actual: $values[2][5]);
    }

    public function testOpenNotExists(): void
    {
        $this->expectException(exception: RuntimeException::class);
        $workbook = (new XLSXParser())->open(path: dirname(path: __DIR__) . '/assets/workbook2.xlsx');
        $this->assertEquals(expected: ['worksheet', ], actual: $workbook->getWorksheets());
    }

    public function testOpenWrongIndex(): void
    {
        $this->expectException(exception: InvalidArgumentException::class);
        $workbook = (new XLSXParser())->open(path: dirname(path: __DIR__) . '/assets/workbook.xlsx');
        $workbook->getIndex(name: 'worksheet2');
    }

    public function testOpenNotZip(): void
    {
        $this->expectException(exception: RuntimeException::class);
        $workbook = (new XLSXParser())->open(path: __DIR__ . '/XLSXParserTest.php');
        $workbook->getIndex(name: 'worksheet');
    }

    public function testOpenNotXlsxZip(): void
    {
        $this->expectException(exception: InvalidArgumentException::class);
        $workbook = (new XLSXParser())->open(path: dirname(path: __DIR__) . '/assets/assets.zip');
        $workbook->getIndex(name: 'worksheet');
    }

    public function testOpenNotXlsxXml(): void
    {
        $this->expectException(exception: RuntimeException::class);
        $workbook = (new XLSXParser())->open(path: dirname(path: __DIR__) . '/assets/test.xml');
        $workbook->getIndex(name: 'worksheet');
    }
}
