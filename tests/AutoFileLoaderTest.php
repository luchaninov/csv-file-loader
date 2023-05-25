<?php

use Luchaninov\CsvFileLoader\AutoFileLoader;
use PHPUnit\Framework\TestCase;

class AutoFileLoaderTest extends TestCase
{
    public function testGetItems(): void
    {
        $filename = sys_get_temp_dir() . '/test_AutoFileLoader_' . microtime(true) . '.txt';
        file_put_contents($filename, implode("\n", [
                implode("\t", ['key1', 'key2']),
                implode("\t", ['r1_1', 'r1_2']),
                implode("\t", ['r2_1', 'r2_2']),
            ]) . "\n");

        $loader = new AutoFileLoader();
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
}
