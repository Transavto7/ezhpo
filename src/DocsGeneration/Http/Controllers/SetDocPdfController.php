<?php

declare(strict_types=1);

namespace Src\DocsGeneration\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Forms\Form;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Src\Signatures\Enums\DocumentType;
use Src\Signatures\Services\SignatureWorker;

final class SetDocPdfController extends Controller
{
    /**
     * @throws FileNotFoundException
     * @throws BindingResolutionException
     */
    public function __invoke(Request $request, $type, $formId)
    {
        $form = Form::withTrashed()->find($formId);

        if (! $form) {
            return response('Осмотр не найден');
        }

        $request->validate([
            'pdf' => ['required', 'mimes:pdf'],
        ]);

        $pdf = $request->file('pdf');
        if (! $pdf) {
            return response('Файл не найден');
        }

        $path = Storage::disk('public')->putFileAs($type, $pdf, 'Документ осмотра №'.$formId.'.pdf');
        $form->details->update([
            $type.'_path' => $path,
        ]);

        SignatureWorker::create(
            null,
            $form->id,
            DocumentType::from($type),
            $path
        );

        return back();
    }
}
