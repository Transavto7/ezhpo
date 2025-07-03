<?php

declare(strict_types=1);

namespace Src\DocsGeneration\Http\Controllers;

use App\Enums\UserActionTypesEnum;
use App\Models\Forms\Form;
use Illuminate\Http\Request;
use Src\DocsGeneration\DocDataService;
use Src\Signatures\Enums\DocumentType;
use Src\Signatures\Services\SignatureWorker;

final class GetDocPageController extends DocsController
{
    public function __invoke(Request $request, DocDataService $service)
    {
        $formId = $request->route('anketa_id');
        /** @var string|null $type */
        $type = $request->route('type');

        if (! view()->exists("docs::$type")) {
            return view('docs::default');
        }

        /** @var Form $form */
        $form = Form::withTrashed()->find($formId);

        if (empty($form)) {
            return view('docs::undefined');
        }

        $this->sendEvent(UserActionTypesEnum::DOCUMENT_REQUEST);

        $data = $service->get($form);
        $data['type'] = $type;

        if ($data[$type.'_path']) {
            SignatureWorker::check($data[$type.'_path'], $form->id, DocumentType::from($type));
        }

        $signature = SignatureWorker::get($form->id, DocumentType::from($type));
        $data['signature'] = $signature;

        return view("docs::$type", $data);
    }
}
