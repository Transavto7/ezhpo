<?php
declare(strict_types=1);

namespace Src\DocsGeneration\Http\Controllers;

use App\Enums\UserActionTypesEnum;
use App\Http\Controllers\Controller;
use App\Models\Forms\Form;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Src\DocsGeneration\DocDataService;
use Src\Signatures\Enums\DocumentType;
use Src\Signatures\Services\SignatureWorker;

final class UpdateDocController extends Controller
{
    public function __invoke(Request $request, $type)
    {
        $form = Form::withTrashed()->find($request->id);

        if (! $form) {
            return response()->json(['message' => 'Осмотр не найден']);
        }

        $details = $form->details;

        $data = array_merge($form->toArray(), $request->all(), $details->toArray());
        $pdf = Pdf::loadView('docs::exports.'.$type, $data);
        $path = $type.'/Документ осмотра №'.$request->id.'.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        $details->update([
            $type.'_path' => $path,
        ]);

        SignatureWorker::create(
            null,
            $form->id,
            DocumentType::from($type),
            $path
        );
    }
}
