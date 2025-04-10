<?php

namespace App\ValueObjects;

use Stringable;

class Vin implements Stringable
{
    /**
     * @var bool
     */
    protected $isValid = true;

    /**
     * @var string
     */
    protected $native;

    /**
     * @var string
     */
    protected $sanitized = '';

    public function __construct(string $native = '')
    {
        $this->native = $native;
        $this->sanitize();
    }

    public function __toString()
    {
        return $this->getSanitized();
    }

    public function getSanitized(): string
    {
        return $this->sanitized;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    protected function sanitize()
    {
        $vin = preg_replace('/\s+/', '', strtoupper($this->native));

        $vin = replaceRuSymbolsToEnEquals($vin);

        if (!in_array(strlen($vin), [16, 17])) {
            $this->isValid = false;

            return;
        }

        if (preg_match('/^[a-zA-Z0-9]+$/', $vin) !== 1) {
            $this->isValid = false;

            return;
        }

        $this->sanitized = $vin;
    }
}
