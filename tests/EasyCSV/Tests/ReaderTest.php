<?php

namespace EasyCSV\Tests;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    private $_reader;

    public function setUp()
    {
        $this->_reader = new \EasyCSV\Reader(__DIR__ . '/read.csv');
    }

    public function testOneAtAtime()
    {
        while($row = $this->_reader->getRow()) {
            $this->assertTrue(is_array($row));
            $this->assertEquals(3, count($row));
        }
    }

    public function testGetAll()
    {
        $this->assertEquals(5, count($this->_reader->getAll()));
    }
}
