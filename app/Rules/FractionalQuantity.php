<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FractionalQuantity implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_null($value) || $value === '') {
            return;
        }

        if (self::parseToFloat((string) $value) === null) {
            $fail('The :attribute must be a valid number or fraction (e.g. 1/2, 1.5, ¾).');
        }
    }

    /**
     * Parse a quantity string (decimal, fraction, unicode fraction, mixed number) to float.
     * Returns null if the value cannot be parsed.
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

    /**
     * Convert a float to a human-readable fraction string where applicable.
     * e.g. 0.5 → "1/2", 1.5 → "1 1/2", 0.33... → "1/3", 2.0 → "2"
     */
    public static function floatToFraction(float $value): string
    {
        if ($value == floor($value)) {
            return (string) (int) $value;
        }

        // Keys are strings to avoid PHP truncating float keys to int(0)
        $commonFractions = [
            '0.125' => '1/8',
            '0.25' => '1/4',
            '0.333' => '1/3',
            '0.375' => '3/8',
            '0.5' => '1/2',
            '0.625' => '5/8',
            '0.667' => '2/3',
            '0.75' => '3/4',
            '0.875' => '7/8',
        ];

        $whole = (int) floor($value);
        $decimal = round($value - $whole, 3);

        $fracStr = null;
        foreach ($commonFractions as $dec => $frac) {
            if (abs($decimal - $dec) < 0.005) {
                $fracStr = $frac;
                break;
            }
        }

        if ($fracStr === null) {
            return (string) $value;
        }

        return $whole > 0 ? "{$whole} {$fracStr}" : $fracStr;
    }
}
