<?php

declare(strict_types=1);

namespace Src\Reminders\Conditions\BaseConditions;

use Illuminate\Database\Query\Builder;
use Src\Reminders\Conditions\Condition;

abstract class IntCondition implements Condition
{
    /** @var string */
    protected $conditionName = 'default-int';

    /** @var int|null */
    protected $value;

    final public function __construct()
    {
    }

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
        return $query->where(function (Builder $query) {
            return $query
                ->whereRaw('JSON_EXTRACT(context, "$.'.$this->conditionName.'") = ?', [$this->value])
                ->orWhereRaw('JSON_EXTRACT(context, "$.'.$this->conditionName.'") is null')
                ->orWhereRaw("JSON_EXTRACT(context, \"$.role\") = CAST('null' AS JSON)");
        });
    }

    /**
     * @param int|null $value
     * @return void
     */
    public function setValue($value): void
    {
        $this->value = $value === null ? null : intval($value);
    }

    /**
     * @return int|null
     */
    public function getValue(): ?int
    {
        return $this->value;
    }

    public static function create(): Condition
    {
        return new static();
    }
}
