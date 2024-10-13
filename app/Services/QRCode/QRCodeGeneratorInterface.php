<?php

namespace App\Services\QRCode;

interface QRCodeGeneratorInterface
{
    /**
     * @param string $data
     * @return mixed
     */
    public function generate(string $data);
}
