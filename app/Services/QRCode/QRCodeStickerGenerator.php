<?php

namespace App\Services\QRCode;

use App\Enums\QRCodeLinkParameter;
use Barryvdh\DomPDF\Facade as PDF;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Contracts\Container\BindingResolutionException;

class QRCodeStickerGenerator
{
    const DRIVER = 'D';
    const CAR = 'C';
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

    /**
     * @throws BindingResolutionException
     */
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
        $options->quietzoneSize = 0;
        $options->scale = 8;

        return (new QRCode($options))->render($this->linkGenerator->generate());
    }

    public function getUrl()
    {
        $url = config('app.url');
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

    public function getView()
    {
        return view('templates.qr-code',
            [
                'qrCode' => $this->getCRCode(),
                'id' => $this->linkGenerator->getId(),
                'type' => $this->linkGenerator->getParameter()->value() === QRCodeLinkParameter::CAR_ID
                    ? self::CAR
                    : self::DRIVER,
                'domain' => $this->getUrl()
            ]
        );
    }
}
