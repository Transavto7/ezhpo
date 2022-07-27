<?php

namespace App\Http\Controllers;

use App\Anketa;
use App\Company;
use App\Point;
use Illuminate\Http\Request;

class DocsController extends Controller
{
    public function Get (Request $request)
    {
        $anketa_id = $request->anketa_id;
        $type = $request->type;
        $user = auth()->user();

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
                $data['town'] = $point->town->name;
            }
        }

        if(view()->exists("docs.$type")) {
            return view("docs.$type", $data);
        }
    }
}
