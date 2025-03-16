<?php

namespace App\Services\QRCode;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

final class QRCodeImageGenerator implements QRCodeGeneratorInterface
{
    /**
     * @inheritDoc
     */
    public function generate(string $data, int $version = QRCodeGeneratorInterface::VERSION_4, string $filePath = null)
    {
        $options = new QROptions;
        $options->version = $version;
        $options->quietzoneSize = 0;
        $options->scale = 8;
        $options->outputType = QRCode::OUTPUT_IMAGE_JPG;

        $qrCode = new QRCode($options);

        return $qrCode->render($data, $filePath);
    }
}
