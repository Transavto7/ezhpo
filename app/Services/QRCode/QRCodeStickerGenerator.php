<?php

namespace App\Services\QRCode;

use App\Enums\QRCodeLinkParameter;
use Barryvdh\DomPDF\Facade as PDF;
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
     * @var QRCodeGeneratorInterface
     */
    private $qrCodeGenerator;

    /**
     * @param QRCodeLinkGenerator $linkGenerator
     * @throws BindingResolutionException
     */
    public function __construct(QRCodeLinkGenerator $linkGenerator)
    {
        $this->linkGenerator = $linkGenerator;
        $this->qrCodeGenerator = app()->make(QRCodeGeneratorInterface::class);
    }

    /**
     * @throws BindingResolutionException
     */
    public function getPdfResponse()
    {
        $customPaper = array(0,0,282.00,222.00);

        $link = $this->linkGenerator->generate();
        $qrCode = $this->qrCodeGenerator->generate($link);

        $pdf = Pdf::loadView('templates.qr-code', [
                'qrCode' => $qrCode,
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
        $link = $this->linkGenerator->generate();
        $qrCode = $this->qrCodeGenerator->generate($link);

        return view('templates.qr-code',
            [
                'qrCode' => $qrCode,
                'id' => $this->linkGenerator->getId(),
                'type' => $this->linkGenerator->getParameter()->value() === QRCodeLinkParameter::CAR_ID
                    ? self::CAR
                    : self::DRIVER,
                'domain' => $this->getUrl()
            ]
        );
    }
}
