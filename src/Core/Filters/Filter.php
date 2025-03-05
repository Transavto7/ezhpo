<?php

declare(strict_types=1);

namespace Src\Core\Filters;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

interface Filter
{
    /**
     * @template T of EloquentBuilder|Builder
     * @param T $query
     */
    public function apply($query);

    public static function create($value): self;

    public function validateValue(): bool;
}
