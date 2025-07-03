<?php

declare(strict_types=1);

namespace Src\DocsGeneration\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Forms\Form;
use Src\Signatures\Enums\DocumentType;
use Src\Signatures\Services\SignatureWorker;

final class DeleteDocController extends Controller
{
    /**
     * @throws \Exception
     */
    public function __invoke(string $type, int $anketaId)
    {
        $form = Form::withTrashed()->find($anketaId);

        if (! $form) {
            return back()->withErrors(['Осмотр не найден']);
        }

        $form->details->update([
            $type.'_path' => null,
        ]);

        $signature = SignatureWorker::get($anketaId, DocumentType::from($type));
        if ($signature !== null) {
            $signature->delete();
        }

        return back();
    }
}
