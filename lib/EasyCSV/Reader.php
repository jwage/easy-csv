<?php

namespace EasyCSV;

class Reader extends AbstractBase
{
    private $_headersInFirstRow = true;

    private $_headers;
    private $_line;

    public function __construct($path, $mode = 'r+', $headersInFirstRow = true)
    {
        parent::__construct($path, $mode);
        $this->_headersInFirstRow = $headersInFirstRow;
        $this->_headers = $this->_headersInFirstRow === true ? $this->getRow() : false;
        $this->_line    = 0;
    }

    public function getRow()
    {
        if (($row = fgetcsv($this->_handle, 1000, $this->_delimiter, $this->_enclosure)) !== false) {
            $this->_line++;
            return $this->_headers ? array_combine($this->_headers, $row) : $row;
        } else {
            return false;
        }
    }

    public function getAll()
    {
        $data = array();
        while ($row = $this->getRow()) {
            $data[] = $row;
        }
        return $data;
    }

    public function getLineNumber()
    {
        return $this->_line;
    }

    public function setDelimiter($delimiter)
    {
        $this->_delimiter = $delimiter;
    }

    public function setEnclosure($enclosure)
    {
        $this->_enclosure = $enclosure;
    }
}
