<?php

/**
 * фанк...
 */
if (!function_exists('user')) {
    function user()
    {
        return auth()->user();
    }
}

if (!function_exists('subtract_array_values')) {
    function subtract_array_values($minuend, $subtrahend): array
    {
        return array_map(function (int $x, int $y) {
            return $y - $x;
        }, $subtrahend, $minuend);
    }
}

if (!function_exists('in_array_thresholds')) {
    function in_array_thresholds(int $value, array $thresholds): bool
    {
        return ($value >= min($thresholds)) && ($value <= max($thresholds));
    }
}
