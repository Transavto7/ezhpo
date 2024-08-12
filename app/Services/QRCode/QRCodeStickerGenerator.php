<?php

namespace App\Services\QRCode;

use App\Enums\QRCodeLinkParameter;
use Barryvdh\DomPDF\Facade as PDF;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QRCodeStickerGenerator
{
    const DRIVER = 'DRIVER';
    const CAR = 'CAR';
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

    public function getPdfResponse()
    {
        $customPaper = array(0,0,300.00,225.00);

        $pdf = Pdf::loadView('templates.qr-code', [
                'qrCode' => $this->getCRCode(),
                'id' => $this->linkGenerator->getId(),
                'type' => $this->linkGenerator->getParameter()->value() === QRCodeLinkParameter::CAR_ID
                    ? self::CAR
                    : self::DRIVER,
                'domain' => $this->getUrl()
            ])
            ->setPaper($customPaper, 'landscape');

        $response = response()->make($pdf->output(), 200);
        $response->header('Content-Type', 'application/pdf');

        return $response;
    }

    public function getCRCode()
    {
        $options = new QROptions;
        $options->version = 4;

        return (new QRCode($options))->render($this->linkGenerator->generate());
    }

    private function getUrl()
    {
        $url = env('APP_URL');
        $http = 'http://';
        $https = 'https://';

        if (strpos($url, $http) !== false) {
            return substr($url,  strlen($http));
        }

        if (strpos($url, $https) !== false) {
            return substr($url,  strlen($https));
        }

        return $url;
    }

    private function getView(string $id, string $type, $img)
    {
        return view('templates.qr-code',
            [
                'qrCode' => $img->generate(),
                'id' => $id,
                'type' => $type === QRCodeLinkParameter::CAR_ID
                    ? self::CAR
                    : self::DRIVER,
                'domain' => $this->getUrl()
            ]
        );
    }
}
