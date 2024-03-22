<?php

namespace App\Http\Controllers;

use App\Anketa;
use App\Company;
use App\Driver;
use App\Point;
use App\Services\DocDataService;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Storage;

class DocsController extends Controller
{
    public function Get (Request $request, DocDataService $service)
    {
        $formId = $request->anketa_id;
        $type = $request->type;

        if (!view()->exists("docs.$type")) {
            return view("docs.default");
        }

        /** @var Anketa $form */
        $form = Anketa::find($formId);

        if (empty($form)) {
            return view("docs.undefined");
        }

        $data = $service->get($form);
        $data['type'] = $type;

        return view("docs.$type", $data);
    }

    public function update(Request $request, $type)
    {
        $anketa = Anketa::find($request->id);

        if (!$anketa) {
            return response()->json(['message' => 'Осмотр не найден']);
        }

        $data = array_merge($anketa->toArray(), $request->all());
        $pdf = Pdf::loadView('docs.exports.' . $type, $data);
        $path = $type . '/Документ осмотра №' . $request->id . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        $anketa->update([
            $type . '_path' => $path
        ]);
    }

    public function delete(Request $request, $type, $anketa_id)
    {
        Anketa::find($anketa_id)->update([
            $type. '_path' => null
        ]);

        return back();
    }

    public function getPdf(Request $request, $type, $formId, DocDataService $service)
    {
        $form = Anketa::find($formId);

        if (!$form) {
            return response('Осмотр не найден');
        }

        $path = $form[$type . '_path'];
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
        $data['alko'] = $data['alko_description'];

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
        $anketa = Anketa::find($anketa_id);
        $request->validate([
            'pdf' => ['required', 'mimes:pdf']
        ]);

        if (!$anketa) {
            return response('Осмотр не найден');
        }

        $pdf = $request->file('pdf');
        if (!$pdf) {
            return response('Файл не найден');
        }

        $path = Storage::disk('public')->putFileAs($type, $pdf, 'Документ осмотра №' . $anketa_id . '.pdf');
        $anketa->update([
            $type . '_path' => $path
        ]);

        return back();
    }
}
