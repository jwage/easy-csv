# EasyCSV

Set of PHP 5.3 classes for reading and writing CSV files.

## Reader

To read CSV files we need to instantiate the EasyCSV reader class:

    $reader = new \EasyCSV\Reader('read.csv');

You can iterate over the rows one at a time:

    while ($row = $reader->getRow()) {
        print_r($row);
    }

Or you can get everything all at once:

    print_r($reader->getAll());

## Writer

To write CSV files we need to instantiate the EasyCSV writer class:

    $writer = new \EasyCSV\Writer('write.csv');

You can write a row by passing a commas separated string:

    $writer->writeRow('column1, column2, column3');

Or you can pass an array:

    $writer->writeRow(array('column1', 'column2', 'column3'));

You can also write several rows at once:

    $writer->writeFromArray(array(
            'value1, value2, value3',
            array('value1', 'value2', 'value3')
    ));
