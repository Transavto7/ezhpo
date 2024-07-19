<?php
declare(strict_types=1);

namespace App\Actions\Element\Import\Core;

abstract class ElementValidator
{
    protected $errors = [];

    abstract function validate(array $parsedDataItem);

    public function hasErrors(): bool
    {
        return ! empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
