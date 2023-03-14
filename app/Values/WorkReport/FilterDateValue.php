<?php

namespace App\Values\WorkReport;

use App\Values\Contracts\ValueInterface;
use Carbon\Carbon;

class FilterDateValue implements ValueInterface
{
    /**
     * @var \Carbon\Carbon|false
     */
    private Carbon $value;

    /**
     * @var string
     */
    private string $format = 'Y-m-d';

    /**
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = Carbon::createFromFormat($this->format, $value);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    public function getValue()
    {
        return $this->value;
    }
}