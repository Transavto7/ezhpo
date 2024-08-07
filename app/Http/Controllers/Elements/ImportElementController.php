<?php
declare(strict_types=1);

namespace App\Http\Controllers\Elements;

use App\Actions\Element\Import\ImportElementAction;
use App\Actions\Element\Import\ImportElementHandlerFactory;
use App\Enums\ElementType;
use App\Events\UserActions\ClientDocImport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportElementRequest;
use App\Services\FileSaver;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class ImportElementController extends Controller
{
    public function __invoke(ImportElementRequest $request): JsonResponse
    {
        event(new ClientDocImport(Auth::user(), $request->input('type')));

        DB::beginTransaction();
        try {
            $handler = ImportElementHandlerFactory::make(ElementType::fromString($request->input('type')));
            $file = $request->file('file');
            $fileName = FileSaver::save($file, 'import');

            $response = $handler->handle(
                new ImportElementAction(
                    Storage::disk('import')->path($fileName)
                )
            );
            DB::commit();

            return response()->json($response->toArray());
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
