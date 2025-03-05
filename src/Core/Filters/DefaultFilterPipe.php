<?php

declare(strict_types=1);

namespace Src\Core\Filters;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

final class DefaultFilterPipe implements FilterPipe
{
    /** @var Filter[] */
    private $filters = [];

    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * @template T of EloquentBuilder|Builder
     * @param T $query
     * @return T
     */
    public function run($query)
    {
        foreach ($this->filters as $filter) {
            $filter->apply($query);
        }

        return $query;
    }
}
