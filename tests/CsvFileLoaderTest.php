<?php

use Luchaninov\CsvFileLoader\CsvFileLoader;

class CsvFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItems()
    {
        $filename = sys_get_temp_dir() . '/test_CsvFileLoader_' . microtime(true) . '.txt';
        file_put_contents($filename, implode("\n", [
            implode(',', ['key1', 'key2']),
            implode(',', ['r1_1', 'r1_2']),
            implode(',', ['r2_1', 'r2_2']),
        ]) . "\n");

        $loader = new CsvFileLoader();
        $loader->setFilename($filename);

        $actual = [];
        foreach ($loader->getItems() as $item) {
            $actual[] = $item;
        }

        $expected = [
            ['key1' => 'r1_1', 'key2' => 'r1_2'],
            ['key1' => 'r2_1', 'key2' => 'r2_2'],
        ];
        $this->assertEquals($expected, $actual);

        @unlink($filename);
    }

    public function testCountItems()
    {
        $filename = sys_get_temp_dir() . '/test_CsvFileLoader_' . microtime(true) . '.txt';
        file_put_contents($filename, implode("\n", [
            implode(',', ['key1', 'key2']),
            implode(',', ['r1_1', 'r1_2']),
            implode(',', ['r2_1', 'r2_2']),
        ]) . "\n");

        $loader = new CsvFileLoader();
        $loader->setFilename($filename);

        $actual = $loader->countItems();
        $expected = 2;
        $this->assertEquals($expected, $actual);

        @unlink($filename);
    }

    public function testGetItemsArray()
    {
        $filename = sys_get_temp_dir() . '/test_CsvFileLoader_' . microtime(true) . '.txt';
        file_put_contents($filename, implode("\n", [
            implode(',', ['key1', 'key2']),
            implode(',', ['r1_1', 'r1_2']),
            implode(',', ['r2_1', 'r2_2']),
        ]) . "\n");

        $loader = new CsvFileLoader();
        $loader->setFilename($filename);

        $actual = $loader->getItemsArray();

        $expected = [
            ['key1' => 'r1_1', 'key2' => 'r1_2'],
            ['key1' => 'r2_1', 'key2' => 'r2_2'],
        ];
        $this->assertEquals($expected, $actual);

        @unlink($filename);
    }

    public function testGetItemsInitWithConstructor()
    {
        $filename = sys_get_temp_dir() . '/test_CsvFileLoader_' . microtime(true) . '.txt';
        file_put_contents($filename, implode("\n", [
            implode(',', ['key1', 'key2']),
            implode(',', ['r1_1', 'r1_2']),
            implode(',', ['r2_1', 'r2_2']),
        ]) . "\n");

        $loader = new CsvFileLoader($filename);

        $actual = [];
        foreach ($loader->getItems() as $item) {
            $actual[] = $item;
        }

        $expected = [
            ['key1' => 'r1_1', 'key2' => 'r1_2'],
            ['key1' => 'r2_1', 'key2' => 'r2_2'],
        ];
        $this->assertEquals($expected, $actual);

        @unlink($filename);
    }

    public function testGetItemsInitWithConstructorHeaders()
    {
        $filename = sys_get_temp_dir() . '/test_CsvFileLoader_' . microtime(true) . '.txt';
        file_put_contents($filename, implode("\n", [
            implode(',', ['r1_1', 'r1_2']),
            implode(',', ['r2_1', 'r2_2']),
        ]) . "\n");

        $loader = new CsvFileLoader($filename, ['key1', 'key2']);

        $actual = [];
        foreach ($loader->getItems() as $item) {
            $actual[] = $item;
        }

        $expected = [
            ['key1' => 'r1_1', 'key2' => 'r1_2'],
            ['key1' => 'r2_1', 'key2' => 'r2_2'],
        ];
        $this->assertEquals($expected, $actual);

        @unlink($filename);
    }

    public function testGetItemsInitWithFalseHeaders()
    {
        $filename = sys_get_temp_dir() . '/test_CsvFileLoader_' . microtime(true) . '.txt';
        file_put_contents($filename, implode("\n", [
            implode(',', ['r1_1', 'r1_2']),
            implode(',', ['r2_1', 'r2_2']),
        ]) . "\n");

        $loader = new CsvFileLoader($filename, false);

        $actual = [];
        foreach ($loader->getItems() as $item) {
            $actual[] = $item;
        }

        $expected = [
            [0 => 'r1_1', 1 => 'r1_2'],
            [0 => 'r2_1', 1 => 'r2_2'],
        ];
        $this->assertEquals($expected, $actual);

        @unlink($filename);
    }

    public function testGetItemsSwitchFile()
    {
        $filename = sys_get_temp_dir() . '/test_CsvFileLoader_1_' . microtime(true) . '.txt';
        file_put_contents($filename, implode("\n", [
            implode(',', ['key1', 'key2']),
            implode(',', ['r1_1', 'r1_2']),
            implode(',', ['r2_1', 'r2_2']),
        ]) . "\n");
        $filename2 = sys_get_temp_dir() . '/test_CsvFileLoader_2_' . microtime(true) . '.txt';
        file_put_contents($filename2, implode("\n", [
            implode(',', ['key1', 'key2']),
            implode(',', ['r1_1', 'r1_2']),
            implode(',', ['r2_1', 'r2_2']),
        ]) . "\n");

        $loader = new CsvFileLoader();
        $loader->setFilename($filename);

        $actual = [];
        foreach ($loader->getItems() as $item) {
            $actual[] = $item;
            $loader->setFilename($filename2); // it should stop reading current file
        }
        foreach ($loader->getItems() as $item) {
            $actual[] = $item;
        }

        $expected = [
            ['key1' => 'r1_1', 'key2' => 'r1_2'],
            ['key1' => 'r1_1', 'key2' => 'r1_2'],
            ['key1' => 'r2_1', 'key2' => 'r2_2'],
        ];
        $this->assertEquals($expected, $actual);

        @unlink($filename);
        @unlink($filename2);
    }

    public function testGetItemsCustomDelimiters()
    {
        $filename = sys_get_temp_dir() . '/test_CsvFileLoader_' . microtime(true) . '.txt';
        file_put_contents($filename, implode("\r\n", [
            implode(",", ['key1', 'key2']),
            implode(",", ["~r1_,\n1~", '~r1_2~']),
            implode(",", ['r2_1', '~r2_2~']),
        ]) . "\r\n");

        $loader = new CsvFileLoader();
        $loader->setFilename($filename);
        $loader->setDelimiter(',');
        $loader->setEnclosure('~');

        $actual = [];
        foreach ($loader->getItems() as $item) {
            $actual[] = $item;
        }

        $expected = [
            ['key1' => "r1_,\n1", 'key2' => 'r1_2'],
            ['key1' => 'r2_1', 'key2' => 'r2_2'],
        ];
        $this->assertEquals($expected, $actual);

        @unlink($filename);
    }

    public function testErrorNotSetFilename()
    {
        $this->setExpectedException('Exception', 'Filename is not set');

        $loader = new CsvFileLoader();
        foreach ($loader->getItems() as $item) {
            // no need to iterate because throws an exception
        }
    }

    public function testErrorMissingFile()
    {
        $filename = 'not_existing';

        $this->setExpectedException('Exception', 'File "not_existing" is not found');

        $loader = new CsvFileLoader();
        $loader->setFilename($filename);
        foreach ($loader->getItems() as $item) {
            // no need to iterate because throws an exception
        }
    }

    public function testAddUnknownColumns()
    {
        $filename = sys_get_temp_dir() . '/test_CsvFileLoader_' . microtime(true) . '.txt';
        file_put_contents($filename, implode("\n", [
            implode(',', ['key1', 'key2']),
            implode(',', ['r1_1', 'r1_2', 'r1_3']),
            implode(',', ['r2_1', 'r2_2']),
        ]) . "\n");

        $loader = new CsvFileLoader($filename);

        // first try default behavior
        $actual = $loader->getItemsArray();
        $expected = [
            ['key1' => 'r1_1', 'key2' => 'r1_2'],
            ['key1' => 'r2_1', 'key2' => 'r2_2'],
        ];
        $this->assertEquals($expected, $actual);

        // and now try get all columns including unknown
        $loader->setAddUnknownColumns(true);
        $actual = $loader->getItemsArray();
        $expected = [
            ['key1' => 'r1_1', 'key2' => 'r1_2', 2 => 'r1_3'],
            ['key1' => 'r2_1', 'key2' => 'r2_2'],
        ];
        $this->assertEquals($expected, $actual);

        @unlink($filename);
    }
}
