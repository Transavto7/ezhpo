<?php
declare(strict_types=1);

namespace App\Actions\Element\Import\Core;

interface ErrorObject
{
    public function toArray(): array;
}
