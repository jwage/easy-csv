<?php

namespace EasyCSV;

abstract class AbstractBase
{
    protected $handle;
    protected $delimiter = ',';
    protected $enclosure = '"';

    public function __construct($path, $mode = 'r+', $isNeedBOM = false)
    {
        if (! file_exists($path)) {
            touch($path);
        }
        $this->handle = new \SplFileObject($path, $mode);
        $this->handle->setFlags(\SplFileObject::DROP_NEW_LINE);

        if ($isNeedBOM) {
            $this->handle->fwrite("\xEF\xBB\xBF");
        }
    }

    public function __destruct()
    {
        $this->handle = null;
    }

    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    public function setEnclosure($enclosure)
    {
        $this->enclosure = $enclosure;
    }
}
