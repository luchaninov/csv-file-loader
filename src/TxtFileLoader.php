<?php

namespace Luchaninov\CsvFileLoader;

use Generator;
use RuntimeException;

class TxtFileLoader implements LoaderInterface
{
    private ?string $filename = null;

    /** @var resource|null */
    private $f = null;

    private bool $skipEmptyRows = true;

    private bool $skipComments = false;

    public function __construct($filename = null)
    {
        if ($filename !== null) {
            $this->setFilename($filename);
        }
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;
        if ($this->f) {
            fclose($this->f);
            $this->f = null;
        }

        return $this;
    }

    public function getItems(): Generator
    {
        $this->openFile();

        while ($this->f && !feof($this->f)) {
            $s = rtrim((string)fgets($this->f), "\r\n");
            if ($this->skipEmptyRows && trim($s) === '') {
                continue;
            }
            if ($this->skipComments && str_starts_with(ltrim($s), '#')) {
                continue;
            }

            yield $s;
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

    public function setSkipEmptyRows(bool $skipEmptyRows): self
    {
        $this->skipEmptyRows = $skipEmptyRows;

        return $this;
    }

    public function setSkipComments(bool $skipComments): self
    {
        $this->skipComments = $skipComments;

        return $this;
    }

    public function countItems(): int
    {
        $this->openFile();

        $count = 0;
        while ($this->f && !feof($this->f)) {
            $s = rtrim((string)fgets($this->f), "\r\n");
            if ($this->skipEmptyRows && trim($s) === '') {
                continue;
            }
            if ($this->skipComments && str_starts_with(ltrim($s), '#')) {
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
}
