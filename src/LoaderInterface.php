<?php

namespace Luchaninov\CsvFileLoader;

use Generator;

interface LoaderInterface
{
    public function getItems(): Generator;

    public function getItemsArray(): array;

    public function countItems(): int;
}
