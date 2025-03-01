<?php

namespace App\ValueObjects;

use App\Enums\FormFeedbackEnum;

final class FormFeedback
{
    /**
     * @var FormFeedbackEnum
     */
    private $feedback;

    /**
     * @param FormFeedbackEnum $feedback
     */
    private function __construct(FormFeedbackEnum $feedback)
    {
        $this->feedback = $feedback;
    }

    public static function fromItems(string $feedback): self
    {
        return new self(FormFeedbackEnum::fromString($feedback));
    }

    public static function fromArray(array $raw): self
    {
        return new self(FormFeedbackEnum::fromString($raw['feedback']));
    }

    public function getFeedback(): string
    {
        return $this->feedback;
    }


    public function toArray(): array
    {
        return [
            'feedback' => $this->feedback->value(),
        ];
    }
}
