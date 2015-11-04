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

    public function testGetItemsArray()
    {
        $filename = sys_get_temp_dir() . '/test_CsvFileLoader_' . microtime(true) . '.txt';
        file_put_contents($filename, implode("\n", ['test1', 'test2', '', 'test3', '']));

        $loader = new TxtFileLoader($filename);

        $actual = $loader->getItemsArray();

        $expected = ['test1', 'test2', 'test3'];
        $this->assertEquals($expected, $actual);

        @unlink($filename);
    }

    public function testCountItems()
    {
        $filename = sys_get_temp_dir() . '/test_CsvFileLoader_' . microtime(true) . '.txt';
        file_put_contents($filename, implode("\n", ['test1', 'test2', '', 'test3', '']));

        $loader = new TxtFileLoader($filename);

        $actual = $loader->countItems();

        $expected = 3;
        $this->assertEquals($expected, $actual);

        @unlink($filename);
    }

    public function testSetSkipEmptyRows()
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

    public function testSetSkipComments()
    {
        $filename = sys_get_temp_dir() . '/test_CsvFileLoader_' . microtime(true) . '.txt';
        file_put_contents($filename, implode("\n", ['test1', 'test2', '# comment', 'test3', '  # comment with whitespaces']));

        $loader = new TxtFileLoader($filename);
        $loader->setSkipComments(true);

        $actual = [];
        foreach ($loader->getItems() as $item) {
            $actual[] = $item;
        }

        $expected = ['test1', 'test2', 'test3'];
        $this->assertEquals($expected, $actual);

        @unlink($filename);
    }

    public function testErrorNotSetFilename()
    {
        $this->setExpectedException('Exception', 'Filename is not set');

        $loader = new TxtFileLoader();
        foreach ($loader->getItems() as $item) {
            // no need to iterate because throws an exception
        }
    }

    public function testErrorMissingFile()
    {
        $filename = 'not_existing';

        $this->setExpectedException('Exception', 'File "not_existing" is not found');

        $loader = new TxtFileLoader();
        $loader->setFilename($filename);
        foreach ($loader->getItems() as $item) {
            // no need to iterate because throws an exception
        }
    }
}
