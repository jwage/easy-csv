<?php

declare(strict_types=1);

namespace EasyCSV;

use SplFileObject;
use const PHP_EOL;
use function file_exists;
use function implode;
use function strlen;
use function touch;

abstract class AbstractBase
{
    /** @var string */
    protected $path;

    /** @var string */
    protected $mode;

    /** @var bool */
    protected $isNeedBOM = false;

    /** @var string[] */
    protected $initialHeaders = [];

    /** @var SplFileObject|null */
    protected $handle;

    /** @var string */
    protected $delimiter = ',';

    /** @var string */
    protected $enclosure = '"';

    /**
     * @param string[] $initialHeaders
     */
    public function __construct(
        string $path,
        string $mode = 'r+',
        bool $isNeedBOM = false,
        array $initialHeaders = []
    ) {
        $this->path           = $path;
        $this->mode           = $mode;
        $this->isNeedBOM      = $isNeedBOM;
        $this->initialHeaders = $initialHeaders;
        $this->handle         = $this->initializeHandle();
    }

    public function __destruct()
    {
        $this->handle = null;
    }

    public function setDelimiter(string $delimiter) : void
    {
        $this->delimiter = $delimiter;
    }

    public function setEnclosure(string $enclosure) : void
    {
        $this->enclosure = $enclosure;
    }

    protected function getHandle() : SplFileObject
    {
        if ($this->handle === null) {
            $this->handle = $this->initializeHandle();
        }

        return $this->handle;
    }

    private function initializeHandle() : SplFileObject
    {
        $isNewFile = false;

        if (! file_exists($this->path)) {
            touch($this->path);

            $isNewFile = true;
        }

        $handle = new SplFileObject($this->path, $this->mode);
        $handle->setFlags(SplFileObject::DROP_NEW_LINE);

        if ($this->isNeedBOM) {
            $handle->fwrite("\xEF\xBB\xBF");
        }

        if ($isNewFile && $this->initialHeaders !== []) {
            $headerLine = implode(',', $this->initialHeaders) . PHP_EOL;

            $handle->fwrite($headerLine, strlen($headerLine));
        }

        return $handle;
    }
}
