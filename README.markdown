EasyCSV
=======

EasyCSV is a simple Object Oriented CSV manipulation library for PHP 7.1+

[![Build Status](https://secure.travis-ci.org/jwage/easy-csv.png?branch=master)](http://travis-ci.org/jwage/easy-csv)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/jwage/easy-csv/badges/quality-score.png?s=7e0e1d4b5d7f6be61a3cd804dba556a0e4d1141d)](https://scrutinizer-ci.com/g/jwage/easy-csv/)
[![Code Coverage](https://scrutinizer-ci.com/g/jwage/easy-csv/badges/coverage.png?s=a02332bc4d6a32df3171f2ba714e4583a70c0154)](https://scrutinizer-ci.com/g/jwage/easy-csv/)
[![Latest Stable Version](https://poser.pugx.org/jwage/easy-csv/v/stable.png)](https://packagist.org/packages/jwage/easy-csv)
[![Total Downloads](https://poser.pugx.org/jwage/easy-csv/downloads.png)](https://packagist.org/packages/jwage/easy-csv)

## Installation

Install via [composer](https://getcomposer.org/):

```sh
composer require jwage/easy-csv
```

## Reader

To read CSV files we need to instantiate the EasyCSV reader class:

```php
$reader = new \EasyCSV\Reader('read.csv');
```

You can iterate over the rows one at a time:

```php
while ($row = $reader->getRow()) {
    print_r($row);
}
```

Or you can get everything all at once:

```php
print_r($reader->getAll());
```

If you have a file with the header in a different line:

```php
// our headers aren't on the first line
$reader = new \EasyCSV\Reader('read.csv', 'r+', false);
// zero-based index, so this is line 4
$reader->setHeaderLine(3);
```

Advance to a different line:

```
$reader->advanceTo(6);
```

More in the Reader unit test.

## Writer

To write CSV files we need to instantiate the EasyCSV writer class:

```php
$writer = new \EasyCSV\Writer('write.csv');
```

You can write a row by passing a commas separated string:

```php
$writer->writeRow('column1, column2, column3');
```

Or you can pass an array:

```php
$writer->writeRow(array('column1', 'column2', 'column3'));
```

You can also write several rows at once:

```php
$writer->writeFromArray(array(
    'value1, value2, value3',
    array('value1', 'value2', 'value3')
));
```
