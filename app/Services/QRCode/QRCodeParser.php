<?php

namespace App\Services\QRCode;

use App\Car;
use App\Driver;
use App\Enums\QRCodeLinkParameter;
use DomainException;

final class QRCodeParser
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $link;

    /**
     * @var QRCodeLinkParameter
     */
    private $parameter;

    /**
     * @param string $link
     * @param QRCodeLinkParameter $parameter
     */
    public function __construct(string $link, QRCodeLinkParameter $parameter)
    {
        $this->link = $link;
        $this->parameter = $parameter;

        $this->url = route('addAnket', ['type' => 'tech']);
    }
    public function getParameter(): string
    {
        $this->validateLink();

        $id = $this->parseLink();

        if ($this->isRecordExist($id)) {
            return $id;
        } else {
            throw new DomainException("Не найдена запись с {$this->parameter->value()} = $id");
        }
    }

    /**
     * @throws DomainException
     */
    private function validateLink()
    {
        if (mb_strpos($this->link, $this->url) === false) {
            throw new DomainException('Посторонний QR Code');
        }

        if (mb_strpos($this->link, $this->parameter->value()) === false) {
            throw new DomainException('В ссылке отсутствует необходимый параметр: ' . $this->parameter->value());
        }
    }

    private function parseLink(): string
    {
        $urlLen = mb_strlen($this->url);
        $paramLen = mb_strlen($this->parameter->value());
        $separatorsLen = 2;

        $start = $urlLen + $paramLen + $separatorsLen;

        $id = mb_substr($this->link, $start);

        if (! ctype_digit($id)) {
            throw new DomainException("ID содержит посторонние символы, отличные от цифровых: $id");
        }

        return $id;
    }

    private function isRecordExist(string $id): bool
    {
        switch ($this->parameter->value()) {
            case QRCodeLinkParameter::DRIVER_ID:
                return Driver::where('hash_id', $id)->count() === 1;
            case QRCodeLinkParameter::CAR_ID:
                return Car::where('hash_id', $id)->count() === 1;
            default:
                return false;
        }
    }
}
