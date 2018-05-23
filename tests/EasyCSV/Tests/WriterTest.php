<?php

declare(strict_types=1);

namespace EasyCSV\Tests;

use EasyCSV\Reader;
use EasyCSV\Writer;
use PHPUnit\Framework\TestCase;

class WriterTest extends TestCase
{
    /** @var Writer */
    private $writer;

    public function setUp() : void
    {
        $this->writer    = new Writer(__DIR__ . '/write.csv');
        $this->writerBOM = new Writer(__DIR__ . '/write_bom.csv', 'r+', true);
    }

    public function testWriteRow() : void
    {
        $this->assertEquals(18, $this->writer->writeRow('test1, test2, test3'));
    }

    public function testWriteRowOnHeaders() : void
    {
        $writer = new Writer(__DIR__ . '/write.csv', 'r+', false, ['header1', 'header2', 'header3']);
        $this->assertEquals(18, $writer->writeRow('test1, test2, test3'));
    }

    public function testWriteBOMRow() : void
    {
        $this->assertEquals(57, $this->writerBOM->writeRow('колонка 1, колонка 2, колонка 3'));
    }

    public function testWriteFromArray() : void
    {
        $this->writer->writeRow('column1, column2, column3');
        $this->writer->writeFromArray([
            '1test1, 1test2ing this out, 1test3',
            ['2test1', '2test2 ing this out ok', '2test3'],
        ]);
        $reader  = new Reader(__DIR__ . '/write.csv');
        $results = $reader->getRow();
        $this->assertEquals([
            'column1' => '1test1',
            'column2' => '1test2ing this out',
            'column3' => '1test3',
        ], $results);
    }

    public function testWriteBOMFromArray() : void
    {
        $this->writerBOM->writeRow('колонка 1, колонка 2, колонка 3');
        $this->writerBOM->writeFromArray([
            'значение 1, значение 2, значение 3',
            ['значение 4', 'значение 5', 'значение 6'],
        ]);
        $reader  = new Reader(__DIR__ . '/write_bom.csv');
        $results = $reader->getRow();
        $this->assertEquals([
            'колонка 1' => 'значение 1',
            'колонка 2' => 'значение 2',
            'колонка 3' => 'значение 3',
        ], $results);
    }

    public function testReadWrittenFile() : void
    {
        $reader   = new Reader(__DIR__ . '/write.csv');
        $results  = $reader->getAll();
        $expected = [
            [
                'column1' => '1test1',
                'column2' => '1test2ing this out',
                'column3' => '1test3',
            ],
            [
                'column1' => '2test1',
                'column2' => '2test2 ing this out ok',
                'column3' => '2test3',
            ],
        ];
        $this->assertEquals($expected, $results);
    }

    public function testReadWrittenBOMFile() : void
    {
        $reader   = new Reader(__DIR__ . '/write_bom.csv');
        $results  = $reader->getAll();
        $expected = [
            [
                'колонка 1' => 'значение 1',
                'колонка 2' => 'значение 2',
                'колонка 3' => 'значение 3',
            ],
            [
                'колонка 1' => 'значение 4',
                'колонка 2' => 'значение 5',
                'колонка 3' => 'значение 6',
            ],
        ];
        $this->assertEquals($expected, $results);
    }
}
