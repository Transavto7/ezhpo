<?php

declare(strict_types=1);

namespace Src\Reminders\Conditions;

use Illuminate\Database\Query\Builder;
use Src\Core\ValueObjects\ClassifierViewModel;

interface Condition
{
    public function run(Builder $query): Builder;

    public function makeViewModel(array $rawReminder): ?ClassifierViewModel;

    public function addJoin(Builder $query): Builder;

    public function getSelectFields(): array;

    public function getName(): string;

    public static function create(): self;

    public function setValue($value): void;

    /**
     * @return int|string|array<int, string|int>|null
     */
    public function getValue();
}
