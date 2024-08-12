<?php

namespace App\Http\Controllers\Api\Forms\TechnicalInspection;

use App\Enums\QRCodeLinkParameter;
use App\Services\QRCode\HashIdChecker;
use App\Services\QRCode\QRCodeLinkGenerator;
use App\Services\QRCode\QRCodeStickerGenerator;
use Exception;
use Illuminate\Http\Request;

class QRCodeStickerController
{
    /**
     * @throws Exception
     */
    public function __invoke(Request $request)
    {
        $entityId = $request->input('id');
        $type = $request->input('type');
        $user = $request->user('api');

        try {
            $checker = new HashIdChecker($user, $entityId, $type);
            $checker->checkAll();

            $generator = new QRCodeStickerGenerator(
                new QRCodeLinkGenerator(
                    $request->input('id'),
                    QRCodeLinkParameter::fromString($request->input('type'))
                )
            );

            return $generator->getPdfResponse();
        } catch (Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
}

