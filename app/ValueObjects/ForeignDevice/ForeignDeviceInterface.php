<?php

namespace App\ValueObjects\ForeignDevice;

/**
 * Объект, описывающий устройство (тонометр, термометр и т.д.)
*/
interface ForeignDeviceInterface
{
    /**
     * Генерирует объект с легальными, рандомными данными
     *
     * @return static
     */
    public static function random();

    /**
     * Провести полный тест
     *
     * @param ForeignDeviceLimitInterface|null $limits
     * @return bool Тест пройден?
     */
    public function isAdmitted(?ForeignDeviceLimitInterface $limits): bool;
}
