<?php

declare(strict_types=1);

namespace Src\DocsGeneration\Http\Controllers;

use App\Enums\UserActionTypesEnum;
use App\Models\Forms\Form;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use Src\DocsGeneration\DocDataService;

final class GetDocPdfController extends DocsController
{
    /**
     * @throws FileNotFoundException
     * @throws BindingResolutionException
     */
    public function __invoke($type, $formId, DocDataService $service)
    {
        $form = Form::withTrashed()->find($formId);

        if (! $form) {
            return response('Осмотр не найден');
        }

        $this->sendEvent(UserActionTypesEnum::DOCUMENT_REQUEST_PDF);

        $details = $form->details;

        $path = $details[$type.'_path'];
        if (Storage::disk('public')->exists($path)) {
            $file = Storage::disk('public')->get($path);

            return response()
                ->make($file, 200)
                ->header('Content-Type', 'application/pdf');
        }

        $data = $service->get($form);
        $data['time'] = date('Hч iмин', strtotime($data['date']));
        $data['date_str'] = 'от '.date('d.m.Y', strtotime($data['date'])).' года';
        $data['post'] = 'Водитель';
        $data['alko'] = $data['alcometer_result'].' мг\л';

        //TODO: фикс для протокола, при необходимости - можно привести к 1 шаблону
        $view = 'docs::exports.'.$type;
        if (view()->exists("docs::exports.$type-new")) {
            $view = "docs::exports.$type-new";
        }

        $file = Pdf::loadView($view, $data);

        return response()
            ->make($file->output(), 200)
            ->header('Content-Type', 'application/pdf');
    }
}
