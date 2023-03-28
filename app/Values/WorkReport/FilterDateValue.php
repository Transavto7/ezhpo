<?php

namespace App\Values\WorkReport;

use App\Values\Contracts\ValueInterface;
use Carbon\Carbon;

class FilterDateValue implements ValueInterface
{
    /**
     * @var \Carbon\Carbon|null
     */
    private ?Carbon $value = null;

    /**
     * @var string
     */
    private string $format = 'Y-m-d';

    /**
     * @param  string|null  $value
     */
    public function __construct(?string $value = null)
    {
        if ($value) {
            $this->value = Carbon::createFromFormat($this->format, $value);
        } else {
            $this->value = now();
        }

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

    public function setValue(string $value): self
    {
        $this->value = Carbon::createFromFormat($this->format, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param  string  $format
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }
}