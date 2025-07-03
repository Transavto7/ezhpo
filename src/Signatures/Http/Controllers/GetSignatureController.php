<?php

declare(strict_types=1);

namespace Src\Signatures\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use Src\Signatures\Enums\DocumentType;
use Src\Signatures\Services\SignatureWorker;

final class GetSignatureController extends Controller
{
    /**
     * @throws FileNotFoundException
     * @throws \Exception
     */
    public function __invoke(string $type, int $formId)
    {
        $signature = SignatureWorker::get($formId, DocumentType::from($type));

        if ($signature === null) {
            return response('', 404);
        }

        if (Storage::disk('public')->exists($signature->path)) {
            $file = Storage::disk('public')->path($signature->path);

            return response()
                ->download($file);
        }

        throw new \Exception('Ошибка получения подписи');
    }
}
