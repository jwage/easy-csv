<?php

namespace EasyCSV\Tests;

use EasyCSV\Reader;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    protected $headerValues = array( "column1", "column2", "column3" );
    protected $expectedRows = array (
        0 =>
            array (
                'column1' => '1column2value',
                'column2' => '1column3value',
                'column3' => '1column4value',
            ),
        1 =>
            array (
                'column1' => '2column2value',
                'column2' => '2column3value',
                'column3' => '2column4value',
            ),
        2 =>
            array (
                'column1' => '3column2value',
                'column2' => '3column3value',
                'column3' => '3column4value',
            ),
        3 =>
            array (
                'column1' => '4column2value',
                'column2' => '4column3value',
                'column3' => '4column4value',
            ),
        4 =>
            array (
                'column1' => '5column2value',
                'column2' => '5column3value',
                'column3' => '5column4value',
            ),
    );

    /**
     * @dataProvider getReaders
     */
    public function testOneAtAtime(Reader $reader)
    {
        while ($row = $reader->getRow()) {
            $this->assertTrue(is_array($row));
            $this->assertEquals(3, count($row));
        }
    }

    /**
     * @dataProvider getReaders
     */
    public function testGetAll(Reader $reader)
    {
        $this->assertEquals(5, count($reader->getAll()));
    }

    /**
     * @dataProvider getReaders
     */
    public function testGetHeaders(Reader $reader)
    {
        $this->assertEquals( $this->headerValues, $reader->getHeaders());
    }

    /**
     * @dataProvider getReaders
     */
    public function testAdvanceto(Reader $reader)
    {
        $reader->advanceTo( 3 );

        $this->assertEquals( 3, $reader->getLineNumber() );

        $reader->advanceTo( 0 );

        $row = array
        (
            'column1' => '1column2value',
            'column2' => '1column3value',
            'column3' => '1column4value',
        );

        $actualRow = $reader->getRow();
        $this->assertEquals( $row, $actualRow );

        $reader->advanceTo( 3 );

        $row = array
        (
            'column1' => '4column2value',
            'column2' => '4column3value',
            'column3' => '4column4value',
        );

        $this->assertEquals( $row, $reader->getRow() );
    }

    /**
     * @dataProvider getReadersNoHeadersFirstRow
     */
    public function testAdvanceToNoHeadersFirstRow(Reader $reader)
    {
        $row = array (
            0 => 'Some Meta Data',
            1 => '',
            2 => '',
        );

        $actualRow = $reader->getRow();
        $this->assertEquals( $row, $actualRow );

        // give it the ol' one-two-switcharoo
        $reader->advanceTo(3);
        $reader->getRow();
        $reader->advanceTo(0);

        $this->assertEquals( $row, $reader->getRow() );
    }

    /**
     * @dataProvider getReaders
     */
    public function testSetHeaderLine(Reader $reader)
    {
        $headers = $this->headerValues;

        $this->assertEquals( $headers, $reader->getHeaders() );

        $reader->setHeaderLine(0);

        $this->assertEquals( $headers, $reader->getHeaders() );
    }

    /**
     * @dataProvider getReadersNoHeadersFirstRow
     */
    public function testSetHeaderLineNoHeadersFirstRow(Reader $reader)
    {
        // set headers
        $reader->setHeaderLine( 3 );

        $this->assertEquals( $this->headerValues, $reader->getHeaders() );

        $rows = $reader->getAll();

        $this->assertCount(5, $rows);
        $this->assertEquals($this->expectedRows, $rows);
    }

    /**
     * @dataProvider getReaders
     */
    public function testGetLastLineNumber(Reader $reader)
    {
        $this->assertEquals( 5, $reader->getLastLineNumber() );
    }

    /**
     * @dataProvider getReadersNoHeadersFirstRow
     */
    public function testGetLastLineNumberNoHeadersFirstRow(Reader $reader)
    {
        $this->assertEquals( 10, $reader->getLastLineNumber() );
    }

    public function getReaders()
    {
        $readerSemiColon = new Reader(__DIR__ . '/read_sc.csv');
        $readerSemiColon->setDelimiter(';');

        return array(
            array(new Reader(__DIR__ . '/read.csv')),
            array($readerSemiColon),
        );
    }

    public function getReadersNoHeadersFirstRow()
    {
        $readerSemiColon = new Reader(__DIR__ . '/read_header_line_sc.csv', 'r+', false );
        $readerSemiColon->setDelimiter(';');

        return array(
            array(new Reader(__DIR__ . '/read_header_line.csv', 'r+', false )),
            array($readerSemiColon),
        );
    }
}
