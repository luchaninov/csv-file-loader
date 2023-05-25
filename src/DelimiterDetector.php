<?php

namespace Luchaninov\CsvFileLoader;

class DelimiterDetector
{
    private const DEFAULT_POSSIBLE_DELIMITERS = [',', ';', "\t"];

    /**
     * @param string|null $text
     * @param string[] $possibleDelimiters
     * @return string
     */
    public static function detect(?string $text, array $possibleDelimiters = self::DEFAULT_POSSIBLE_DELIMITERS): string
    {
        $len = strlen($text);
        $topDelimiter = $possibleDelimiters[0] ?? self::DEFAULT_POSSIBLE_DELIMITERS[0];
        $maxCount = 0;
        foreach ($possibleDelimiters as $delimiter) {
            $count = $len - strlen(str_replace($delimiter, '', $text));
            if ($count > $maxCount) {
                $topDelimiter = $delimiter;
                $maxCount = $count;
            }
        }

        return $topDelimiter;
    }
}
