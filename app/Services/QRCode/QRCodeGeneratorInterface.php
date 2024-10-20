<?php

namespace App\Services\QRCode;

interface QRCodeGeneratorInterface
{
    const VERSION_4 = 4;
    const VERSION_5 = 5;
    const VERSION_6 = 6;
    const VERSION_7 = 7;
    const VERSION_8 = 8;

    /**
     * @param string $data
     * @param int $version
     * @return mixed
     */
    public function generate(string $data, int $version);
}
