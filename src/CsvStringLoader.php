<?php

/** @noinspection DuplicatedCode */

namespace Luchaninov\CsvFileLoader;

use Generator;
use RuntimeException;

class CsvStringLoader implements LoaderInterface
{
    private null|false|array $headers; // Null - is not yet set, False - don't use headers, Array - already loaded headers

    private string $originalData;

    protected string $delimiter = ',';

    protected string $enclosure = '"';
    protected bool $addUnknownColumns = false; // Add numeric key elements for rows that have more elements than headers

    public function __construct(string $data, array|bool $headers = null)
    {
        $this->originalData = $data;
        $this->headers = $headers;
    }

    public function getItems(): Generator
    {
        $delimiter = ($this->delimiter === 'auto') ? DelimiterDetector::detect(mb_substr($this->originalData, 0, 10000)) : $this->delimiter;

        $rows = str_getcsv($this->originalData, "\n", $this->enclosure);

        $startRowIndex = 0;
        $headers = $this->headers;
        if ($headers === false) {
            $countHeaders = 0;
        } else {
            if ($headers === null) {
                $cols = str_getcsv($rows[$startRowIndex], $delimiter, $this->enclosure);
                $headers = $cols;
                $startRowIndex++;
            }
            $countHeaders = count($headers);
        }

        for ($rowIndex = $startRowIndex, $maxRowIndex = count($rows) - 1; $rowIndex <= $maxRowIndex; $rowIndex++) {
            $row = $rows[$rowIndex];

            if (trim($row) === '') {
                continue;
            }

            $cols = str_getcsv($row, $delimiter, $this->enclosure);
            if (!$cols) {
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

    public function setAddUnknownColumns(bool $addUnknownColumns): self
    {
        $this->addUnknownColumns = $addUnknownColumns;

        return $this;
    }

    public function countItems(): int
    {
        return count($this->getItemsArray());
    }
}
