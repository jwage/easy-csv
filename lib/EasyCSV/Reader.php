<?php

namespace EasyCSV;

class Reader extends AbstractBase
{
	private
		$headers_in_first_row = true;

    private $_headers;
    private $_line;

    public function __construct($path, $mode = 'r+', $headers_in_first_row=true)
    {
        parent::__construct($path, $mode);
		$this->headers_in_first_row = $headers_in_first_row;
        $this->_headers = ($this->headers_in_first_row === true)?$this->getRow():false;
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
}