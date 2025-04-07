<?php

declare(strict_types=1);

namespace Src\Core\Filters\AbstractFilters;

use Src\Core\Filters\Filter;

abstract class ArrayFilter implements Filter
{
    protected $column = 'default';

    /**
     * @var array|null
     */
    protected $value;

    private function __construct($value)
    {
        $this->value = $value;
    }

    public function apply($query)
    {
        $query->whereIn($this->column, $this->value);
    }

    public static function create($value): Filter
    {
        return new static($value);
    }

    public function validateValue(): bool
    {
        return is_array($this->value);
    }
}
