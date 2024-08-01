<?php

namespace App\Http\Controllers\Api\Forms\TechnicalInspection;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class ParseQRCodeController extends Controller
{
    /**
     * @throws Exception
     */
    public function __invoke(Request $request)
    {
        $text = $request->input('decodedText');

        return response()->json(['id' => $this->getParamsFromQRText($text)]);
    }

    /**
     * @throws Exception
     */
    private function getParamsFromQRText(string $text): string
    {
        $appUrl = env('APP_URL');

        if (mb_strpos($text, $appUrl) === false) {
            throw new Exception('Посторонний QR Code');
        }

        if (mb_strpos($text, 'autoId')) {
            return substr($text, mb_strlen($appUrl) + 8);
        }

        return '';
    }
}
