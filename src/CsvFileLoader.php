<?php

namespace Luchaninov\CsvFileLoader;

class CsvFileLoader
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
     * @param string $filename
     * @throws \Exception
     */
    public function setFilename($filename)
    {
        if (!file_exists($filename)) {
            throw new \Exception(sprintf('File "%s" is not found', $filename));
        }

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
        if ($this->filename === null) {
            throw new \Exception('Filename is not set');
        }

        $this->f = fopen($this->filename, 'r');
        if ($this->f === false) {
            throw new \Exception('Cannot open file');
        }

        if ($this->headers === null) {
            $cols = fgetcsv($this->f, 0, $this->delimiter, $this->enclosure);
            $this->headers = $cols;
            $countHeaders = count($this->headers);
        } else {
            $countHeaders = 0;
        }

        while ($this->f && !feof($this->f)) {
            $cols = fgetcsv($this->f, 0, $this->delimiter, $this->enclosure);
            if (empty($cols)) {
                continue;
            }
            if ($this->headers === false) {
                $item = $cols;
            } else {
                $countCols = count($cols);
                if ($countHeaders < $countCols) {
                    $item = ($countHeaders) ? array_combine($this->headers, array_slice($cols, 0, $countHeaders)) : [];
                    for ($i = $countHeaders; $i < $countCols; $i++) {
                        $item[$i] = $cols[$i];
                    }
                } elseif ($countHeaders > $countCols) {
                    $item = array_combine(array_slice($this->headers, 0, $countCols), $cols);
                    for ($i = $countCols; $i < $countHeaders; $i++) {
                        $item[$i] = null;
                    }
                } else {
                    $item = array_combine($this->headers, $cols);
                }
            }

            yield $item;
        }

        if ($this->f) {
            fclose($this->f);
            $this->f = null;
        }
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
}