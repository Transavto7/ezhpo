<?php
declare(strict_types=1);

namespace App\Services\Import;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

final class DateParser
{
    /**
     * @throws \Exception
     */
    public static function parse($value): ?string
    {
        return self::getDateFromExcelNumeric($value);
    }

    /**
     * @param $date
     * @return false|mixed|string
     * @throws \Exception
     */
    private static function getDateFromExcelNumeric($date)
    {
        if (is_numeric($date)) {
            return date('Y-m-d', Date::excelToTimestamp($date));
        } else {
            return self::createDateFromString($date);
        }
    }

    /**
     * @param $rawText
     * @return mixed
     */
    private static function createDateFromString($rawText)
    {
        $pattern = "/\d{2}\.\d{2}\.\d{4}|\d{1}\.\d{2}\.\d{4}|\d{1}\.\d{2}\.\d{2}|\d{2}\.\d{2}\.\d{2}/";
        if (preg_match($pattern, $rawText, $matches)) {
            return $matches[0];
        }

        return $rawText;
    }

}
