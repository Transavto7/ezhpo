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

if(!function_exists('replaceRuSymbolsToEnEquals'))
{
    function replaceRuSymbolsToEnEquals(string $string): string
    {
        $ruToEnAlphabet = [
            'А' => 'A',
            'В' => 'B',
            'Е' => 'E',
            'К' => 'K',
            'М' => 'M',
            'Н' => 'H',
            'О' => 'О',
            'Р' => 'P',
            'C' => 'C',
            'Т' => 'T',
            'У' => 'Y',
            'Х' => 'X',

            'а' => 'a',
            'в' => 'b',
            'е' => 'e',
            'к' => 'k',
            'м' => 'm',
            'н' => 'h',
            'о' => 'о',
            'р' => 'p',
            'c' => 'c',
            'т' => 't',
            'у' => 'y',
            'х' => 'x',
        ];

        foreach ($ruToEnAlphabet as $ru => $en) {
            $string = str_replace($ru, $en, $string);
        }

        return $string;
    }
}
