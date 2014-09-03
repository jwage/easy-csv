<?php

namespace EasyCSV;

class Reader extends AbstractBase
{
    /**
     * @var bool
     */
    private $headersInFirstRow = true;
    /**
     * @var array|bool
     */
    private $headers = false;
    /**
     * @var int
     */
    private $line;

    /**
     * @var
     */
    private $init;

    /**
     * @var bool
     */
    private $headerLine = false;
    /**
     * @var bool
     */
    private $lastLine = false;

    /**
     * @param $path
     * @param string $mode
     * @param bool   $headersInFirstRow
     */
    public function __construct($path, $mode = 'r+', $headersInFirstRow = true)
    {
        parent::__construct($path, $mode);
        $this->headersInFirstRow = $headersInFirstRow;
        $this->line = 0;
    }

    /**
     * @return bool
     */
    public function getHeaders()
    {
        $this->init();

        return $this->headers;
    }

    /**
     * @return array|bool
     */
    public function getRow()
    {
        $this->init();
        if ($this->handle->eof()) {
            return false;
        }

        $row = $this->handle->fgetcsv($this->delimiter, $this->enclosure);
        $isEmpty = $this->rowIsEmpty($row);

        if ($row !== false && $row != null && $isEmpty === false) {
            $this->line++;

            return $this->headers ? array_combine($this->headers, $row) : $row;
        } elseif ($isEmpty) {
            // empty row, transparently try the next row
            return $this->getRow();
        } else {
            return false;
        }
    }

    /**
     * @return array
     */
    public function getAll()
    {
        $data = array();
        while ($row = $this->getRow()) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * @return int zero-based index
     */
    public function getLineNumber()
    {
        return $this->handle->key();
    }

    /**
     * @return int zero-based index
     */
    public function getLastLineNumber()
    {
        if ($this->lastLine !== false) {
            return $this->lastLine;
        }

        $this->handle->seek($this->handle->getSize());
        $lastLine = $this->handle->key();

        $this->handle->rewind();

        return $this->lastLine = $lastLine;
    }

    /**
     * @return array
     */
    public function getCurrentRow()
    {
        return str_getcsv($this->handle->current(), $this->delimiter, $this->enclosure);
    }

    /**
     * @param $lineNumber zero-based index
     */
    public function advanceTo($lineNumber)
    {
        if ($this->headerLine > $lineNumber) {
            throw new \LogicException("Line Number $lineNumber is before the header line that was set");
        } elseif ($this->headerLine === $lineNumber) {
            throw new \LogicException("Line Number $lineNumber is equal to the header line that was set");
        }

        $this->line = $lineNumber;

        $this->handle->seek($lineNumber);
    }

    /**
     * @param $lineNumber zero-based index
     */
    public function setHeaderLine($lineNumber)
    {
        if ($lineNumber !== 0) {
            $this->headersInFirstRow = false;
        } else {
            return false;
        }

        $this->headerLine = $lineNumber;

        // seek to line before headers
        $this->handle->seek($lineNumber);

        // get headers
        $this->headers = $this->getCurrentRow();
    }

    protected function init()
    {
        if (true === $this->init) {
            return;
        }
        $this->init = true;

        if ($this->headersInFirstRow === true) {
            $this->handle->rewind();

            $this->headers = $this->getRow();
        }
    }

    /**
     * @param $row
     * @return bool
     */
    protected function rowIsEmpty($row)
    {
        $emptyRow = ($row === array(null));
        $emptyRowWithDelimiters = (array_filter($row) === array());
        $isEmpty = false;

        if ($emptyRow) {
            $isEmpty = true;

            return $isEmpty;
        } elseif ($emptyRowWithDelimiters) {
            $isEmpty = true;

            return $isEmpty;
        }

        return $isEmpty;
    }
}
