<?php

namespace App\Services\QRCode;

use App\Car;
use App\Driver;
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
        $title = $this->getEntityTitle();
        $link = $this->linkGenerator->generate();
        $qrCode = $this->qrCodeGenerator->generate($link);
        $type = $this->linkGenerator->getParameter()->value() === QRCodeLinkParameter::CAR_ID
            ? self::CAR
            : self::DRIVER;
        $customPaper = [0, 0, $type === 'D' ? 375.00 : 335.00, 222.00];

        $pdf = Pdf::loadView('templates.qr-code', [
                'qrCode' => $qrCode,
                'title' => $title,
                'id' => $this->linkGenerator->getId(),
                'type' => $type,
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

    private function getEntityTitle(): string
    {
        if ($this->linkGenerator->getParameter()->value() === QRCodeLinkParameter::DRIVER_ID) {
            return Driver::where('hash_id', $this->linkGenerator->getId())->first()->fio;
        } elseif ($this->linkGenerator->getParameter()->value() === QRCodeLinkParameter::CAR_ID) {
            return Car::where('hash_id', $this->linkGenerator->getId())->first()->gos_number;
        } else {
            throw new \DomainException('Тип сущности не указан или указан неверно. Тип: '.$this->linkGenerator->getParameter()->value(), 400);
        }
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
