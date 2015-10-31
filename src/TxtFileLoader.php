<?php

namespace Luchaninov\CsvFileLoader;

class TxtFileLoader
{
    /**
     * @var string
     */
    private $filename = null;

    /**
     * @var resource
     */
    private $f = null;

    private $skipEmptyRows = true;

    public function __construct($filename = null)
    {
        if ($filename !== null) {
            $this->setFilename($filename);
        }
    }

    /**
     * @param string $filename
     * @throws \Exception
     */
    public function setFilename($filename)
    {
        if (!file_exists($filename)) {
            throw new \Exception(sprintf('File "%s" is not found', $filename));
        }

        $this->filename = $filename;
        if ($this->f) {
            fclose($this->f);
            $this->f = null;
        }
    }

    /**
     * @return \Generator
     * @throws \Exception
     */
    public function getItems()
    {
        if ($this->filename === null) {
            throw new \Exception('Filename is not set');
        }

        $this->f = fopen($this->filename, 'r');
        if ($this->f === false) {
            throw new \Exception('Cannot open file');
        }

        while ($this->f && !feof($this->f)) {
            $s = rtrim(fgets($this->f), "\r\n");
            if ($this->skipEmptyRows && trim($s) === '') {
                continue;
            }

            yield $s;
        }

        if ($this->f) {
            fclose($this->f);
            $this->f = null;
        }
    }

    /**
     * @param boolean $skipEmptyRows
     */
    public function setSkipEmptyRows($skipEmptyRows)
    {
        $this->skipEmptyRows = $skipEmptyRows;
    }
}
