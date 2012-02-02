<?php

namespace EasyCSV;

class Reader extends AbstractBase
{
    private $_headers;
    private $_line;
    private $_skipBlanks;

    public function __construct($path, $mode = 'r+', $skipBlanks = true)
    {
        parent::__construct($path, $mode);
        $this->_skipBlanks = $skipBlanks;
        $this->_headers    = $this->getRow();
        $this->_line       = 0;
    }

    public function getRow()
    {
        if (($row = fgetcsv($this->_handle, 1000, $this->_delimiter, $this->_enclosure)) !== false) {
            if (true === $this->_skipBlanks) {
                while ((1 == count($row)) && (null == $row[0]))
                {
                    $this->_line++;

                    if (($row = fgetcsv($this->_handle, 1000, $this->_delimiter, $this->_enclosure)) === false) {
                        return false;
                    }
                }
            }

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
}