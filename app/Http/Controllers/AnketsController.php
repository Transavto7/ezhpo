<?php

namespace App\Http\Controllers;

use App\Actions\Anketa\CreateFormHandlerFactory;
use App\Actions\Anketa\CreateSdpoFormHandler;
use App\Actions\Anketa\TrashFormHandler;
use App\Actions\Anketa\UpdateFormHandler;
use App\Anketa;
use App\Company;
use App\DDates;
use App\Driver;
use App\Enums\FormTypeEnum;
use App\Point;
use App\Settings;
use App\Traits\UserEdsTrait;
use App\User;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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

    public function Trash (Request $request, TrashFormHandler $handler)
    {
        $id = $request->id;
        $action = $request->action;
        $anketa = Anketa::find($id);

        if($anketa) {
            $saved = $handler->handle($anketa, $action);

            if($saved) {
                return redirect(url()->previous());
            }
        }

        return abort(403);
    }

    public function MassTrash(Request $request, TrashFormHandler $handler)
    {
        $ids = $request->input('ids') ?? [];
        $action = $request->input('action');
        $notDeletedAnkets = [];

        foreach ($ids as $id) {
            try {
                $anketa = Anketa::findOrFail($id);

                if ($anketa && !$anketa->deleted_at) {
                    $handler->handle($anketa, $action);
                }
            } catch (Throwable $exception) {
                $notDeletedAnkets[] = $id;
            }
        }

        if (count($notDeletedAnkets)) {
            session()->flash('not_deleted_ankets', $notDeletedAnkets);
        }

        return response()->json();
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

    public function ChangePakQueue(Request $request, $id, $admitted): RedirectResponse
    {
        $allowedAdmitted = ['Допущен', 'Не идентифицирован', 'Не допущен'];
        if (!in_array($admitted, $allowedAdmitted)) {
            return back()->with('error', 'Недопустимый результат осмотра');
        }

        /** @var User $user */
        $user = Auth::user();

        $form = Anketa::find($id);

        if (!$form) {
            return back()->with('error', 'Осмотр не найден');
        }

        if ($form->type_anketa !== FormTypeEnum::PAK_QUEUE) {
            return back()->with('error', 'Осмотр не находится в очереди утверждения');
        }

        $form->type_anketa = 'medic';
        $form->flag_pak = 'СДПО Р';
        $form->admitted = $admitted;

        if ($form->admitted === 'Не идентифицирован') {
            $form->comments = Settings::setting('not_identify_text') ?? 'Водитель не идентифицирован';
        }

        $form->user_id = $user->id;
        $form->user_name = $user->name;
        $form->operator_id = $user->id;
        $form->user_eds = $user->eds;
        $form->user_validity_eds_start = $user->validity_eds_start;
        $form->user_validity_eds_end = $user->validity_eds_end;

        $form->save();

        /**
         * ОТПРАВКА SMS
         */
        if ($admitted === 'Не допущен') {
            $company = Company::query()->where('hash_id', $form->company_id)->first();
            $driver = Driver::query()->where('hash_id', $form->driver_id)->first();

            $phoneToCall = Settings::setting('sms_text_phone');
            $message = Settings::setting('sms_text_driver') . " $driver->fio . $phoneToCall";

            $sms = new SmsController();
            $sms->sms($company->where_call, $message);
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

    public function Update(Request $request, UpdateFormHandler $handler): RedirectResponse
    {
        DB::beginTransaction();

        $id = $request->id;

        try {
            //TODO: нет проверки на существование формы
            $form = Anketa::find($id);

            $handler->handle($form, $request->all(), Auth::user());

            $referer = $request->input('REFERER');
            if ($referer) {
                $response = redirect( $referer );
            } else {
                $response = redirect(route('forms.get', [
                    'id' => $id,
                    'msg' => 'Осмотр успешно обновлён!'
                ]));
            }

            DB::commit();
        } catch (Throwable $exception) {
            $response = redirect(route('forms.get', [
                'id' => $id,
                'errors' => [$exception->getMessage()],
            ]));

            DB::rollBack();
        }

        return $response;
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
            ];

            DB::rollBack();
        }

        $responseData['type'] = $formType;
        $responseData['is_dop'] = $responseData['is_dop'] ?? $request->input('is_dop', 0);

        return redirect()->route('forms', $responseData);
    }

    /**
     * @deprecated
     * API ROUTE FOR SDPO
     */
    public function ApiAddForm(Request $request, CreateSdpoFormHandler $handler): JsonResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->all();

            if ($request->hasFile('photos')) {
                $photos = $request->file('photos');
                $photosPaths = [];

                foreach($photos as $photo) {
                    $photosPaths[] = Storage::disk('public')
                        ->putFile('ankets', $photo);
                }

                $data['photos'] = implode(',', $photosPaths);
            }

            $responseData = $handler->handle($data, $request->user('api'));

            DB::commit();

            Log::channel('deprecated-api')->info(json_encode(
                [
                    'request' => $request->all(),
                    'ip' => $request->getClientIp() ?? null,
                    'response' => $responseData
                ]
            ));

            return response()->json(response()->json($responseData));
        } catch (Throwable $exception) {
            DB::rollBack();

            $responseData = [
                'errors' => [$exception->getMessage()],
            ];

            Log::channel('deprecated-api')->info(json_encode(
                [
                    'request' => $request->all(),
                    'ip' => $request->getClientIp() ?? null,
                    'response' => $responseData
                ]
            ));

            return response()->json(response()->json($responseData), 500);
        }
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
            'user' => $user,
            'validity' => UserEdsTrait::getValidityString()
        ]);

        $response = response()->make($pdf->output(), 200);
        $response->header('Content-Type', 'application/pdf');
        return $response;
    }
}
