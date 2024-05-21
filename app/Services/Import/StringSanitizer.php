<?php
declare(strict_types=1);

namespace App\Services\Import;

final class StringSanitizer
{
    const TRIM_CHARACTERS = ", \t\n\r\0\x0B";

    public static function sanitize($value)
    {
        if (! is_string($value)) {
            return $value;
        }

        $value = str_replace("\xc2\xa0", ' ', $value); //  Non-breakable space (nbsp)

        return preg_replace(['/\s{2,}/'], ' ', trim($value, self::TRIM_CHARACTERS));
    }

    public static function sanitizeDriverLicense($value)
    {
        if (! is_string($value)) {
            return $value;
        }

        if (mb_strpos($value, ',') !== false) {
            $licenses = str_replace(',', '',$value);

            return substr($licenses, 0, 4) . " " . substr($licenses, 4);
        }

        return $value;
    }

}
