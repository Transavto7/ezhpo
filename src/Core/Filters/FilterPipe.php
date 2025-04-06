<?php

declare(strict_types=1);

namespace Src\Core\Filters;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

interface FilterPipe
{
    public function addFilter(Filter $filter);

    /**
     * @template T of EloquentBuilder|Builder
     * @param T $query
     * @return T
     */
    public function run($query);
}
