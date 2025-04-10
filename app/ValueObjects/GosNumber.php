<?php

namespace App\ValueObjects;

use Stringable;

class GosNumber implements Stringable
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
        $gosNumber = preg_replace('/\s+/', '', strtoupper($this->native));

        if (preg_match('/^[a-zA-Zа-яА-Я0-9]+$/', $gosNumber) !== 1) {
            $this->isValid = false;

            return;
        }

        $this->sanitized = $gosNumber;
    }
}
