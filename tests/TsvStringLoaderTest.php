<?php

use Luchaninov\CsvFileLoader\TsvStringLoader;
use PHPUnit\Framework\TestCase;

class TsvStringLoaderTest extends TestCase
{
    public function testGetItems(): void
    {
        $s = implode("\n", [
            implode("\t", ['key1', 'key2']),
            implode("\t", ['r1_1', 'r1_2']),
            implode("\t", ['r2_1', 'r2_2']),
        ]) . "\n";

        $loader = new TsvStringLoader($s);

        $actual = [];
        foreach ($loader->getItems() as $item) {
            $actual[] = $item;
        }

        $expected = [
            ['key1' => 'r1_1', 'key2' => 'r1_2'],
            ['key1' => 'r2_1', 'key2' => 'r2_2'],
        ];
        $this->assertEquals($expected, $actual);
    }
}
