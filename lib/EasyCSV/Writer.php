<?php

namespace EasyCSV;

class Writer extends AbstractBase
{
    public function writeRow($row)
    {
        if (is_string($row)) {
            $row = explode(',', $row);
            $row = array_map('trim', $row);
        }
        return fputcsv($this->_handle, $row, $this->_delimiter, $this->_enclosure);
    }

    public function writeFromArray(array $array)
    {
        foreach ($array as $key => $value) {
            $this->writeRow($value);
        }
    }
}