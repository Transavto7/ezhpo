<?php

namespace App\Services\FormsLabelingPDFGenerator;

use InvalidArgumentException;

class FormLabelingTemplate
{
    const WIDE = 'wide';
    const NARROW = 'narrow';

    /**
     * @var string
     */
    private $template;
    /**
     * @var array
     */
    private $paper;

    /**
     * @param string $template
     * @param array $paper
     */
    private function __construct(string $template, array $paper)
    {
        $this->template = $template;
        $this->paper = $paper;
    }

    public static function fromTemplateName(string $value): self
    {
        switch (true) {
            case $value === self::WIDE:
                return new self(self::WIDE, [0, 0, 250.00, 484.00]);
            case $value === self::NARROW:
                return new self(self::NARROW, [0, 0, 240.00, 167.00]);
            default:
                throw new InvalidArgumentException('Invalid form labeling template name');
        }
    }

    public function getView(): string
    {
        return 'templates.form-labeling.'.$this->template;
    }

    public function getPaper(): array
    {
        return $this->paper;
    }
}
