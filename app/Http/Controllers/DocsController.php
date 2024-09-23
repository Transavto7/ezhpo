<?php

namespace App\Http\Controllers;

use App\Enums\UserActionTypesEnum;
use App\Events\UserActions\ClientDocumentRequest;
use App\Models\Forms\Form;
use App\Services\DocDataService;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Storage;

class DocsController extends Controller
{
    public function Get (Request $request, DocDataService $service)
    {
        $this->sendEvent(UserActionTypesEnum::DOCUMENT_REQUEST);

        $formId = $request->anketa_id;
        $type = $request->type;

        if (!view()->exists("docs.$type")) {
            return view("docs.default");
        }

        /** @var Form $form */
        $form = Form::find($formId);

        if (empty($form)) {
            return view("docs.undefined");
        }

        $data = $service->get($form);
        $data['type'] = $type;

        return view("docs.$type", $data);
    }

    public function update(Request $request, $type)
    {
        $form = Form::find($request->id);
        $details = $form->details;

        if (!$form) {
            return response()->json(['message' => 'Осмотр не найден']);
        }

        $data = array_merge($form->toArray(), $request->all(), $details->toArray());
        $pdf = Pdf::loadView('docs.exports.' . $type, $data);
        $path = $type . '/Документ осмотра №' . $request->id . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        $details->update([
            $type . '_path' => $path
        ]);
    }

    public function delete($type, $anketa_id)
    {
        Form::find($anketa_id)->details->update([
            $type. '_path' => null
        ]);

        return back();
    }

    public function getPdf($type, $formId, DocDataService $service)
    {
        $this->sendEvent(UserActionTypesEnum::DOCUMENT_REQUEST_PDF);

        $form = Form::find($formId);
        $details = $form->details;

        if (!$form) {
            return response('Осмотр не найден');
        }

        $path = $details[$type . '_path'];
        if (Storage::disk('public')->exists($path)) {
            $file = Storage::disk('public')->get($path);

            return response()
                ->make($file, 200)
                ->header('Content-Type', 'application/pdf');
        }

        $data = $service->get($form);
        $data['time'] = date('Hч iмин', strtotime($data['date']));
        $data['date_str'] = 'от ' . date('d.m.Y', strtotime($data['date'])) . ' года';
        $data['post'] = 'Водитель';
        $data['alko'] = $data['alcometer_result'] . ' мг\л';

        //TODO: фикс для протокола, при необходимости - можно привести к 1 шаблону
        $view = 'docs.exports.' . $type;
        if (view()->exists("docs.exports.$type-new")) {
            $view = "docs.exports.$type-new";
        }

        $file = Pdf::loadView($view, $data);

        return response()
            ->make($file->output(), 200)
            ->header('Content-Type', 'application/pdf');
    }

    public function setPdf(Request $request, $type, $anketa_id)
    {
        $form = Form::find($anketa_id);
        $request->validate([
            'pdf' => ['required', 'mimes:pdf']
        ]);

        if (!$form) {
            return response('Осмотр не найден');
        }

        $pdf = $request->file('pdf');
        if (!$pdf) {
            return response('Файл не найден');
        }

        $path = Storage::disk('public')->putFileAs($type, $pdf, 'Документ осмотра №' . $anketa_id . '.pdf');
        $form->details->update([
            $type . '_path' => $path
        ]);

        return back();
    }

    private function sendEvent(string $actionType)
    {
        event(new ClientDocumentRequest(Auth::user(), $actionType));
    }
}
