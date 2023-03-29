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
        return (($value >= min($thresholds)) && ($value <= max($thresholds)));
    }
}

if (!function_exists('phone_sanitizing')) {
    function phone_sanitizing(string $phone) : string
    {
        return preg_replace('/^0|[^а-яА-Я0-9+]+/u', '', filter_var($phone, FILTER_SANITIZE_NUMBER_INT));
    }
}

if (!function_exists('tonometer_sanitizing')) {
    function tonometer_sanitizing(string $tonometer) : string
    {
        $tonometer = str_replace(['//', "\\"], ['/', '/'], $tonometer);
        return preg_replace('/[^0-9\/]/',"", $tonometer);
    }
}
