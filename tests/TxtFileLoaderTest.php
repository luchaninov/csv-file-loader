<?php

use Luchaninov\CsvFileLoader\TxtFileLoader;
use PHPUnit\Framework\TestCase;

class TxtFileLoaderTest extends TestCase
{
    public function testGetItems(): void
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

    public function testGetItemsArray(): void
    {
        $filename = sys_get_temp_dir() . '/test_CsvFileLoader_' . microtime(true) . '.txt';
        file_put_contents($filename, implode("\n", ['test1', 'test2', '', 'test3', '']));

        $loader = new TxtFileLoader($filename);

        $actual = $loader->getItemsArray();

        $expected = ['test1', 'test2', 'test3'];
        $this->assertEquals($expected, $actual);

        @unlink($filename);
    }

    public function testCountItems(): void
    {
        $filename = sys_get_temp_dir() . '/test_CsvFileLoader_' . microtime(true) . '.txt';
        file_put_contents($filename, implode("\n", ['test1', 'test2', '', 'test3', '']));

        $loader = new TxtFileLoader($filename);

        $actual = $loader->countItems();

        $expected = 3;
        $this->assertEquals($expected, $actual);

        @unlink($filename);
    }

    public function testSetSkipEmptyRows(): void
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

    public function testSetSkipComments(): void
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

    /**
     * @noinspection PhpStatementHasEmptyBodyInspection
     * @noinspection LoopWhichDoesNotLoopInspection
     * @noinspection MissingOrEmptyGroupStatementInspection
     */
    public function testErrorNotSetFilename(): void
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Filename is not set');

        $loader = new TxtFileLoader();
        foreach ($loader->getItems() as $ignored) {
            // no need to iterate because throws an exception
        }
    }

    /**
     * @noinspection PhpStatementHasEmptyBodyInspection
     * @noinspection LoopWhichDoesNotLoopInspection
     * @noinspection MissingOrEmptyGroupStatementInspection
     */
    public function testErrorMissingFile(): void
    {
        $filename = 'not_existing';

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('File "not_existing" is not found');

        $loader = new TxtFileLoader();
        $loader->setFilename($filename);
        foreach ($loader->getItems() as $ignored) {
            // no need to iterate because throws an exception
        }
    }
}
