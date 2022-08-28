<?php

use Luchaninov\CsvFileLoader\CsvFileLoader;
use PHPUnit\Framework\TestCase;

class CsvFileLoaderTest extends TestCase
{
    public function testGetItems(): void
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

    public function testCountItems(): void
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

    public function testGetItemsArray(): void
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

    public function testGetItemsInitWithConstructor(): void
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

    public function testGetItemsInitWithConstructorHeaders(): void
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

    public function testGetItemsInitWithFalseHeaders(): void
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

    public function testGetItemsSwitchFile(): void
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

    public function testGetItemsCustomDelimiters(): void
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

    public function testErrorNotSetFilename(): void
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Filename is not set');

        $loader = new CsvFileLoader();
        foreach ($loader->getItems() as $item) {
            // no need to iterate because throws an exception
        }
    }

    public function testErrorMissingFile(): void
    {
        $filename = 'not_existing';

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('File "not_existing" is not found');

        $loader = new CsvFileLoader();
        $loader->setFilename($filename);
        foreach ($loader->getItems() as $item) {
            // no need to iterate because throws an exception
        }
    }

    public function testAddUnknownColumns(): void
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
