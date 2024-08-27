<?php

namespace App\Http\Controllers\Api\Forms\TechnicalInspection;

use App\Enums\QRCodeLinkParameter;
use App\Http\Controllers\Controller;
use App\Services\QRCode\QRCodeParser;
use DomainException;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ParseQRCodeController extends Controller
{
    /**
     * @throws Exception
     */
    public function __invoke(Request $request)
    {
        try {
            $parser = new QRCodeParser(
                $request->input('decodedText'),
                QRCodeLinkParameter::fromString($request->input('fieldType'))
            );

            return response()->json(['id' => $parser->getParameter()]);
        } catch (DomainException $exception) {
            return response()->json(['error' => $exception->getMessage()])->setStatusCode(ResponseAlias::HTTP_NOT_FOUND);
        }
    }
}
