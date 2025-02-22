<?php

if(!function_exists('user'))
{
    function user()
    {
        return auth()->user();
    }
}
if (! function_exists('dd500')) {
    function dd500(...$args)
    {
        http_response_code(500);
        foreach ($args as $arg) {
            dump($arg);
        }
        die(1);
    }
}

if(!function_exists('return_bytes'))
{
    function return_bytes($size_str)
    {
        switch (substr ($size_str, -1))
        {
            case 'M': case 'm': return (int)$size_str * 1048576;
            case 'K': case 'k': return (int)$size_str * 1024;
            case 'G': case 'g': return (int)$size_str * 1073741824;
            default: return $size_str;
        }
    }
}

if(!function_exists('getValueByPriority'))
{
    function getValueByPriority(...$args)
    {
        foreach ($args as $arg) {
            if ($arg) {
                return $arg;
            }
        }

        return null;
    }
}

if(!function_exists('convertStringToBoolean'))
{
    function convertStringToBoolean(?string $string): bool
    {
        if ($string) {
            return mb_strtolower($string) !== 'нет';
        }

        return false;
    }
}
