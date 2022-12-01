# xlsx-parser

[![Stand With Ukraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner2-direct.svg)](https://vshymanskyy.github.io/StandWithUkraine)

Installation
---

The recommended way to install is via Composer:

```shell
composer require simpletoimplement/xlsx-parser
```

This package requires at least PHP version 8.1

### Very simple to implement xlsx parser to extract data from excel spreadsheets

Usage
---
```php
use SimpleToImplement\XLSXParser\XLSXParser;

$workbook = (new XLSXParser())->open('workbook.xlsx');
$index = $workbook->getWorksheetIndex('mysheet');

foreach ($workbook->createRowIterator($index) as $values) {
    var_dump($values);
}
```

Tests
---
