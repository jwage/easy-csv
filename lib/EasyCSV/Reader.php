<?php

namespace EasyCSV;

class Reader extends AbstractBase
{
    private $_headers;

    public function __construct($path, $mode = 'r+')
    {
        parent::__construct($path, $mode);
        $this->_headers = $this->getRow();
    }

    public function getRow()
    {
        if (($row = fgetcsv($this->_handle, 1000, $this->_delimiter, $this->_enclosure)) !== false) {
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
}