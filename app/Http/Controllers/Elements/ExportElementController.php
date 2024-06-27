<?php
declare(strict_types=1);

namespace App\Http\Controllers\Elements;

use App\Actions\Element\Export\ExportElementAction;
use App\Actions\Element\Export\ExportElementHandlerFactory;
use App\Enums\ElementType;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

final class ExportElementController extends Controller
{
    public function __invoke(string $type, Request $request)
    {
        $authUser = Auth::user();
        $userCompanyId = $authUser->hasRole('client')
            ? User::getUserCompanyId('id', true)
            : (int)$request->input('company_id', -1);

        $handler = ExportElementHandlerFactory::make(
            ElementType::fromString(mb_strtolower($type)),
            new ExportElementAction(
                $userCompanyId === -1 ? null : $userCompanyId,
                Auth::user()->hasRole('admin')
            )
        );
        try {
            $fileName = $handler->handle();
        } catch (\DomainException $exception) {
            return response()->json(['message' => $exception->getMessage()])->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['url' => Storage::disk('export')->url($fileName)]);
    }
}
