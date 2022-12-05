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
        $workbook = (new XLSXParser())->open(dirname(__DIR__) . '/assets/workbook.xlsx');
        $this->assertEquals(['worksheet', ], $workbook->getWorksheets());
        $values = [];
        foreach ($workbook->getRows($workbook->getIndex('worksheet')) as $row) {
            $values[] = $row;
        }

        $this->assertCount(201, $values);
    }

    public function testOpenNotExists(): void
    {
        $this->expectException(RuntimeException::class);
        $workbook = (new XLSXParser())->open(dirname(__DIR__) . '/assets/workbook2.xlsx');
        $this->assertEquals(['worksheet', ], $workbook->getWorksheets());
    }

    public function testOpenWrongIndex(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $workbook = (new XLSXParser())->open(dirname(__DIR__) . '/assets/workbook.xlsx');
        $index = $workbook->getIndex('worksheet2');
    }
}
