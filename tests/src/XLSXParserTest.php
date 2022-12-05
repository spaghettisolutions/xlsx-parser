<?php declare(strict_types = 1);

namespace Spaghetti\Tests;

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
        $this->assertEquals('Alfred', $values[6][0]);
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

    public function testOpenNotXlsx(): void
    {
        $this->expectException(exception: InvalidArgumentException::class);
        $workbook = (new XLSXParser())->open(path: dirname(path: __DIR__) . '/assets/assets.zip');
        $workbook->getIndex(name: 'worksheet');
    }
}
