<?php

namespace Src\Employees\Workdays\SmartEnum;

use Src\Helper\Pattern\SmartEnum;

final class WorkdayEventTypeEnum extends SmartEnum
{
    public const OPEN = 1;
    public const CLOSE = 2;

    public function isOpen()
    {
        return $this->value === self::OPEN;
    }

    public function isClose()
    {
        return $this->value === self::CLOSE;
    }

    public function setValue($value = null): self
    {
        if (is_numeric($value) && ($value === self::OPEN || $value === self::CLOSE)) {
            return parent::setValue($value);
        }
        if (is_string($value)) {
            switch ($value) {
                case 'open':
                case 'Открыта':
                    return parent::setValue(self::OPEN);
                case 'close':
                case 'Закрыта':
                    return parent::setValue(self::CLOSE);
            }
        }

        return $this;
    }

    public function getSpdoValue()
    {
        switch ($this->value) {
            case self::OPEN:
                return 'open';
            case self::CLOSE:
                return 'close';
        }
    }
}
