<?php
declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

final class DateIsCorrectFormat implements Rule
{
    public function passes($attribute, $value): bool
    {
        return (bool)preg_match("/^(3[01]|[12][0-9]|0?[1-9])\.(1[012]|0?[1-9])\.((?:19|20)\d{2})$/m", $value);
    }

    public function message(): string
    {
        return ':attribute должна иметь формат дд.мм.гггг.';
    }
}
