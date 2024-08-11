<?php

namespace App\Services\QRCode;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QRCodeStickerGenerator
{
    /**
     * @var QRCodeLinkGenerator
     */
    protected $linkGenerator;

    /**
     * @param QRCodeLinkGenerator $linkGenerator
     */
    public function __construct(QRCodeLinkGenerator $linkGenerator)
    {
        $this->linkGenerator = $linkGenerator;
    }

    public function generate()
    {
        $data = $this->getCRCode();

        return $data;
    }

    public function getCRCode()
    {
        $options = new QROptions;
        $options->version = 4;

        return (new QRCode($options))->render($this->linkGenerator->generate());
    }
}
