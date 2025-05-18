<?php

declare(strict_types=1);

namespace Src\Core\Filters\AbstractFilters;

use Src\Core\Filters\Dto\DateRange;
use Src\Core\Filters\Filter;

abstract class DateRangeFilter implements Filter
{
    protected $column = 'default';

    /**
     * @var DateRange
     */
    protected $value;

    final private function __construct($value)
    {
        $this->value = $value;
    }

    public function apply($query)
    {
        if ($this->value->getStart()) {
            $query->where($this->column, '>=', $this->value->getStart()->startOfDay());
        }

        if ($this->value->getEnd()) {
            $query->where($this->column, '<=', $this->value->getEnd()->endOfDay());
        }
    }

    public static function create($value): Filter
    {
        return new static($value);
    }

    public function validateValue(): bool
    {
        return $this->value instanceof DateRange;
    }
}
