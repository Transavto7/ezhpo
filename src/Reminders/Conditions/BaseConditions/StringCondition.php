<?php

declare(strict_types=1);

namespace Src\Reminders\Conditions\BaseConditions;

use Illuminate\Database\Query\Builder;
use Src\Reminders\Conditions\Condition;
use Src\Reminders\ViewModels\SelectViewModel;

abstract class StringCondition implements Condition
{
    /** @var string */
    protected $conditionName = 'default-string';

    /** @var string|null */
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
        return $query->where(function (Builder $query) {
            return $query
                ->whereRaw('JSON_EXTRACT(context, "$.'.$this->conditionName.'") = ?', [$this->value])
                ->orWhereRaw('JSON_EXTRACT(context, "$.'.$this->conditionName.'") is null');
        });
    }

    /**
     * @param string|null $value
     * @return void
     */
    public function setValue($value): void
    {
        if ($value === null) {
            $this->value = null;

            return;
        }

        $this->value = is_string($value) ? $value : (string) $value;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    public static function create(): Condition
    {
        return new static();
    }
}
