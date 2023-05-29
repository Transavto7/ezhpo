<?php

namespace App\Http\Controllers;

use App\Anketa;
use App\Company;
use App\Driver;
use App\Point;
use App\Req;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Storage;

class DocsController extends Controller
{
    public function Get (Request $request)
    {
        $anketa_id = $request->anketa_id;
        $type = $request->type;

        $data = [
            'type' => $type,
            'anketa_id' => $anketa_id,

            'driver_fio' => '',
            'driver_yb' => '',
            'driver_pv' => '',

            'user_name' => '',
            'user_post' => '',
            'user_fio' => '',
            'user_company' => '',
            'date' => '',
            'town' => '',
            'drugs' => false,
            'alko' => false
        ];

        $a = \App\Anketa::find($anketa_id);

        if($a) {
            $data['user_post'] = ProfileController::getUserRole(true, $a->user_id);

            $fields = new Anketa();
            $fields = $fields->fillable;

            foreach($fields as $field) {
                $data[$field] = $a[$field];
            }

            $driver = Driver::where('hash_id', $a->driver_id)->first();
            $data['driver'] = $driver;

            if ($a->test_narko === 'Положительно') {
                $data['drugs'] = true;
            }

            if ($a->proba_alko === 'Положительно') {
                $data['alko'] = true;
            }

            if($a->company_id) {
                $c = Company::where('hash_id', $a->company_id)->first();

                if($c) {
                    $c = Point::find($c->pv_id);

                    if($c) {
                        $data['driver_pv'] = $c->name;
                    }
                }
            }

            if ($a->pv_id) {
                $point = Point::where('name', $a->pv_id)->with('town')->first();
                if ($point) {
                    $data['town'] = $point->town->name;
                }
            }
        }

        if(view()->exists("docs.$type")) {
            return view("docs.$type", $data);
        }
    }

    public function update(Request $request, $type) {
        $anketa = Anketa::find($request->id);

        if (!$anketa) {
            return response()->json(['message' => 'Осмотр не найден']);
        }

        $data = array_merge($request->all(), $anketa->toArray());
        $pdf = Pdf::loadView('docs.exports.' . $type, $data);
        $path = $type . '/Документ осмотра №' . $request->id . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        $anketa->update([
            $type . '_path' => $path
        ]);
    }

    public function delete(Request $request, $type, $anketa_id) {
        Anketa::find($anketa_id)->update([
            $type. '_path' => null
        ]);

        return back();
    }

    public function getPdf(Request $request, $type, $anketa_id) {
        $anketa = Anketa::find($anketa_id);

        if (!$anketa) {
            return response('Осмотр не найден');
        }

        $path = $anketa[$type . '_path'];
        if (Storage::disk('public')->exists($path)) {
            $file = Storage::disk('public')->get($path);
            $response = response()->make($file, 200);
            $response->header('Content-Type', 'application/pdf');
            return $response;
        } else {
            $data = [
                'type' => $type,
                'anketa_id' => $anketa_id,

                'driver_fio' => '',
                'driver_yb' => '',
                'driver_pv' => '',

                'user_name' => '',
                'user_post' => '',
                'user_fio' => '',
                'user_company' => '',
                'date' => '',
                'town' => '',
                'drugs' => false,
                'alko' => false
            ];

            if($anketa) {
                $data['user_post'] = ProfileController::getUserRole(true, $anketa->user_id);

                $fields = new Anketa();
                $fields = $fields->fillable;

                foreach($fields as $field) {
                    $data[$field] = $anketa[$field];
                }

                if ($anketa->test_narko === 'Положительно') {
                    $data['drugs'] = true;
                }

                if ($anketa->proba_alko === 'Положительно') {
                    $data['alko'] = true;
                }

                $driver = Driver::where('hash_id', $anketa->driver_id)->first();
                $data['driver'] = $driver;

                if($anketa->company_id) {
                    $c = Company::where('hash_id', $anketa->company_id)->first();

                    if($c) {
                        $c = Point::find($c->pv_id);

                        if($c) {
                            $data['driver_pv'] = $c->name;
                        }
                    }
                }

                if ($anketa->pv_id) {
                    $point = Point::where('name', $anketa->pv_id)->with('town')->first();
                    if ($point) {
                        $data['town'] = $point->town->name;
                    }
                }
            }

            $pdf = Pdf::loadView('docs.exports.' . $type . '-new', $data);
            $response = response()->make($pdf->output(), 200);
            $response->header('Content-Type', 'application/pdf');
            return $response;
        }
    }

    public function setPdf(Request $request, $type, $anketa_id) {
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
