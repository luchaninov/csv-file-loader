<?php

namespace Luchaninov\CsvFileLoader;

class TxtFileLoader implements LoaderInterface
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

    private $skipComments = false;

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
        $this->openFile();

        while ($this->f && !feof($this->f)) {
            $s = rtrim(fgets($this->f), "\r\n");
            if ($this->skipEmptyRows && trim($s) === '') {
                continue;
            }
            if ($this->skipComments && substr(ltrim($s), 0, 1) === '#') {
                continue;
            }

            yield $s;
        }

        $this->closeFile();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getItemsArray()
    {
        $result = [];

        foreach ($this->getItems() as $item) {
            $result[] = $item;
        }

        return $result;
    }

    /**
     * @param boolean $skipEmptyRows
     */
    public function setSkipEmptyRows($skipEmptyRows)
    {
        $this->skipEmptyRows = $skipEmptyRows;
    }

    /**
     * @param boolean $skipComments
     */
    public function setSkipComments($skipComments)
    {
        $this->skipComments = $skipComments;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function countItems()
    {
        $this->openFile();

        $count = 0;
        while ($this->f && !feof($this->f)) {
            $s = rtrim(fgets($this->f), "\r\n");
            if ($this->skipEmptyRows && trim($s) === '') {
                continue;
            }
            if ($this->skipComments && substr(ltrim($s), 0, 1) === '#') {
                continue;
            }

            $count++;
        }

        $this->closeFile();

        return $count;
    }

    private function openFile()
    {
        if ($this->filename === null) {
            throw new \Exception('Filename is not set');
        }

        if (!file_exists($this->filename)) {
            throw new \Exception(sprintf('File "%s" is not found', $this->filename));
        }

        $this->f = fopen($this->filename, 'r');
        if ($this->f === false) {
            throw new \Exception('Cannot open file');
        }
    }

    private function closeFile()
    {
        if ($this->f) {
            fclose($this->f);
            $this->f = null;
        }
    }
}
