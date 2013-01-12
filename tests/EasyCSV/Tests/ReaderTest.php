<?php

namespace EasyCSV\Tests;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    private $reader;

    public function setUp()
    {
        $this->reader = new \EasyCSV\Reader(__DIR__ . '/read.csv');
    }

    public function testOneAtAtime()
    {
        while($row = $this->reader->getRow()) {
            $this->assertTrue(is_array($row));
            $this->assertEquals(3, count($row));
        }
    }

    public function testGetAll()
    {
        $this->assertEquals(5, count($this->reader->getAll()));
    }

    public function testGetHeaders()
    {
        $this->assertEquals(array("column1", "column2", "column3"), $this->reader->getHeaders());
    }
}
