<?php declare(strict_types = 1);

namespace Spaghetti\Tests;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Spaghetti\XLSXParser;
use Spaghetti\XLSXParser\Exception\InvalidArchiveException;
use Spaghetti\XLSXParser\Exception\InvalidIndexException;
use Spaghetti\XLSXParser\Exception\InvalidXLSXFileException;

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

    public function testDataValidity(): void
    {
        $workbook = (new XLSXParser())->open(path: dirname(path: __DIR__) . '/assets/mock.xlsx');
        $this->assertEquals(expected: ['data', ], actual: $workbook->getWorksheets());
        $values = [];
        foreach ($workbook->getRows(index: $workbook->getIndex(name: 'data')) as $key => $row) {
            $values[$key] = $row;
        }

        $this->assertCount(expectedCount: 1001, haystack: $values);
        $data = [
            'JM1NC2MF8E0960570', 'Acura', 'TSX', 2012, 'Teal', 218514, 'Electric', 'Manual', 5.6, 272, 159, 1,
            73103.86, '5/10/2022', 'Augusto Grissett', 27, 'Male', '85426 International Trail', 'Néa Karyá',
            'Greece', '4/24/2018', '', 'XYZ789', 'Topicblab', 469689, '3/7/2029', 'Maecenas ut massa quis augue luctus tincidunt. Nulla mollis molestie lorem. Quisque ut erat.

Curabitur gravida nisi at nibh. In hac habitasse platea dictumst. Aliquam augue quam, sollicitudin vitae, consectetuer eget, rutrum at, lorem.

Integer tincidunt ante vel ipsum. Praesent blandit lacinia erat. Vestibulum sed magna at nunc commodo placerat.', true, 'Curabitur at ipsum ac tellus semper interdum. Mauris ullamcorper purus sit amet nulla. Quisque arcu libero, rutrum ac, lobortis vel, dapibus at, diam.', false,
        ];
        $this->assertEquals(expected: $data, actual: $values[2]);
    }

    public function testOpenCellXfs(): void
    {
        $workbook = (new XLSXParser())->open(path: dirname(path: __DIR__) . '/assets/samplefile.xlsx');
        $this->assertEquals(expected: ['Sample', 'Cities', ], actual: $workbook->getWorksheets());
        $values = [];

        foreach ($workbook->getRows(index: $workbook->getIndex(name: 'Cities')) as $row) {
            $values[] = $row;
        }

        foreach ($workbook->getRows(index: $workbook->getIndex(name: 'Sample')) as $row) {
            $values[] = $row;
        }

        $this->assertCount(expectedCount: 12, haystack: $values);
    }

    public function testOpenNotExists(): void
    {
        $this->expectException(exception: InvalidArchiveException::class);
        $workbook = (new XLSXParser())->open(path: dirname(path: __DIR__) . '/assets/workbook2.xlsx');
        $this->assertEquals(expected: ['worksheet', ], actual: $workbook->getWorksheets());
    }

    public function testOpenWrongIndex(): void
    {
        $this->expectException(exception: InvalidIndexException::class);
        $workbook = (new XLSXParser())->open(path: dirname(path: __DIR__) . '/assets/workbook.xlsx');
        $workbook->getIndex(name: 'worksheet2');
    }

    public function testOpenNotZip(): void
    {
        $this->expectException(exception: InvalidArchiveException::class);
        $workbook = (new XLSXParser())->open(path: __DIR__ . '/XLSXParserTest.php');
        $workbook->getIndex(name: 'worksheet');
    }

    public function testOpenNotXlsxZip(): void
    {
        $this->expectException(exception: InvalidXLSXFileException::class);
        $workbook = (new XLSXParser())->open(path: dirname(path: __DIR__) . '/assets/assets.zip');
        $workbook->getIndex(name: 'worksheet');
    }

    public function testOpenNotXlsxXml(): void
    {
        $this->expectException(exception: InvalidArchiveException::class);
        $workbook = (new XLSXParser())->open(path: dirname(path: __DIR__) . '/assets/test.xml');
        $workbook->getIndex(name: 'worksheet');
    }
}
