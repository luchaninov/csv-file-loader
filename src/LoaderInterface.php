<?php

namespace Luchaninov\CsvFileLoader;

interface LoaderInterface
{
    /**
     * @return \Generator
     * @throws \Exception
     */
    public function getItems();
}
