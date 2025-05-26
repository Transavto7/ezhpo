<?php

namespace App\ValueObjects;

use App\Exceptions\InvalidCarGosNumberException;
use App\Exceptions\InvalidCarRegionException;
use Stringable;

class GosNumber implements Stringable
{
    /**
     * @var bool
     */
    protected $isValid = true;

    /**
     * @var string
     */

    protected $type;

    /**
     * @var string
     */
    protected $native;

    /**
     * @var string
     */
    protected $number;

    /**
     * @var string
     */
    protected $region;

    /**
     * @var string
     */
    protected $sanitized = '';

    public function __construct(string $native = '', string $type = null)
    {
        $this->native = $native;
        $this->type = $type;
        $this->sanitize();
    }

    public function __toString()
    {
        return $this->getSanitized();
    }

    public function getSanitized(): string
    {
        return $this->sanitized;
    }

    public function getNative(): string
    {
        return $this->native;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    protected function sanitize()
    {
        $gosNumber = preg_replace('/\s+/', '', strtoupper($this->native));

        if (mb_strlen($gosNumber) < 8 || mb_strlen($gosNumber) > 9) {
            $this->isValid = false;

            return;
        }

        if (preg_match('/^[a-zA-Zа-яА-Я0-9]+$/', $gosNumber) !== 1) {
            $this->isValid = false;

            return;
        }

        if ($this->validateCarNumber($gosNumber) === false) {
            throw new InvalidCarGosNumberException();
        }

        if ($this->validateRegionCode($gosNumber) === false) {
            throw new InvalidCarRegionException();
        }

        $this->number = mb_substr($gosNumber, 0, 6);
        $this->sanitized = $gosNumber;
    }

    function validateCarNumber(string $number)
    {
        $standard = '/^[АВЕКМНОРСТУХABEKMHOPCTYX]{1}\d{3}[АВЕКМНОРСТУХABEKMHOPCTYX]{2}\d{2,3}$/u';
        $trailer = '/^[АВЕКМНОРСТУХABEKMHOPCTYX]{2}\d{3}\d{2,3}$/u';
        $moto = '/^\d{4}[АВЕКМНОРСТУХABEKMHOPCTYX]{2}\d{2,3}$/u';

        switch (true) {
            case $this->type === 'Е - прицепы': // Номера прицепов (АА111196)
                return preg_match($trailer, $number);
            case $this->type === 'М - мототехника (мопеды\\мотоциклы\\трициклы и т.п.)': // Мотоциклы, трактора (1111АА96)
            case $this->type === 'Tr - трактора\\с-х техника':
                return preg_match($moto, $number);
            case $this->type === 'В - легковые и грузовые автомобили до 3.5 тн': // Стандартный (А111АА96)
            case $this->type === 'С - грузовые т\\с от 3.5 тн':
            case $this->type === 'Ст - спецтранспорт':
            case $this->type === 'D - автобусы':
            default:
                return preg_match($moto, $standard);
        }
    }

    function validateRegionCode(string $number): bool
    {
        $region = mb_substr($number, 6);
        $this->region = $region;

        $validRegions = config('car_region_codes');

        return in_array($region, $validRegions);
    }

    public function getDetails(): array
    {
        return [
            'region' => $this->getRegion(),
            'number' => $this->getNumber(),
            'raw' => $this->getNative(),
            'formated' => $this->getSanitized(),
        ];
    }
}
