<?php

namespace Luchaninov\CsvFileLoader;

interface LoaderInterface
{
    /**
     * @return \Generator
     * @throws \Exception
     */
    public function getItems();

    /**
     * @return array
     * @throws \Exception
     */
    public function getItemsArray();

    /**
     * @return int
     * @throws \Exception
     */
    public function countItems();
}
