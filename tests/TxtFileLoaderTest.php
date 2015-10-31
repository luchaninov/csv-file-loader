<?php

use Luchaninov\CsvFileLoader\TxtFileLoader;

class TxtFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItems()
    {
        $filename = sys_get_temp_dir() . '/test_CsvFileLoader_' . microtime(true) . '.txt';
        file_put_contents($filename, implode("\n", ['test1', 'test2', '', 'test3', '']));

        $loader = new TxtFileLoader($filename);

        $actual = [];
        foreach ($loader->getItems() as $item) {
            $actual[] = $item;
        }

        $expected = ['test1', 'test2', 'test3'];
        $this->assertEquals($expected, $actual);

        @unlink($filename);
    }

    public function testGetItemsNotSkippingEmptyRows()
    {
        $filename = sys_get_temp_dir() . '/test_CsvFileLoader_' . microtime(true) . '.txt';
        file_put_contents($filename, implode("\n", ['test1', 'test2', '', 'test3', '']));

        $loader = new TxtFileLoader($filename);
        $loader->setSkipEmptyRows(false);

        $actual = [];
        foreach ($loader->getItems() as $item) {
            $actual[] = $item;
        }

        $expected = ['test1', 'test2', '', 'test3', ''];
        $this->assertEquals($expected, $actual);

        @unlink($filename);
    }
}