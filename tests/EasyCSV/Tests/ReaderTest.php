<?php

declare(strict_types=1);

namespace EasyCSV\Tests;

use EasyCSV\Reader;
use PHPUnit\Framework\TestCase;
use function count;
use function is_array;

class ReaderTest extends TestCase
{
    /** @var string[] */
    protected $headers = ['column1', 'column2', 'column3'];

    /** @var string[][] */
    protected $expectedRows = [
        0 =>
            [
                'column1' => '1column2value',
                'column2' => '1column3value',
                'column3' => '1column4value',
            ],
        1 =>
            [
                'column1' => '2column2value',
                'column2' => '2column3value',
                'column3' => '2column4value',
            ],
        2 =>
            [
                'column1' => '3column2value',
                'column2' => '3column3value',
                'column3' => '3column4value',
            ],
        3 =>
            [
                'column1' => '4column2value',
                'column2' => '4column3value',
                'column3' => '4column4value',
            ],
        4 =>
            [
                'column1' => '5column2value',
                'column2' => '5column3value',
                'column3' => '5column4value',
            ],
    ];

    /** @var string[] */
    protected $dataRow1 = [
        'column1' => '1column2value',
        'column2' => '1column3value',
        'column3' => '1column4value',
    ];

    /** @var string[] */
    protected $dataRow2 = [
        'column1' => '2column2value',
        'column2' => '2column3value',
        'column3' => '2column4value',
    ];

    /** @var string[] */
    protected $dataRow5 = [
        'column1' => '5column2value',
        'column2' => '5column3value',
        'column3' => '5column4value',
    ];

    /**
     * @dataProvider getReaders
     */
    public function testOneAtAtime(Reader $reader) : void
    {
        while ($row = $reader->getRow()) {
            $this->assertTrue(is_array($row));
            $this->assertEquals(3, count($row));
        }
    }

    /**
     * @dataProvider getReaders
     */
    public function testLastRowIsCorrect(Reader $reader) : void
    {
        $reader->getRow();
        $reader->getRow();
        $reader->getRow();
        $reader->getRow();
        $row = $reader->getRow();

        $expected = $this->dataRow5;

        $this->assertEquals($expected, $row);

        // line number should have not advanced
        $this->assertEquals(5, $reader->getLineNumber());
    }

    /**
     * @dataProvider getReaders
     */
    public function testIsEofReturnsCorrectly(Reader $reader) : void
    {
        $this->assertFalse($reader->isEof());

        $reader->getAll();

        $this->assertTrue($reader->isEof());
        $this->assertFalse($reader->getRow());
    }

    /**
     * @dataProvider getReaders
     */
    public function testGetAll(Reader $reader) : void
    {
        $this->assertEquals(5, count($reader->getAll()));
    }

    /**
     * @dataProvider getReaders
     */
    public function testGetHeaders(Reader $reader) : void
    {
        $this->assertEquals($this->headers, $reader->getHeaders());
    }

    /**
     * @dataProvider getReaders
     */
    public function testAdvanceTo(Reader $reader) : void
    {
        $this->assertEquals(0, $reader->getLineNumber());

        // getting a row before calling advanceTo() shouldn't affect it
        $dataRow1 = $reader->getRow();
        $this->assertEquals($this->dataRow1, $dataRow1);
        $this->assertEquals(2, $reader->getLineNumber());

        $reader->advanceTo(3);

        $this->assertEquals(3, $reader->getLineNumber());

        $reader->advanceTo(1);

        $row = $this->dataRow1;

        $actualRow = $reader->getRow();
        $this->assertEquals($row, $actualRow);
        $this->assertEquals(2, $reader->getLineNumber());

        $reader->advanceTo(3);

        $row = [
            'column1' => '3column2value',
            'column2' => '3column3value',
            'column3' => '3column4value',
        ];

        $this->assertEquals($row, $reader->getRow());
        $this->assertEquals(4, $reader->getLineNumber());
    }

    /**
     * @dataProvider getReadersNoHeadersFirstRow
     */
    public function testAdvanceToNoHeadersFirstRow(Reader $reader) : void
    {
        $firstMetaRow  = [
            0 => 'Some Meta Data',
            1 => '',
            2 => '',
        ];
        $secondMetaRow = [
            0 => 'Field: Value',
            1 => '',
            2 => '',
        ];

        $actualRow = $reader->getRow();
        $this->assertEquals($firstMetaRow, $actualRow);
        $this->assertEquals(1, $reader->getLineNumber());

        $actualRow = $reader->getRow();
        $this->assertEquals($secondMetaRow, $actualRow);
        $this->assertEquals(2, $reader->getLineNumber());

        // give it the ol' one-two-switcharoo
        $reader->advanceTo(3);
        $advancedRow = $reader->getRow();

        $this->assertEquals(
            $this->headers,
            $advancedRow
        );

        $reader->advanceTo(0);

        $this->assertEquals($firstMetaRow, $reader->getRow());
        $this->assertEquals(1, $reader->getLineNumber());

        $this->assertEquals($secondMetaRow, $reader->getRow());
        $this->assertEquals(2, $reader->getLineNumber());

        $reader->advanceTo(1);

        $row = $reader->getRow();
        $this->assertEquals(2, $reader->getLineNumber());
        $this->assertEquals(
            $secondMetaRow,
            $row
        );
    }

    /**
     * @param mixed[] $expectedRows
     *
     * @dataProvider advanceToLastLineProvider
     */
    public function testAdvanceToLastLine(array $expectedRows, Reader $reader) : void
    {
        $reader->advanceTo(5);
        $this->assertSame($expectedRows, $reader->getCurrentRow());
    }

    /**
     * @dataProvider getReadersNoHeadersFirstRow
     * @expectedException \LogicException
     */
    public function testAdvanceToBeforeHeaderLineNoHeadersFirstRow(Reader $reader) : void
    {
        $reader->setHeaderLine(3);
        $reader->advanceTo(1);
    }

    /**
     * @dataProvider getReaders
     * @expectedException \LogicException
     */
    public function testAdvanceToHeaderLine(Reader $reader) : void
    {
        $reader->getRow();
        $reader->advanceTo(0);
    }

    /**
     * @dataProvider getReaders
     * @expectedException \LogicException
     */
    public function testAdvanceToPastEof(Reader $reader) : void
    {
        $reader->advanceTo(999);
    }

    /**
     * @dataProvider getReaders
     */
    public function testSetHeaderLine(Reader $reader) : void
    {
        $headers = $this->headers;

        $this->assertEquals($headers, $reader->getHeaders());

        $reader->setHeaderLine(0);

        $this->assertEquals($headers, $reader->getHeaders());
    }

    /**
     * @dataProvider getReadersNoHeadersFirstRow
     */
    public function testSetHeaderLineNoHeadersFirstRow(Reader $reader) : void
    {
        // set headers
        $reader->setHeaderLine(3);

        $this->assertEquals($this->headers, $reader->getHeaders());

        $rows = $reader->getAll();

        $this->assertCount(5, $rows);
        $this->assertEquals($this->expectedRows, $rows);
    }

    /**
     * @dataProvider getReaders
     */
    public function testGetLastLineNumber(Reader $reader) : void
    {
        $this->assertEquals(5, $reader->getLastLineNumber());
        $this->assertEquals(5, $reader->getLastLineNumber());
    }

    /**
     * @dataProvider getReadersNoHeadersFirstRow
     */
    public function testGetLastLineNumberNoHeadersFirstRow(Reader $reader) : void
    {
        $this->assertEquals(10, $reader->getLastLineNumber());
    }

    /**
     * @return Reader[]
     */
    public function getReaders() : array
    {
        $readerSemiColon = new Reader(__DIR__ . '/read_sc.csv');
        $readerSemiColon->setDelimiter(';');

        return [
            [new Reader(__DIR__ . '/read.csv')],
            [$readerSemiColon],
        ];
    }

    /**
     * @return mixed[]
     */
    public function advanceToLastLineProvider() : array
    {
        $readerSemiColon = new Reader(__DIR__ . '/read_sc.csv');
        $readerSemiColon->setDelimiter(';');

        return [
            [['5column2value', '5column3value', '5column4value'], new Reader(__DIR__ . '/read.csv')],
            [['5column2value', '5column3value', '5column4value'], $readerSemiColon],
        ];
    }

    /**
     * @return mixed[]
     */
    public function getReadersNoHeadersFirstRow() : array
    {
        $readerSemiColon = new Reader(__DIR__ . '/read_header_line_sc.csv', 'r+', false);
        $readerSemiColon->setDelimiter(';');

        return [
            [new Reader(__DIR__ . '/read_header_line.csv', 'r+', false)],
            [$readerSemiColon],
        ];
    }
}
