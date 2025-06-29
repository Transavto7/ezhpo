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

    public function city(?int $value): self
    {
        $this->context['city'] = $value;
        return $this;
    }

    public function company(?int $value): self
    {
        $this->context['company'] = $value;
        return $this;
    }

    public function point(?int $value): self
    {
        $this->context['point'] = $value;
        return $this;
    }

    public function roles(?array $value): self
    {
        $this->context['role'] = $value;
        return $this;
    }

    public function role(?int $value): self
    {
        $this->context['role'] = $value;
        return $this;
    }

    public function subject(?int $value): self
    {
        $this->context['subject'] = $value;
        return $this;
    }

    public function subjectType(?ReminderSubjectType $value): self
    {
        $this->context['subject_type'] = $value->value();
        return $this;
    }

    public function toArray(): array
    {
        return $this->context;
    }
}
