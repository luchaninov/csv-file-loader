<?php

use Luchaninov\CsvFileLoader\DelimiterDetector;
use PHPUnit\Framework\TestCase;

class DelimiterDetectorTest extends TestCase
{
    public function testDetect(): void
    {
        self::assertSame(',', DelimiterDetector::detect('1,2,3'));
        self::assertSame(',', DelimiterDetector::detect('1,2,3;'."\t"));
        self::assertSame(',', DelimiterDetector::detect('1,2,3;;'."\t\t"));
        self::assertSame(',', DelimiterDetector::detect('1;2;3,,'."\t\t"));
        self::assertSame(';', DelimiterDetector::detect(';1;2;3,,'."\t\t"));
    }
}
