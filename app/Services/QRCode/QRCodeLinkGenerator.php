<?php

namespace App\Services\QRCode;

use App\Enums\QRCodeLinkParameter;

final class QRCodeLinkGenerator
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $paramSeparator = '&';

    /**
     * @var string
     */
    private $equal = '=';

    /**
     * @var string
     */
    private $id;

    /**
     * @var QRCodeLinkParameter
     */
    private $parameter;

    /**
     * @param string $id
     * @param QRCodeLinkParameter $parameter
     */
    public function __construct(string $id, QRCodeLinkParameter $parameter)
    {
        $this->id = $id;
        $this->parameter = $parameter;

        $this->url = route('forms.index', ['type' => 'tech']);
    }

    public function generate(): string
    {
        return $this->url.$this->paramSeparator.$this->parameter->value().$this->equal.$this->id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return QRCodeLinkParameter
     */
    public function getParameter(): QRCodeLinkParameter
    {
        return $this->parameter;
    }
}
