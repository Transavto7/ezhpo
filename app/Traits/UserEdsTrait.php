<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait UserEdsTrait
{
    public static function getValidityString(string $validityEdsStart = null, string $validityEdsEnd = null): ?string
    {
        if ($validityEdsStart && $validityEdsEnd) {
            return sprintf(
                'Срок действия с %s по %s',
                Carbon::parse($validityEdsStart)->format('d.m.Y'),
                Carbon::parse($validityEdsEnd)->format('d.m.Y')
            );
        }

        return null;
    }
}
