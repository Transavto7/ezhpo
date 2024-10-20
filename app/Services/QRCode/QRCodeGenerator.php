<?php

namespace App\Services\QRCode;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

final class QRCodeGenerator implements QRCodeGeneratorInterface
{
    /**
     * @inheritDoc
     */
    public function generate(string $data, int $version = QRCodeGeneratorInterface::VERSION_4)
    {
        $options = new QROptions;
        $options->version = $version;
        $options->quietzoneSize = 0;
        $options->scale = 8;

        $qrCode = new QRCode($options);

        return $qrCode->render($data);
    }
}
