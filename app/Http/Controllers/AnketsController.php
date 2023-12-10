<?php

namespace App\Http\Controllers;

use App\Actions\Anketa\CreateFormHandlerFactory;
use App\Anketa;
use App\Car;
use App\Company;
use App\DDates;
use App\Driver;
use App\Point;
use App\User;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class AnketsController extends Controller
{
    public function Delete (Request $request)
    {
        $id = $request->id;

        if(Anketa::find($id)->delete()) {
            return redirect(url()->previous());
        }

        return abort(403);
    }

    public function Trash (Request $request)
    {
        $id = $request->id;
        $action = $request->action;
        $anketa = Anketa::find($id);

        if($anketa) {
            $anketa->in_cart = $action;
            if ($anketa->type_anketa === 'medic' && $anketa->driver_id) {
                $driver = Driver::where('hash_id', $anketa->driver_id)->first();

                if ($driver && $driver->end_of_ban) {
                    $last = Anketa::orderBy('created_at', 'desc')
                        ->where('driver_id', $anketa->driver_id)
                        ->select('driver_id', 'created_at', 'id')->first();

                    if ($last->id === $anketa->id) {
                        $driver->end_of_ban = null;
                        $driver->save();
                    }
                }
            }
            $anketa->deleted_id = user()->id;
            $anketa->deleted_at = \Carbon\Carbon::now();

            if($anketa->save()) {
                return redirect(url()->previous());
            }
        }

        return abort(403);
    }

    public function Get (Request $request)
    {
        $id = $request->id;
        $anketa = Anketa::where('id', $id)->first();

        $data = [];

        foreach ($anketa->fillable as $f) {
            $data[$f] = $anketa[$f];
        }

        $point = $anketa->pv_id;
        $points = Point::getAll();

        $iController = new IndexController();

        $company_fields = $iController->elements['Driver']['fields']['company_id'];
        $company_fields['getFieldKey'] = 'name';

        // Дефолтные значения
        $data['title'] = 'Редактирование осмотра';

        if ($anketa->date) {
            $data['default_current_date'] = date('Y-m-d\TH:i', strtotime($anketa->date)); // date('Y-m-d\TH:i')
        }

        $data['date'] = $anketa->date;
        $data['proba_alko'] = $anketa->proba_alko;
        $data['test_narko'] = $anketa->test_narko;
        $data['med_view'] = $anketa->med_view;
        $data['default_point'] = $anketa->point_id ?? $point;
        $data['points'] = $points;
        $data['is_dop'] = $anketa->is_dop;
        $data['anketa_view'] = 'profile.ankets.' . $anketa->type_anketa;
        $data['default_pv_id'] = $anketa->point_id ?? $anketa->pv_id;
        $data['anketa_route'] = 'forms.update';
        $data['company_fields'] = $company_fields;

        return view('profile.anketa', $data);
    }

    public function ChangePakQueue(Request $request, $id, $admitted)
    {
        $anketa = Anketa::find($id);

        if($anketa) {
            $anketa->type_anketa = 'medic';
            $anketa->flag_pak = 'СДПО Р';
            $anketa->admitted = $admitted;
            $anketa->operator_id = $request->user()->id;
            $anketa->save();
        }

        return back();
    }

    public function ChangeResultDop ($id, $result_dop)
    {
        $anketa = Anketa::find($id);
        $hourdiff = 1;
        $anketaDublicate = [
            'id' => 0,
            'date' => ''
        ];

        if($anketa->type_anketa === 'medic') {
            $anketaMedic = Anketa::where('driver_id', $anketa->driver_id)
                ->where('type_anketa', 'medic')
                ->where('type_view', $anketa->type_view)
                ->where('in_cart', 0)
                ->orderBy('date', 'desc')
                ->get();

            foreach($anketaMedic as $aM) {
                if (!$aM->date || $aM->id === $anketa->id || ($aM->is_dop && $aM->result_dop == null)) {
                    continue;
                }

                $hourdiff_check = round((Carbon::parse($anketa->date)->timestamp - Carbon::parse($aM->date)->timestamp)/60, 1);

                if($hourdiff_check < 1 && $hourdiff_check >= 0) {
                    $anketaDublicate['id'] = $aM->id;
                    $anketaDublicate['date'] = $aM->date;
                    $hourdiff = $hourdiff_check;
                }
            }
        } else if($anketa->type_anketa === 'tech') {
            $anketasTech = Anketa::where('car_id', $anketa->car_id)
                ->where('type_anketa', 'tech')
                ->where('type_view', $anketa->type_view ?? '')
                ->where('in_cart', 0)
                ->orderBy('date', 'desc')
                ->get();

            foreach($anketasTech as $aT) {
                if (!$aT->date || $aT->id === $anketa->id || ($aT->is_dop && $aT->result_dop == null)) {
                    continue;
                }

                $hourdiff_check = round((Carbon::parse($anketa->date)->timestamp - Carbon::parse($aT->date)->timestamp)/60, 1);

                if($hourdiff_check < 1 && $hourdiff_check >= 0) {
                    $anketaDublicate['id'] = $aT->id;
                    $anketaDublicate['date'] = $aT->date;
                    $hourdiff = $hourdiff_check;
                }
            }
        }

        if($hourdiff < 1 && $hourdiff >= 0) {
            return back()->with('error', "Найден дубликат осмотра (ID: $anketaDublicate[id], Дата: $anketaDublicate[date])");
        }

        if ($anketa->type_anketa === 'tech') {
            if (!$anketa || !$anketa->date || !$anketa->car_id) {
                return back()->with('error', 'Указаны не полные данные осмотра');
            }

            if($anketa->number_list_road === null && $anketa->type_anketa !== 'medic') {
                // Генерируем номер ПЛ
                $findCurrentPL = Anketa::where('created_at', '>=', Carbon::today())->where('in_cart', 0)->get();
                $anketa->number_list_road = $anketa->car_id . '-' . date('d.m.Y', strtotime($anketa['date']));
            }
        }

        $anketa->result_dop = $result_dop;
        $anketa->save();

        return back();
    }

    public function Update (Request $request)
    {
        $id = $request->id;
        $anketa = Anketa::find($id);
        $hourdiff = 1;
        $anketaDublicate = [
            'id' => 0,
            'date' => ''
        ];

        $data = $request->all();

        $REFERER = $data['REFERER'] ?? '';

        if(isset($data['REFERER'])) {
            unset($data['REFERER']);
        }

        $point = Point::where('id', $data['pv_id'])->first();
        $data['pv_id'] = $point->name;
        $data['point_id'] = $point->id;
        $type_anketa = $data['type_anketa'];

        if(isset($data['anketa'])) {
            if($anketa->type_anketa === 'medic' && (!$anketa->is_dop || $anketa->result_dop != null)) {
                $anketaMedic = Anketa::where('driver_id', $data['driver_id'])
                    ->where('type_anketa', 'medic')
                    ->where('type_view', $data['anketa'][0]['type_view'])
                    ->where('in_cart', 0)
                    ->orderBy('date', 'desc')
                    ->get();

                foreach($anketaMedic as $aM) {
                    if (!$aM->date || $aM->id === $anketa->id || ($aM->is_dop && $aM->result_dop == null)) {
                        continue;
                    }

                    $hourdiff_check = round((Carbon::parse($data['anketa'][0]['date'])->timestamp - Carbon::parse($aM->date)->timestamp)/60, 1);

                    if($hourdiff_check < 1 && $hourdiff_check >= 0) {
                        $anketaDublicate['id'] = $aM->id;
                        $anketaDublicate['date'] = $aM->date;
                        $hourdiff = $hourdiff_check;
                    }
                }
            } else if($anketa->type_anketa === 'tech' && (!$anketa->is_dop || $anketa->result_dop != null)) {
                $anketasTech = Anketa::where('car_id', $data['anketa'][0]['car_id'])
                    ->where('type_anketa', 'tech')
                    ->where('type_view', $data['anketa'][0]['type_view'] ?? '')
                    ->where('in_cart', 0)
                    ->orderBy('date', 'desc')
                    ->get();

                foreach($anketasTech as $aT) {
                    if (!$aT->date || $aT->id === $anketa->id || ($aT->is_dop && $aT->result_dop == null)) {
                        continue;
                    }

                    $hourdiff_check = round((Carbon::parse($data['anketa'][0]['date'])->timestamp - Carbon::parse($aT->date)->timestamp)/60, 1);

                    if($hourdiff_check < 1 && $hourdiff_check >= 0) {
                        $anketaDublicate['id'] = $aT->id;
                        $anketaDublicate['date'] = $aT->date;
                        $hourdiff = $hourdiff_check;
                    }
                }
            }

            if($hourdiff < 1 && $hourdiff >= 0) {
                return redirect(route('forms.get', [
                    'id' => $anketa->id,
                    'errors' => ["Найден дубликат осмотра (ID: $anketaDublicate[id], Дата: $anketaDublicate[date])"]
                ]));
            }

            foreach($data['anketa'][0] as $daK => $daV) {
                $company_id = null;

                switch($daK) {
                    case 'driver_id':

                        if($daV) {
                            $driver = Driver::where('hash_id', $daV)->first();

                            if($driver) {

                                $data['driver_fio'] = $driver->fio;
                                $data['driver_group_risk'] = $driver->group_risk;
                                $data['driver_gender'] = $driver->gender;
                                $data['driver_year_birthday'] = $driver->year_birthday;

                                $company_id = $driver->company_id;
                            }
                        }

                        break;

                    case 'car_id':

                        if($daV) {
                            $car = Car::where('hash_id', $daV)->first();

                            if($car) {
                                $data['car_mark_model'] = $car->mark_model;
                                $data['car_gos_number'] = $car->gos_number;

                                $company_id = $car->company_id;
                            }
                        }

                        break;
                }

                if($company_id) {
                    $Company = Company::where('id', $company_id)->first();

                    if($Company) {
                        $data['company_id'] = $Company->hash_id;
                        $data['company_name'] = $Company->name;
                    } else {
                        $data['company_id'] = '';
                        $data['company_name'] = '';
                    }
                }

                $data[$daK] = $daV;
            }
            unset($data['anketa']);
        }

        unset($data['_token']);
        foreach($data as $dK => $dV) {
            $company_id = null;

            switch($dK) {
                case 'driver_id':

                    $driver = Driver::where('hash_id', $dV)->first();

                    if($driver) {
                        $anketa['driver_fio'] = $driver->fio;
                        $anketa['driver_group_risk'] = $driver->group_risk;
                        $anketa['driver_gender'] = $driver->gender;
                        $anketa['driver_year_birthday'] = $driver->year_birthday;

                        $company_id = $driver->company_id;
                    }

                    break;

                case 'car_id':

                    $car = Car::where('hash_id', $dV)->first();

                    if($car) {
                        $anketa['car_mark_model'] = $car->mark_model;
                        $anketa['car_gos_number'] = $car->gos_number;

                        $company_id = $car->company_id;
                    }

                    break;
            }

            if($company_id) {
                $Company = Company::where('id', $company_id)->first();

                if($Company) {
                    $anketa['company_id'] = $Company->hash_id;
                    $anketa['company_name'] = $Company->name;
                } else {
                    $anketa['company_id'] = '';
                    $anketa['company_name'] = '';
                }
            }

            $timezone      = $user->timezone ?? 3;
            $diffDateCheck = Carbon::parse($anketa['created_at'])->addHours($timezone)->diffInMinutes($data['date'] ?? null);

            if ($diffDateCheck <= 60 * 12 && $anketa['date'] ?? null) {
                $anketa['realy'] = 'да';
            } else {
                $anketa['realy'] = 'нет';
            }

            $anketa[$dK] = $dV;
        }

        $anketa->save();

        if($anketa->connected_hash) {
            $anketaCopy = Anketa::where('connected_hash', $anketa->connected_hash)->where('type_anketa', '!=', $anketa->type_anketa)->first();

            if($anketaCopy) {

                foreach($anketa->fillable as $i => $key) {
                    if($key !== 'type_anketa' && $key !== 'id' && $key !== 'created_at' && $key !== 'updated_at') {
                        $anketaCopy->$key = $anketa[$key];
                    }
                }

                $anketaCopy->save();
            }
        }

        if($REFERER) {
            return redirect( $REFERER );
        } else {
            return redirect(route('forms.get', [
                'id' => $id,
                'msg' => 'Осмотр успешно обновлён!'
            ]));
        }
    }

    public static function ddateCheck ($dateAnketa, $dateModel, $id)
    {
        $dateCheckModel = app("App\\$dateModel")->find($id);
        $dateCheck = DDates::where('item_model', $dateModel)->get();

        $redDates = [];

        if($dateCheck && $dateCheckModel) {
            foreach($dateCheck as $dateCheckItem) {
                $fieldDateCheck = $dateCheckItem->field;

                if(isset($dateCheckModel[$fieldDateCheck])) {
                    $fieldDateItemValue = $dateCheckModel[$fieldDateCheck];

                    $dateAction = $dateCheckItem->action . ' ' . $dateCheckItem->days . ' days';

                    $dateCheckWithAnketa = date('Y-m-d', strtotime($fieldDateItemValue . ' ' . $dateAction));
                    $anketaDate = date('Y-m-d', strtotime($dateAnketa));

                    if($dateCheckWithAnketa <= $anketaDate) {
                        $redDates[$fieldDateCheck] = [
                            'value' => $fieldDateItemValue,
                            'item_model' => $dateCheckItem->item_model,
                            'item_id' => $dateCheckModel->id,
                            'item_field' => $fieldDateCheck
                        ];
                    }
                }
            }
        }

        return $redDates;
    }

    public function AddForm(Request $request, CreateFormHandlerFactory $factory): RedirectResponse
    {
        DB::beginTransaction();

        $formType = $request->input('type_anketa');

        try {
            // TODO: добавить время действия
            session(['anketa_pv_id' => [
                'value' => $request->get('pv_id', 0),
                'expired' => date('d.m')
            ]]);

            $handler = $factory->make($formType);

            $responseData = $handler->handle($request->all(), Auth::user());

            DB::commit();
        } catch (Throwable $exception) {
            $responseData = [
                'errors' => [$exception->getMessage()],
                'type' => $formType
            ];

            DB::rollBack();
        }

        return redirect()->route('forms', $responseData);
    }

    public function print(Request $request, $id) {
       $anketa = Anketa::find($id);

       if (!$anketa) {
           return abort(404);
       }

       $terminal = User::find($anketa->terminal_id);
       $stamp = null;

        if ($terminal) {
            $stamp = $terminal->stamp;
        }

        $user = User::find($anketa->user_id);

        $pdf = Pdf::loadView('docs.print', [
            'anketa' => $anketa,
            'stamp' => $stamp,
            'user' => $user
        ]);

        $response = response()->make($pdf->output(), 200);
        $response->header('Content-Type', 'application/pdf');
        return $response;
    }
}
