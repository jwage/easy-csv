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
     * @var
     */
    private $init;

    /**
     * @var bool|int
     */
    private $headerLine = false;

    /**
     * @var bool|int
     */
    private $lastLine = false;

    /**
     * @var bool|int
     */
    private $isNeedBOMRemove = true;

    /**
     * @param $path
     * @param string $mode
     * @param bool   $headersInFirstRow
     */
    public function __construct($path, $mode = 'r+', $headersInFirstRow = true)
    {
        parent::__construct($path, $mode);
        $this->headersInFirstRow = $headersInFirstRow;
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
        if ($this->isEof()) {
            return false;
        }

        $row = $this->getCurrentRow();
        $isEmpty = $this->rowIsEmpty($row);

        if ($this->isEof() === false) {
            $this->handle->next();
        }

        if ($isEmpty === false) {
            return ($this->headers && is_array($this->headers)) ? array_combine($this->headers, $row) : $row;
        } elseif ($isEmpty) {
            // empty row, transparently try the next row
            return $this->getRow();
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isEof()
    {
        return $this->handle->eof();
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
        $current = $this->handle->current();
        if ($this->isNeedBOMRemove && mb_strpos($current, "\xEF\xBB\xBF", 0, 'utf-8') === 0) {
            $this->isNeedBOMRemove = false;

            $current = str_replace("\xEF\xBB\xBF", '', $current);
        }

        return str_getcsv($current, $this->delimiter, $this->enclosure);
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

        if ($lineNumber > 0) {
            $this->handle->seek($lineNumber - 1);
        } // check the line before

        if ($this->isEof()) {
            throw new \LogicException("Line Number $lineNumber is past the end of the file");
        }

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

        $this->handle->seek($lineNumber);

        // get headers
        $this->headers = $this->getRow();
    }

    protected function init()
    {
        if (true === $this->init) {
            return;
        }
        $this->init = true;

        if ($this->headersInFirstRow === true) {
            $this->handle->rewind();

            $this->headerLine = 0;

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
