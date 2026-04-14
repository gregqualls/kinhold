<?php

namespace App\Rules;

class FractionalQuantity
{
    /**
     * Parse a quantity string (decimal, fraction, unicode fraction, mixed number) to float.
     * Returns null if the value cannot be parsed.
     *
     * Examples: "2" → 2.0, "1/2" → 0.5, "1 1/2" → 1.5, "½" → 0.5, "1¾" → 1.75
     */
    public static function parseToFloat(string $value): ?float
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        // Already a valid decimal / integer
        if (is_numeric($value)) {
            return (float) $value;
        }

        // Unicode fraction map
        $unicodeMap = [
            '½' => 0.5,
            '¼' => 0.25,
            '¾' => 0.75,
            '⅓' => 1 / 3,
            '⅔' => 2 / 3,
            '⅛' => 0.125,
            '⅜' => 0.375,
            '⅝' => 0.625,
            '⅞' => 0.875,
        ];

        // Pure unicode fraction: "½"
        if (isset($unicodeMap[$value])) {
            return $unicodeMap[$value];
        }

        // Integer + unicode fraction: "1½", "2¾"
        foreach ($unicodeMap as $char => $frac) {
            if (preg_match('/^(\d+)'.preg_quote($char, '/').'$/', $value, $m)) {
                return (float) $m[1] + $frac;
            }
        }

        // Mixed number with unicode: "1 ½"
        foreach ($unicodeMap as $char => $frac) {
            if (preg_match('/^(\d+)\s+'.preg_quote($char, '/').'$/', $value, $m)) {
                return (float) $m[1] + $frac;
            }
        }

        // Simple fraction: "1/2", "3/4"
        if (preg_match('/^(\d+)\s*\/\s*(\d+)$/', $value, $m)) {
            $denominator = (int) $m[2];
            if ($denominator === 0) {
                return null;
            }

            return (int) $m[1] / $denominator;
        }

        // Mixed number: "1 1/2", "2 3/4"
        if (preg_match('/^(\d+)\s+(\d+)\s*\/\s*(\d+)$/', $value, $m)) {
            $denominator = (int) $m[3];
            if ($denominator === 0) {
                return null;
            }

            return (int) $m[1] + ((int) $m[2] / $denominator);
        }

        return null;
    }
}
