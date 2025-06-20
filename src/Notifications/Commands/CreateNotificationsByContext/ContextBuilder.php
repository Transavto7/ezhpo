<?php

namespace Src\Notifications\Commands\CreateNotificationsByContext;

use Src\Reminders\Enums\ReminderSubjectType;

final class ContextBuilder
{
    /**
     * @var array
     */
    private $context;

    private function __construct()
    {
        $this->context = [];
    }

    public static function create(): self
    {
        return new self();
    }

    public function city(?int $id): self
    {
        $this->context['city_id'] = $id;
        return $this;
    }

    public function company(?int $id): self
    {
        $this->context['company'] = $id;
        return $this;
    }

    public function point(?int $id): self
    {
        $this->context['point'] = $id;
        return $this;
    }

    public function roles(?array $ids): self
    {
        $this->context['roles'] = $ids;
        return $this;
    }

    public function role(?array $id): self
    {
        $this->context['role'] = $id;
        return $this;
    }

    public function subject(?int $id): self
    {
        $this->context['subject'] = $id;
        return $this;
    }

    public function subjectType(ReminderSubjectType $type): self
    {
        $this->context['subject_type'] = $type->value();
        return $this;
    }

    public function toArray(): array
    {
        return $this->context;
    }
}
