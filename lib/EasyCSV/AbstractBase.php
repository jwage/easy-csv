<?php

namespace EasyCSV;

abstract class AbstractBase
{
    protected $handle;
    protected $delimiter = ',';
    protected $enclosure = '"';

    public function __construct($path, $mode = 'r+', $isNeedBOM = false, $headers = array())
    {
        $isNewFile = false;

        if (! file_exists($path)) {
            touch($path);
            $isNewFile = true;
        }
        $this->handle = new \SplFileObject($path, $mode);
        $this->handle->setFlags(\SplFileObject::DROP_NEW_LINE);

        if ($isNeedBOM) {
            $this->handle->fwrite("\xEF\xBB\xBF");
        }

        if($isNewFile && isset($headers)) {
            $headerLine = join(',', $headers) . PHP_EOL;
            $this->handle->fwrite($headerLine, strlen($headerLine));
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
