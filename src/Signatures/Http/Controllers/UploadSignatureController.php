<?php

declare(strict_types=1);

namespace Src\Signatures\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Forms\Form;
use Illuminate\Http\Request;
use Src\Signatures\Enums\DocumentType;
use Src\Signatures\Services\SignatureWorker;

final class UploadSignatureController extends Controller
{
    public function __invoke(Request $request, string $type, int $formId)
    {
        /** @var Form $form */
        $form = Form::withTrashed()->findOrFail($formId);
        $details = $form->details;
        $attribute = $type.'_path';

        if ($details === null) {
            return response('', 422)->json(['message' => 'Details for form not found.']);
        }

        $path = $form->details->$attribute;

        SignatureWorker::create($request->file('signature'), $formId, DocumentType::from($type), $path);

        return response('', 204);
    }
}
