<?php

namespace Luchaninov\CsvFileLoader;

use Generator;
use RuntimeException;

class CsvFileLoader implements LoaderInterface
{
    private ?string $filename = null;

    private null|false|array $headers; // Null - is not yet set, False - don't use headers, Array - already loaded headers

    /** @var resource|null */
    private $f = null;

    protected string $delimiter = ',';
    protected string $enclosure = '"';
    protected string $escape = '\\';

    protected bool $addUnknownColumns = false; // Add numeric key elements for rows that have more elements than headers

    public function __construct(
        string|null $filename = null,
        array|bool|null $headers = null,
    ) {
        if ($filename !== null) {
            $this->setFilename($filename);
        }

        $this->headers = $headers;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;
        $this->headers = null;
        if ($this->f) {
            fclose($this->f);
            $this->f = null;
        }

        return $this;
    }

    public function getItems(): Generator
    {
        $this->openFile();
        $delimiter = null;

        $headers = $this->headers;
        if ($headers === false) {
            $countHeaders = 0;
        } else {
            if ($headers === null) {
                if ($delimiter === null) {
                    $delimiter = $this->detectDelimiter();
                }

                $cols = fgetcsv($this->f, 0, $delimiter, $this->enclosure, $this->escape);
                $headers = $cols;
            }
            $countHeaders = count($headers);
        }

        while ($this->f && !feof($this->f)) {
            if ($delimiter === null) {
                $delimiter = $this->detectDelimiter();
            }

            $cols = fgetcsv($this->f, 0, $delimiter, $this->enclosure, $this->escape);
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

    public function getItemsArray(): array
    {
        $result = [];

        foreach ($this->getItems() as $item) {
            $result[] = $item;
        }

        return $result;
    }

    public function setHeaders(?array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function setDelimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    public function setEnclosure(string $enclosure): self
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    public function setEscape(string $escape): self
    {
        $this->escape = $escape;

        return $this;
    }

    public function setAddUnknownColumns(bool $addUnknownColumns): self
    {
        $this->addUnknownColumns = $addUnknownColumns;

        return $this;
    }

    public function countItems(): int
    {
        $this->openFile();
        if ($this->headers === null) {
            fgetcsv($this->f, 0, $this->delimiter, $this->enclosure, $this->escape);
        }

        $count = 0;
        while ($this->f && !feof($this->f)) {
            $cols = fgetcsv($this->f, 0, $this->delimiter, $this->enclosure, $this->escape);
            if (empty($cols)) {
                continue;
            }

            $count++;
        }

        $this->closeFile();

        return $count;
    }

    private function openFile(): void
    {
        if ($this->filename === null) {
            throw new RuntimeException('Filename is not set');
        }

        if (!file_exists($this->filename)) {
            throw new RuntimeException(sprintf('File "%s" is not found', $this->filename));
        }

        $this->f = fopen($this->filename, 'rb');
        if ($this->f === false) {
            throw new RuntimeException('Cannot open file');
        }
    }

    private function closeFile(): void
    {
        if ($this->f) {
            fclose($this->f);
            $this->f = null;
        }
    }

    private function detectDelimiter(): string
    {
        $pos = ftell($this->f);
        $s = fgets($this->f, 1000);
        $delimiter = DelimiterDetector::detect($s);
        fseek($this->f, $pos); // return back to original position in file

        return $delimiter;
    }
}
