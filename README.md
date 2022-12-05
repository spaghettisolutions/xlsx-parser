<p align="center">
  <a href="https://github.com/spaghettisolutions"><img src="https://avatars.githubusercontent.com/u/99179033?s=84&v=4"></a><br>
</p>

<h1 align="center">XLSX Parser</h1>

<p align="center">Very simple to implement xlsx parser to extract data from spreadsheets</p>

What is it?
---
**XLSXParser** is blazingly fast xlsx parser for **php 8.1+**. It is made as a simple tool to get job done. No fancy options of any kind 
and no need for any extra libraries other than need for `zip` and `xmlreader` php extensions.  

---
* Initialize class. 
* Open workbook. 
* Choose worksheet. 
* And iterate through receiving each row as an array.

[![Test Coverage](https://api.codeclimate.com/v1/badges/70a54d59d6b335ff303c/test_coverage)](https://codeclimate.com/github/spaghettisolutions/xlsx-parser/test_coverage)

---
Installation
---
The recommended way to install is via Composer:

```shell
composer require spaghetti/xlsx-parser
```
Usage
---

```php
use Spaghetti\XLSXParser;

$workbook = (new XLSXParser())->open('workbook.xlsx');

foreach ($workbook->getRows($workbook->getIndex('worksheet')) as $values) {
    var_dump($values);
}
```

---
[![Stand With Ukraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner2-direct.svg)](https://vshymanskyy.github.io/StandWithUkraine)
