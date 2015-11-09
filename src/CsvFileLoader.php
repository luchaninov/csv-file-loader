<?php

namespace Luchaninov\CsvFileLoader;

class CsvFileLoader implements LoaderInterface
{
    /**
     * @var string
     */
    private $filename = null;
    /**
     * @var null|false|array Null - is not yet set, False - don't use headers, Array - already loaded headers
     */
    private $headers = null;
    /**
     * @var resource
     */
    private $f = null;
    /**
     * @var string
     */
    protected $delimiter = ',';
    /**
     * @var string
     */
    protected $enclosure = '"';

    /**
     * Add numeric key elements for rows that have more elements than headers
     * @var bool
     */
    protected $addUnknownColumns = false;

    public function __construct($filename = null, $headers = null)
    {
        if ($filename !== null) {
            $this->setFilename($filename);
        }

        $this->headers = $headers;
    }

    /**
     * @param string $filename
     * @throws \Exception
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        $this->headers = null;
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

        $headers = $this->headers;
        if ($headers === false) {
            $countHeaders = 0;
        } else {
            if ($headers === null) {
                $cols = fgetcsv($this->f, 0, $this->delimiter, $this->enclosure);
                $headers = $cols;
            }
            $countHeaders = count($headers);
        }

        while ($this->f && !feof($this->f)) {
            $cols = fgetcsv($this->f, 0, $this->delimiter, $this->enclosure);
            if (empty($cols)) {
                continue;
            }
            if ($headers === false) {
                $item = $cols;
            } else {
                $countCols = count($cols);
                if ($countHeaders < $countCols) {
                    $item = ($countHeaders) ? array_combine($headers, array_slice($cols, 0, $countHeaders)) : [];
                    if ($this->addUnknownColumns) {
                        for ($i = $countHeaders; $i < $countCols; $i++) {
                            $item[$i] = $cols[$i];
                        }
                    }
                } elseif ($countHeaders > $countCols) {
                    $item = array_combine(array_slice($headers, 0, $countCols), $cols);
                    for ($i = $countCols; $i < $countHeaders; $i++) {
                        $item[$i] = null;
                    }
                } else {
                    $item = array_combine($headers, $cols);
                }
            }

            yield $item;
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
     * @param array|null $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param string $delimiter
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     * @param string $enclosure
     */
    public function setEnclosure($enclosure)
    {
        $this->enclosure = $enclosure;
    }

    /**
     * @param boolean $addUnknownColumns
     */
    public function setAddUnknownColumns($addUnknownColumns)
    {
        $this->addUnknownColumns = $addUnknownColumns;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function countItems()
    {
        $this->openFile();
        if ($this->headers === null) {
            fgetcsv($this->f, 0, $this->delimiter, $this->enclosure);
        }

        $count = 0;
        while ($this->f && !feof($this->f)) {
            $cols = fgetcsv($this->f, 0, $this->delimiter, $this->enclosure);
            if (empty($cols)) {
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
