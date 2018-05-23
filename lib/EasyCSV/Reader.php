<?php

declare(strict_types=1);

namespace EasyCSV;

use LogicException;
use function array_combine;
use function array_filter;
use function is_array;
use function mb_strpos;
use function sprintf;
use function str_getcsv;
use function str_replace;

class Reader extends AbstractBase
{
    /** @var bool */
    private $headersInFirstRow = true;

    /** @var string[]|bool */
    private $headers = false;

    /** @var bool */
    private $init;

    /** @var bool|int */
    private $headerLine = false;

    /** @var bool|int */
    private $lastLine = false;

    /** @var bool */
    private $isNeedBOMRemove = true;

    public function __construct(string $path, string $mode = 'r+', bool $headersInFirstRow = true)
    {
        parent::__construct($path, $mode);

        $this->headersInFirstRow = $headersInFirstRow;
    }

    /**
     * @return string[]|bool
     */
    public function getHeaders()
    {
        $this->init();

        return $this->headers;
    }

    /**
     * @return string[]|bool
     */
    public function getRow()
    {
        $this->init();

        if ($this->isEof()) {
            return false;
        }

        $row     = $this->getCurrentRow();
        $isEmpty = $this->rowIsEmpty($row);

        if ($this->isEof() === false) {
            $this->getHandle()->next();
        }

        if ($isEmpty === false) {
            return ($this->headers !== false && is_array($this->headers)) ? array_combine($this->headers, $row) : $row;
        }

        if ($isEmpty) {
            // empty row, transparently try the next row
            return $this->getRow();
        }

        return false;
    }

    public function isEof() : bool
    {
        return $this->getHandle()->eof();
    }

    /**
     * @return string[][]
     */
    public function getAll() : array
    {
        $data = [];
        while ($row = $this->getRow()) {
            $data[] = $row;
        }

        return $data;
    }

    public function getLineNumber() : int
    {
        return $this->getHandle()->key();
    }

    /**
     * @return int|bool
     */
    public function getLastLineNumber()
    {
        if ($this->lastLine !== false) {
            return $this->lastLine;
        }

        $this->getHandle()->seek($this->getHandle()->getSize());
        $lastLine = $this->getHandle()->key();

        $this->getHandle()->rewind();

        return $this->lastLine = $lastLine;
    }

    /**
     * @return string[]
     */
    public function getCurrentRow() : array
    {
        $current = $this->getHandle()->current();

        if ($this->isNeedBOMRemove && mb_strpos($current, "\xEF\xBB\xBF", 0, 'utf-8') === 0) {
            $this->isNeedBOMRemove = false;

            $current = str_replace("\xEF\xBB\xBF", '', $current);
        }

        return str_getcsv($current, $this->delimiter, $this->enclosure);
    }

    public function advanceTo(int $lineNumber) : void
    {
        if ($this->headerLine > $lineNumber) {
            throw new LogicException(sprintf(
                'Line Number %s is before the header line that was set',
                $lineNumber
            ));
        } elseif ($this->headerLine === $lineNumber) {
            throw new LogicException(sprintf(
                'Line Number %s is equal to the header line that was set',
                $lineNumber
            ));
        }

        if ($lineNumber > 0) {
            $this->getHandle()->seek($lineNumber - 1);
        } // check the line before

        if ($this->isEof()) {
            throw new LogicException(sprintf(
                'Line Number %s is past the end of the file',
                $lineNumber
            ));
        }

        $this->getHandle()->seek($lineNumber);
    }

    public function setHeaderLine(int $lineNumber) : bool
    {
        if ($lineNumber === 0) {
            return false;
        }

        $this->headersInFirstRow = false;

        $this->headerLine = $lineNumber;

        $this->getHandle()->seek($lineNumber);

        // get headers
        $this->headers = $this->getRow();

        return true;
    }

    protected function init() : void
    {
        if ($this->init === true) {
            return;
        }

        $this->init = true;

        if ($this->headersInFirstRow !== true) {
            return;
        }

        $this->getHandle()->rewind();

        $this->headerLine = 0;

        $this->headers = $this->getRow();
    }

    /**
     * @param string[]|null[] $row
     */
    protected function rowIsEmpty(array $row) : bool
    {
        $emptyRow               = ($row === [null]);
        $emptyRowWithDelimiters = (array_filter($row) === []);
        $isEmpty                = false;

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
