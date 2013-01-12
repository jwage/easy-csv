<?php

namespace EasyCSV;

class Reader extends AbstractBase
{
    private $headersInFirstRow = true;
    private $headers;
    private $line;

    public function __construct($path, $mode = 'r+', $headersInFirstRow = true)
    {
        parent::__construct($path, $mode);
        $this->headersInFirstRow = $headersInFirstRow;
        $this->headers = $this->headersInFirstRow === true ? $this->getRow() : false;
        $this->line    = 0;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getRow()
    {
        if (($row = fgetcsv($this->handle, 1000, $this->delimiter, $this->enclosure)) !== false) {
            $this->line++;
            return $this->headers ? array_combine($this->headers, $row) : $row;
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
        return $this->line;
    }
}
