<?php

namespace Src\Employees\Workdays\SmartEnum;

use Exception;
use Src\Helper\Pattern\SmartEnum;

final class WorkdayEventTypeEnum extends SmartEnum
{
    public const OPEN = 1;

    public const CLOSE = 2;

    public function isOpen(): bool
    {
        return $this->value === self::OPEN;
    }

    public function isClose(): bool
    {
        return $this->value === self::CLOSE;
    }

    /**
     * @throws Exception
     */
    public function setValue($value = null): self
    {
        if (is_numeric($value)) {
            $value = intval($value);
        }

        switch ($value) {
            case self::OPEN:
            case 'open':
            case 'Открыта':
                return parent::setValue(self::OPEN);
            case self::CLOSE:
            case 'close':
            case 'Закрыта':
                return parent::setValue(self::CLOSE);
            default:
                throw new Exception("Unsupported value - $value");
        }
    }

    /**
     * @throws Exception
     */
    public function getSpdoValue(): string
    {
        switch ($this->value) {
            case self::OPEN:
                return 'open';
            case self::CLOSE:
                return 'close';
            default:
                throw new Exception("Unsupported value - $this->value");
        }
    }

    /**
     * @throws Exception
     */
    public function getTitle(): string
    {
        switch ($this->value) {
            case self::OPEN:
                return 'Открытие';
            case self::CLOSE:
                return 'Закрытие';
            default:
                throw new Exception("Unsupported value - $this->value");
        }
    }

    public static function cases(): array
    {
        $variants = [
            self::create(self::OPEN),
            self::create(self::CLOSE),
        ];

        $cases = [];

        /** @var WorkdayEventTypeEnum $variant */
        foreach ($variants as $variant) {
            $cases[$variant->value] = $variant->getTitle();
        }

        return $cases;
    }
}
