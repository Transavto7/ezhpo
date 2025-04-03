<?php

declare(strict_types=1);

namespace Src\Reminders\Conditions\BaseConditions;

use Illuminate\Database\Query\Builder;
use Src\Reminders\Conditions\Condition;

abstract class ArrayCondition implements Condition
{
    /** @var string */
    protected $conditionName = 'default-int';

    /** @var array<int, int|string>|null */
    protected $value;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->conditionName;
    }

    /**
     * @param Builder $query
     * @return void
     */
    public function run(Builder $query): Builder
    {
        $wheresString = implode(',', array_fill(0, count($this->value), '?'));

        return $query->where(function (Builder $query) use ($wheresString) {
            return $query
                ->whereRaw('JSON_EXTRACT(context, "$.'.$this->conditionName.'") in ('.$wheresString.')', $this->value)
                ->orWhereRaw('JSON_EXTRACT(context, "$.'.$this->conditionName.'") is null')
                ->orWhereRaw("JSON_EXTRACT(context, \"$.role\") = CAST('null' AS JSON)");
        });
    }

    /**
     * @param array<int, int|string>|null $value
     * @return void
     */
    public function setValue($value): void
    {
        $this->value = $value === null ? null : $value;
    }

    /**
     * @return array<int, int|string>|null
     */
    public function getValue(): ?array
    {
        return $this->value;
    }

    public static function create(): Condition
    {
        return new static();
    }
}
