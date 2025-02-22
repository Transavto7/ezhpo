<?php

namespace Src\Users\Management\Queries\GetUsersListPage;

final class FilterOption implements \JsonSerializable
{
    /**
     * @var int|string
     */
    private $value;
    /**
     * @var string
     */
    private $label;

    /**
     * @param int|string $value
     * @param string $label
     */
    public function __construct($value, string $label)
    {
        $this->value = $value;
        $this->label = $label;
    }

    /**
     * @return int|string
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getLabel(): string
    {
        return $this->label;
    }


    public function jsonSerialize(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label,
        ];
    }
}