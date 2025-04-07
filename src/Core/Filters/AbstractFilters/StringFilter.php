<?php

declare(strict_types=1);

namespace Src\Core\Filters\AbstractFilters;

use Src\Core\Filters\Filter;

abstract class StringFilter implements Filter
{
    protected $column = 'default';

    /**
     * @var string
     */
    protected $value;

    private function __construct($value)
    {
        $this->value = $value;
    }

    public function apply($query)
    {
        $query->where($this->column, 'ilike', '%'.$this->value.'%');
    }

    public static function create($value): Filter
    {
        return new static($value);
    }

    public function validateValue(): bool
    {
        return is_string($this->value);
    }
}
