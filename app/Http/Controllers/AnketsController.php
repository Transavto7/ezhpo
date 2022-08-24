<?php

namespace App\Http\Controllers;

use App\Anketa;
use App\Car;
use App\Company;
use App\DDates;
use App\Driver;
use App\Notify;
use App\Point;
use App\Settings;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Routing\Matcher\RedirectableUrlMatcher;

class AnketsController extends Controller
{
    public function Delete (Request $request)
    {
        $id = $request->id;

        if(Anketa::find($id)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
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
            $anketa->deleted_id = user()->id;
            $anketa->deleted_at = \Carbon\Carbon::now();

            if($anketa->save()) {
                return redirect($_SERVER['HTTP_REFERER']);
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

        $point = Point::getPointText($anketa->pv_id);
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
        $data['default_point'] = $point;
        $data['points'] = $points;
        $data['is_dop'] = $anketa->is_dop;
        $data['anketa_view'] = 'profile.ankets.' . $anketa->type_anketa;
        $data['default_pv_id'] = $anketa->pv_id;
        $data['anketa_route'] = 'forms.update';
        $data['company_fields'] = $company_fields;

        return view('profile.anketa', $data);
    }

    public function ChangePakQueue ($id, $admitted)
    {
        $anketa = Anketa::find($id);

        if($anketa) {
            $anketa->type_anketa = 'medic';
            $anketa->flag_pak = 'СДПО Р';
            $anketa->admitted = $admitted;
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
                ->whereIn('type_anketa', ['tech', 'dop'])
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

        if($anketa) {
            $anketa->result_dop = $result_dop;
            $anketa->save();
        }

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

        $REFERER = isset($data['REFERER']) ? $data['REFERER'] : '';

        if(isset($data['REFERER'])) {
            unset($data['REFERER']);
        }

        $data['pv_id'] = Point::where('id', $data['pv_id'])->first()->name;
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
                    ->whereIn('type_anketa', ['tech', 'dop'])
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

    public function savePakForm ($anketa, $comments = '') {
        if(isset($anketa['is_pak'])) {
            $anketa['type_anketa'] = 'pak';
            $anketa['comments'] = $comments;

            Anketa::create($anketa);
        }
    }

    public function AddForm (Request $request, $isApiRoute = false)
    {
        $user = ($isApiRoute) ? $request->user('api') : Auth::user();
        $sms = new SmsController();

        $data = $request->all();
        $d_id = $request->get('driver_id', 0); // Driver
        $pv_id = $request->get('pv_id', 0);

        function mt_rand_float($min, $max, $countZero = '0') {
            $countZero = +('1'.$countZero);
            $min = floor($min*$countZero);
            $max = floor($max*$countZero);
            $rand = mt_rand($min, $max) / $countZero;
            return $rand;
        }

        // TODO: добавить время действия
        session(['anketa_pv_id' => [
            'value' => $pv_id,
            'expired' => date('d.m')
        ]]);

        // Проверяем дефолтные значения
        $defaultDatas = [
            'tonometer' => rand(118,129) .'/'. rand(70,90),
            't_people' => mt_rand_float(35.9,36.7),
            'date' => date('Y-m-d H:i:s')
        ];

        $test_narko = $data['test_narko'] ?? 'Отрицательно';
        $proba_alko = $data['proba_alko'] ?? 'Отрицательно';
        $is_dop = $data['is_dop'] ?? 0;

        // Выставляем оптимальные параметры
        unset($data['_token']);

        // ЕСЛИ ЗАПРОС С API/ПАК
        if(isset($data['user_id']) && $isApiRoute) {
            $user = User::find($data['user_id']);
        }

        $data['user_id'] = $user->id;

        // Фотографии с ПАК
        if($request->hasFile('photos')) {
            // Парсим файлы
            $photos = $request->file('photos');
            $photos_path = '';

            foreach($photos as $photoIndex => $photo) {
                $file_path = Storage::disk('public')->putFile('ankets', $photo);

                $photos_path .= ($photoIndex == 0 ? '' : ',') . $file_path;
            }

            $data['photos'] = $photos_path;
        }

        // Анкета
        if(isset($data['anketa'])) {
            // Клонируем анкету
            $createdAnketas = [];
            $createdAnketasDataResponseApi = [];
            $data_anketa = $data['anketa'];
            $errorsAnketa = array();
            $dopAnketas = [];
            $Driver = Driver::where('hash_id', $d_id)->first();
            $cars = [];

            // Только обычные осмотры валидируем
            if(($data['is_dop'] ?? 0) != 1){

                //================== VALIDATE company/driver/car ===================//
                // tech
                if($data['type_anketa'] === 'tech'){
                    if($data['is_dop'] == 1){
                        if(!Company::where('hash_id', $data['company_id'])->count()){
                            $errorsAnketa[] = 'Не найдена компания.';
                        }
                    }
                    if(!Car::where('hash_id', $data['anketa'][0]['car_id'])->count()){
                        $errorsAnketa[] = 'Не найдена машина.';
                    }
                    if(!Driver::where('hash_id', $data['driver_id'])->count()){
                        $errorsAnketa[] = 'Не найден водитель.';
                    }
                }

                // medic
                if($data['type_anketa'] === 'medic'){
                    if($data['is_dop'] == 1){
                        if(!Company::where('hash_id', $data['company_id'])->count()){
                            $errorsAnketa[] = 'Не найдена компания.';
                        }
                    }
                    if(!Driver::where('hash_id', $data['driver_id'])->count()){
                        $errorsAnketa[] = 'Не найден водитель.';
                    }
                }

                // Журнал снятия отчетов с карт
                if($data['type_anketa'] === 'report_cart'){
                    if(!Driver::where('hash_id', $data['driver_id'])->count()){
                        $errorsAnketa[] = 'Не найден водитель.';
                    }
                }

                // Журнал печати путевых листов
                if($data['type_anketa'] === 'pechat_pl'){
                    if(!Driver::where('hash_id', $data['anketa'][0]['driver_id'])->count()){
                        $errorsAnketa[] = 'Не найден водитель.';
                    }
                }

                // Журнал ПЛ
                if($data['type_anketa'] === 'Dop'){
                    if($data['driver_id']){
                        if(!Driver::where('hash_id', $data['driver_id'])->count()){
                            $errorsAnketa[] = 'Не найден водитель.';
                        }
                    }else{
                        $errorsAnketa[] = 'Не указана машина.';
                    }
                    if($data['car_id']){
                        if(!Car::where('hash_id', $data['car_id'])->count()){
                            $errorsAnketa[] = 'Не найдена машина.';
                        }
                    }else{
                        $errorsAnketa[] = 'Не указана машина.';
                    }
                }

                // Журнал инструктажей по БДД
                if($data['type_anketa'] === 'bdd'){
                    if($data['driver_id']){
                        if(!Driver::where('hash_id', $data['driver_id'])->count()){
                            $errorsAnketa[] = 'Не найден водитель.';
                        }
                    }else{
                        $errorsAnketa[] = 'Не указана машина.';
                    }
                }

                if(count($errorsAnketa)){
                    return redirect()->route('forms', [
                        'errors' => $errorsAnketa,
                        'type' => $data['type_anketa']
                    ]);
                }
            }

            if (isset($data['car_id'])) {
                $cars[] = $data['car_id'];
            }

            foreach ($data_anketa as $anketa) {
                $cars[] = $anketa['car_id'] ?? 0;
            }

            if ($data['type_anketa'] === 'medic' || $data['type_anketa'] === 'pak') {
                $anketasMedic = Anketa::where('driver_id', $d_id)
                    ->where('type_anketa', 'medic')
                    ->where('in_cart', 0)
                    ->orderBy('date', 'desc')
                    ->get();
            } else if ($data['type_anketa'] === 'tech' || $data['type_anketa'] === 'vid_pl') {
                $anketasTech = Anketa::whereIn('car_id', $cars)
                    ->whereIn('type_anketa', ['tech', 'dop'])
                    ->where('in_cart', 0)
                    ->orderBy('date', 'desc')
                    ->get();
            }

            foreach($data_anketa as $anketa) {
                // Выделение красных дат
                $redDates = [];

                // ID автомобиля
                $c_id = $anketa['car_id'] ?? 0;

                $Car = Car::where('hash_id', $c_id)->first();

                // Тонометр
                $tonometer = $anketa['tonometer'] ?? $defaultDatas['tonometer'];

                if(!isset($anketa['med_view'])) {
                    $anketa['med_view'] = 'В норме';
                }

                /**
                 * Парсим данные в анкете, удаляем главную анкету и ставим актуальную
                 */
                unset($data['anketa']);
                foreach($data as $dk => $dv) {
                    $dv_item = $dv;

                    if(empty($dv_item) && isset($defaultDatas[$dk])) {
                        if($dk === 'date' && $is_dop) {

                        } else {
                            $dv_item = $defaultDatas[$dk];
                        }
                    }

                    $anketa[$dk] = $dv_item;
                }

                /**
                 * Проверяем дефолтные значения
                 */
                foreach($defaultDatas as $dfKey => $dfData) {
                    if(empty($anketa[$dfKey])) {
                        if($is_dop && $dfKey === 'date') {

                        } else {
                            $anketa[$dfKey] = $dfData;
                        }
                    }
                }

                /**
                 * ОЧЕРЕДЬ ПАК
                 */

                if($anketa['type_anketa'] == 'pak_queue') {
                    $notifyTo = new Notify();
                    $notifyTo->sendMsgToUsersFrom('role', '4', 'Новый осмотр в очереди СДПО');
                }

                /**
                 * Компания
                 */
                if(isset($anketa['company_id'])) {
                    $CompanyDop = Company::where('hash_id', $anketa['company_id'])->first();

                    if($CompanyDop) {
                        $anketa['company_id'] = $CompanyDop->hash_id;
                        $anketa['company_name'] = $CompanyDop->name;
                    }
                }

                if (isset($anketa['driver_id'])) {
                    $DriverDop = Driver::where('hash_id', $anketa['driver_id'])->first();

                    if ($DriverDop) {
                        $anketa['driver_id'] = $DriverDop->hash_id;
                        $anketa['driver_fio'] = $DriverDop->fio;
                    }
                }

                /**
                 * Проверка водителя по: тесту наркотиков, возрасту
                 */
                if($d_id || isset($Driver)) {
                    if(!Driver::DriverChecker($d_id, $tonometer, $test_narko, $proba_alko) && !in_array($anketa['type_anketa'], ['bdd', 'pechat_pl', 'vid_pl', 'report_cart'])) {

                        if($anketa['type_anketa'] !== 'tech') {
                            $errMsg = 'Водитель не найден';

                            $errorDriverFind = [
                                'errors' => [$errMsg]
                            ];

                            $this->savePakForm($anketa, $errMsg);

                            if($isApiRoute) {
                                return response()->json($errorDriverFind);
                            }

                            return redirect()->route('forms', $errorDriverFind);
                        } else {
                            $anketa['driver_id'] = 0;
                        }
                    } else {
                        $anketa['driver_fio'] = $Driver->fio;
                        $anketa['driver_group_risk'] = $Driver->group_risk;

                        if($Driver->dismissed === 'Да') {
                            $errMsg = 'Водитель уволен. Осмотр зарегистрирован. Обратитесь к менеджеру';

                            array_push($errorsAnketa, $errMsg);
                        }

                        /**
                         * Добавляем Компанию
                         */
                        if($Driver->company_id) {
                            $Company = Company::where('id', $Driver->company_id)->first();

                            if($Company) {

                                $anketa['company_id'] = $Company->hash_id;
                                $anketa['company_name'] = $Company->name;

                                if($Company->dismissed === 'Да') {
                                    $errMsg = 'Компания в черном списке. Необходимо связаться с руководителем!';

                                    array_push($errorsAnketa, $errMsg);

                                    continue;
                                }

                            } else {
                                $errMsg = "У Водителя не верно указано ID компании";

                                array_push($errorsAnketa, $errMsg);

                                $this->savePakForm($anketa, $errMsg);

                                continue;
                            }

                        } else {
                            $errMsg = "У Водителя не найдена компания";

                            array_push($errorsAnketa, $errMsg);

                            $this->savePakForm($anketa, $errMsg);

                            continue;
                        }
                    }
                }

                /**
                 * Проверка данных анкеты (добавляем в доп.осмотр или нет)
                 * Техосмотр:
                 *  - если ПЛ то добавляем доп
                 *
                 * Медосмотр:
                 *    - ID авто и номер ПЛ то добавляем доп
                 */
                $is_med_dop = $anketa['type_anketa'] === 'medic';
                $is_tech_dop = $anketa['type_anketa'] === 'tech';

                /**
                 * ПРОВЕРЯЕМ статус для поля "Заключение"
                 */
                $tonometer = explode('/', $anketa['tonometer']);
                if($proba_alko === 'Отрицательно' && ($test_narko === 'Отрицательно' || $test_narko === 'Не проводился')
                    && $anketa['med_view'] === 'В норме' && $anketa['t_people'] < 38 && $tonometer[0] < 150) {
                    $anketa['admitted'] = 'Допущен';
                } else {
                    $anketa['admitted'] = 'Не допущен';
                }

                /**
                 * ПРОВЕРЯЕМ СТАТУС для поля "Заключение" - от ПАК
                 */
                if(isset($anketa['sleep_status']) && isset($anketa['people_status']) && isset($anketa['alcometer_result'])) {

                    if($anketa['sleep_status'] === 'Да' && $anketa['people_status'] === 'Да' && $anketa['alcometer_result'] <= 0) {
                        $anketa['admitted'] = 'Допущен';
                    } else {
                        $anketa['admitted'] = 'Не допущен';
                    }

                }

                if(!empty($d_id) || !empty($c_id) || !empty($anketa['number_list_road'])) {
                    /**
                     * <КОНТРОЛЬ-ДАТ>
                     */
                    $dateCheckModel = null;

                    if($anketa['type_anketa'] === 'medic') {
                        $dateCheckModel = $Driver;
                        $dateCheck = DDates::where('item_model', 'Driver')->get();
                    } else {
                        $dateCheckModel = $Car;
                        $dateCheck = DDates::where('item_model', 'Car')->get();
                    }

                    if($dateCheck && $dateCheckModel) {
                        foreach($dateCheck as $dateCheckItem) {
                            $fieldDateCheck = $dateCheckItem->field;

                            if(isset($dateCheckModel[$fieldDateCheck])) {
                                $fieldDateItemValue = $dateCheckModel[$fieldDateCheck];

                                $dateAction = $dateCheckItem->action . ' ' . $dateCheckItem->days . ' days';

                                $dateCheckWithAnketa = date('Y-m-d', strtotime($fieldDateItemValue . ' ' . $dateAction));
                                $anketaDate = date('Y-m-d', strtotime($anketa['date']));

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
                    /**
                     * </КОНТРОЛЬ-ДАТ>
                     */
                }

                /**
                 * Проверка на дубликат из ТЗ
                 *
                 * Мы должны дать техническую возможность внесение осмотров любой даты (год назад, месяц назад.
                 * При внесении осмотра, система должна смотреть, есть ли подобный.
                 *
                 * Например сегодня 13.02.21 в 09.00 до 10.00.
                 */
                $hourdiff = 1;
                $anketaDublicate = [
                    'id' => 0,
                    'date' => ''
                ];

                if(isset($anketasMedic) && !$anketa['is_dop']) {
                    $anketaMedic = $anketasMedic;

                    $count = 0;

                    foreach ($data_anketa as $aM) {
                        $hourdiff_check = round((Carbon::parse($anketa['date'])->timestamp - Carbon::parse($aM['date'])->timestamp)/60, 1);

                        if($hourdiff_check < 1 && $hourdiff_check >= 0) {
                            $count++;
                        }
                    }

                    if ($count > 1) {
                        $hourdiff = $hourdiff_check;
                        $anketaDublicate['date'] = $aM['date'];
                    }

                    foreach($anketaMedic as $aM) {
                        if (!$aM->date || ($aM->is_dop && $aM->result_dop == null)) {
                            continue;
                        }

                        $hourdiff_check = round((Carbon::parse($anketa['date'])->timestamp - Carbon::parse($aM->date)->timestamp)/60, 1);


                        if($hourdiff_check < 1 && $hourdiff_check >= 0) {
                            $anketaDublicate['id'] = $aM->id;
                            $anketaDublicate['date'] = $aM->date;
                            $hourdiff = $hourdiff_check;
                        }
                    }
                } else if (isset($anketasTech)) {
                    $anketaTech = $anketasTech;

                    /**
                     * Уволенный АВТО
                     */
                    if($Car) {
                        if($Car->dismissed === 'Да') {
                            $errMsg = 'Автомобиль уволен. Осмотр зарегистрирован. Обратитесь к менеджеру';

                            array_push($errorsAnketa, $errMsg);
                        }
                    }

                    // Если нет водителя И есть Авто - то ставим компанию из Авто

                    if(!isset($Driver->id) && $Car) {
                        $Company_Car = Company::where('id', $Car->company_id)->first();

                        if($Company_Car) {

                            $anketa['company_id'] = $Company_Car->hash_id;
                            $anketa['company_name'] = $Company_Car->name;

                            if($Company_Car->dismissed === 'Да') {
                                $errMsg = 'Компания в черном списке. Необходимо связаться с руководителем!';

                                array_push($errorsAnketa, $errMsg);

                                continue;
                            }
                        } else {
                            $errMsg = "У Автомобиля не найдена компания";

                            array_push($errorsAnketa, $errMsg);

                            $this->savePakForm($anketa, $errMsg);

                            continue;
                        }
                    }

                    if($anketaTech && !$anketa['is_dop']) {
                        $count = 0;

                        foreach ($data_anketa as $aT) {
                            if ($aT['car_id'] != $anketa['car_id']) {
                                continue;
                            }

                            $hourdiff_check = round((Carbon::parse($anketa['date'])->timestamp - Carbon::parse($aT['date'])->timestamp)/60, 1);

                            if($hourdiff_check < 1 && $hourdiff_check >= 0) {
                                $count++;
                            }
                        }

                        if ($count > 1) {
                            $hourdiff = $hourdiff_check;
                            $anketaDublicate['date'] = $aT['date'];
                        }

                        foreach($anketaTech as $aT) {
                            if (!$aT->date || ($aT->is_dop && $aT->result_dop == null)) {
                                continue;
                            }

                            $hourdiff_check = round((Carbon::parse($anketa['date'])->timestamp - Carbon::parse($aT->date)->timestamp)/60, 1);

                            if($hourdiff_check < 1 && $hourdiff_check >= 0) {
                                $anketaDublicate['id'] = $aT->id;
                                $anketaDublicate['date'] = $aT->date;
                                $hourdiff = $hourdiff_check;
                            }
                        }
                    }
                }

                if(($hourdiff < 1 && $hourdiff >= 0)) {
                    if ($anketaDublicate['id'] != 0) {
                        $errMsg = "Найден дубликат осмотра (ID: $anketaDublicate[id], Дата: $anketaDublicate[date])";
                    } else {
                        $errMsg = "Найден дубликат осмотра при добавлении (Дата: $anketaDublicate[date])";
                    }

                    array_push($errorsAnketa, $errMsg);
                    continue;
                }

                /**
                 * Генерация номера ПЛ
                 */
                if(empty($anketa['number_list_road'])) {
                    if($anketa['type_anketa'] !== 'medic') {
                        // Генерируем номер ПЛ
                        $findCurrentPL = Anketa::where('created_at', '>=', Carbon::today())->where('in_cart', 0)->get();
                        $suffix_anketa = count($findCurrentPL) > 0 ? '/' . (count($findCurrentPL) + 1) : '';
                        $anketa['number_list_road'] = ((isset($d_id) && $anketa['type_anketa'] === 'medic') ? $d_id : $c_id) . '-' . date('d.m.Y', strtotime($anketa['date'])) . $suffix_anketa;
                    }

                    // Проверка записи в Журнале ПЛ, если у нас ТО
                    if($anketa['type_anketa'] === 'tech') {
                        $anketaPL = $anketasTech
                            ->where('type_anketa', 'Dop');

                        if($anketaPL) {
                            foreach($anketaPL as $aPL) {
                                $hourdiff_check_minus = round((strtotime($anketa['date']) - strtotime($aPL->date))/1800, 1);
                                $hourdiff_check_plus = round((strtotime($anketa['date']) + strtotime($aPL->date))/1800, 1);

                                if(($hourdiff_check_minus < 1 && $hourdiff_check_minus >= 0) || ($hourdiff_check_plus < 1 && $hourdiff_check_plus >= 0)) {
                                    $aPL->delete();
                                }
                            }
                        }
                    }

                }

                /**
                 * Добавляем доп поля
                 */
                $anketa['user_id'] = $user->id;
                $anketa['user_name'] = $user->name;

                $anketa['user_eds'] = isset($anketa['user_eds']) ? $anketa['user_eds'] : $user->eds;

                $anketa['pulse'] = isset($anketa['pulse']) ? $anketa['pulse'] : mt_rand(60,80);

                $anketa['pv_id'] = Point::where('id', $pv_id)->first();

                // Проверка ПВ
                if($anketa['pv_id'])
                    $anketa['pv_id'] = $anketa['pv_id']->name;
                else
                    $anketa['pv_id'] = '';

                // Проверка АВТО
                if($Car) {
                    $anketa['car_id'] = $Car->hash_id;
                    $anketa['car_mark_model'] = $Car->mark_model;
                    $anketa['car_gos_number'] = $Car->gos_number;
                }

                /**
                 * Проверка дат при вводе БДД и Отчета
                 */
                if($Driver) {
                    if($anketa['type_anketa'] === 'bdd') {
                        $Driver->date_bdd = $anketa['date'];
                    } else if ($anketa['type_anketa'] === 'report_cart') {
                        $Driver->date_report_driver = $anketa['date'];
                    }

                    if($Driver->year_birthday) {
                        if($Driver->year_birthday !== '' && $Driver->year_birthday !== '0000-00-00') {
                            $anketa['driver_year_birthday'] = $Driver->year_birthday;
                        }
                    }

                    $anketa['driver_gender'] = isset($Driver->gender) ? $Driver->gender : '';

                    $Driver->save();
                }

                // Конвертация текущего времени Юзера
                date_default_timezone_set('UTC');
                $time = time();
                $timezone = $user->timezone ? $user->timezone : 3;
                $time += $timezone * 3600;
                $time = date('Y-m-d H:i:s', $time);

                $anketa['created_at'] = isset($anketa['created_at']) ? $anketa['created_at'] : $time;

                $connected_hash = sha1(time() . rand(99,9999));

                /**
                 * Проверка на "Дополнительный осмотр"
                 */
                if($is_tech_dop) {
                    $anketa['connected_hash'] = $connected_hash;
                    $dopAnketa = $anketa;
                    $dopAnketa['type_anketa'] = 'Dop';

                    $ank = new Anketa();
                    $dopAnketas[] = Arr::only($dopAnketa, $ank->getFillable());
                }

                /**
                 * Проверяем ПАК на наличие осмотра
                 * Выставляем автоматический режим если осмотр пришел с ПАК
                 */
                if(isset($anketa['is_pak'])) {
                    if($anketa['is_pak'] && $anketa['type_anketa'] === 'pak_queue') {
                        $anketa['flag_pak'] = 'СДПО Р';
                    } else {
                        $anketa['flag_pak'] = 'СДПО А';
                    }
                }

                /**
                 * Создаем анкету
                 */
//                $createdAnketa = Anketa::create($anketa);
//                array_push($createdAnketas, $createdAnketa->id);
//                $anketaCreated = Anketa::find($createdAnketa->id);

                /**
                 * Diff Date (ОСМОТР РЕАЛЬНЫЙ ИЛИ НЕТ)
                 */
                if($anketa['type_anketa'] === 'medic') {
                    $timezone = $user->timezone ?? 3;
                    $diffDateCheck = Carbon::now()->addHour($timezone)->diffInMinutes($anketa['date']);
                    if($diffDateCheck <= 60+12) {
                        $anketa['realy'] = 'да';
                    } else {
                        $anketa['realy'] = 'нет';
                    }

                }

                /**
                 * ОТПРАВКА SMS
                 */
                if($anketa['admitted'] == 'Не допущен' && isset($Company)) {
                    $phone_to_call = Settings::setting('sms_text_phone');

                    if(isset($Driver)) {
                        $sms->sms($Company->where_call, Settings::setting('sms_text_driver') . " $Driver->fio. $phone_to_call");
                    } else if (isset($Car)) {
                        $sms->sms($Company->where_call, Settings::setting('sms_text_car') . " $Car->gos_number. $phone_to_call");
                    } else {
                        $sms->sms($Company->where_call, Settings::setting('sms_text_default') . ' ' . new Anketa($anketa) . '.' . ' ' . $phone_to_call);
                    }
                }

                $ank = new Anketa();
                $createdAnketas[] = Arr::only($anketa, $ank->getFillable());
            }

            if (count($dopAnketas) > 0) {
                Anketa::insert($dopAnketas);
            }

            Anketa::insert($createdAnketas);
            $createdAnketas = Anketa::where('type_anketa', $data['type_anketa'])
                ->limit(count($createdAnketas))->orderBy('id', 'desc')->get();

            $responseData = [
                'createdId' => $createdAnketas->pluck('id')->toArray(),
                'errors' => array_unique($errorsAnketa),
                'type' => $data['type_anketa']
            ];

            if($isApiRoute) {
                $responseData['ankets'] = $createdAnketas;
                return response()->json($responseData);
            }

            if(count($redDates) > 0) {
                $responseData['redDates'] = $redDates;
            }

            if($is_dop) {
                $responseData['is_dop'] = 1;
            }

            return redirect()->route('forms', $responseData);
        }
    }

    /**
     * API ROUTES
     */
    public function ApiAddForm (Request $request)
    {
        $addForm = $this->AddFormTemp($request, true);

        return response()->json($addForm);
    }

    // Временный метод для фикса СДПО
    public function AddFormTemp (Request $request, $isApiRoute = false)
    {
        $user = ($isApiRoute) ? $request->user('api') : Auth::user();
        $sms = new SmsController();

        $data = $request->all();
        $d_id = $request->get('driver_id', 0); // Driver
        $pv_id = $request->get('pv_id', 0);

        function mt_rand_float($min, $max, $countZero = '0') {
            $countZero = +('1'.$countZero);
            $min = floor($min*$countZero);
            $max = floor($max*$countZero);
            $rand = mt_rand($min, $max) / $countZero;
            return $rand;
        }

        // TODO: добавить время действия
        session(['anketa_pv_id' => [
            'value' => $pv_id,
            'expired' => date('d.m')
        ]]);

        // Проверяем дефолтные значения
        $defaultDatas = [
            'termometr' => '36,3 - 36,9',
            'tonometer' => rand(118,129) .'/'. rand(70,90),
            't_people' => mt_rand_float(35.9,36.7),
            'date' => date('Y-m-d H:i:s')
        ];

        $test_narko = isset($data['test_narko']) ? $data['test_narko'] : 'Отрицательно';
        $proba_alko = isset($data['proba_alko']) ? $data['proba_alko'] : 'Отрицательно';
        $is_dop = isset($data['is_dop']) ? ($data['is_dop'] === '1') : 0;

        // Выставляем оптимальные параметры
        unset($data['_token']);

        // ЕСЛИ ЗАПРОС С API/ПАК
        if(isset($data['user_id']) && $isApiRoute) {
            $user = User::find($data['user_id']);
        }

        $data['user_id'] = $user->id;

        // Фотографии с ПАК
        if($request->hasFile('photos')) {
            // Парсим файлы
            $photos = $request->file('photos');
            $photos_path = '';

            foreach($photos as $photoIndex => $photo) {
                $file_path = Storage::disk('public')->putFile('ankets', $photo);

                $photos_path .= ($photoIndex == 0 ? '' : ',') . $file_path;
            }

            $data['photos'] = $photos_path;
        }

        // Анкета
        if(isset($data['anketa'])) {
            // Клонируем анкету
            $createdAnketas = [];
            $createdAnketasDataResponseApi = [];
            $data_anketa = $data['anketa'];
            $errorsAnketa = array();

            foreach($data_anketa as $anketa) {
                // Выделение красных дат
                $redDates = [];

                // ID автомобиля
                $c_id = isset($anketa['car_id']) ? $anketa['car_id'] :
                    (isset($data['car_id']) ? $data['car_id'] : 0);

                $Car = Car::where('hash_id', $c_id)->first();
                $Driver = Driver::where('hash_id', $d_id)->first();

                // Тонометр
                $tonometer = isset($anketa['tonometer']) ? $anketa['tonometer'] : $defaultDatas['tonometer'];

                if(!isset($anketa['med_view'])) {
                    $anketa['med_view'] = 'В норме';
                }

                /**
                 * Парсим данные в анкете, удаляем главную анкету и ставим актуальную
                 */
                unset($data['anketa']);
                foreach($data as $dk => $dv) {
                    $dv_item = $dv;

                    if(empty($dv_item) && isset($defaultDatas[$dk])) {
                        if($dk === 'date' && $is_dop) {

                        } else {
                            $dv_item = $defaultDatas[$dk];
                        }
                    }

                    $anketa[$dk] = $dv_item;
                }

                /**
                 * Проверяем дефолтные значения
                 */
                foreach($defaultDatas as $dfKey => $dfData) {
                    if(empty($anketa[$dfKey])) {
                        if($is_dop && $dfKey === 'date') {

                        } else {
                            $anketa[$dfKey] = $dfData;
                        }
                    }
                }

                /**
                 * ОЧЕРЕДЬ ПАК
                 */
                if($anketa['type_anketa'] == 'pak_queue') {
                    $notifyTo = new Notify();
                    $notifyTo->sendMsgToUsersFrom('role', '4', 'Новый осмотр в очереди СДПО');
                }

                /**
                 * Компания
                 */
                if(isset($anketa['company_id'])) {
                    $CompanyDop = Company::where('hash_id', $anketa['company_id'])->first();

                    if($CompanyDop) {
                        $anketa['company_id'] = $CompanyDop->hash_id;
                        $anketa['company_name'] = $CompanyDop->name;
                    }
                }

                if (isset($anketa['driver_id'])) {
                    $DriverDop = Driver::where('hash_id', $anketa['driver_id'])->first();

                    if ($DriverDop) {
                        $anketa['driver_id'] = $DriverDop->hash_id;
                        $anketa['driver_fio'] = $DriverDop->fio;
                    }
                }

                /**
                 * Проверка водителя по: тесту наркотиков, возрасту
                 */
                if($d_id || isset($Driver)) {
                    if(!Driver::DriverChecker($d_id, $tonometer, $test_narko, $proba_alko) && !in_array($anketa['type_anketa'], ['bdd', 'pechat_pl', 'vid_pl', 'report_cart'])) {

                        if($anketa['type_anketa'] !== 'tech') {
                            $errMsg = 'Водитель не найден';

                            $errorDriverFind = [
                                'errors' => [$errMsg]
                            ];

                            $this->savePakForm($anketa, $errMsg);

                            if($isApiRoute) {
                                return response()->json($errorDriverFind);
                            }

                            return redirect()->route('forms', $errorDriverFind);
                        } else {
                            $anketa['driver_id'] = 0;
                        }
                    } else {
                        $anketa['driver_fio'] = $Driver->fio;
                        $anketa['driver_group_risk'] = $Driver->group_risk;

                        if($Driver->dismissed === 'Да') {
                            $errMsg = 'Водитель уволен. Осмотр зарегистрирован. Обратитесь к менеджеру';

                            array_push($errorsAnketa, $errMsg);
                        }

                        /**
                         * Добавляем Компанию
                         */
                        if($Driver->company_id) {
                            $Company = Company::where('id', $Driver->company_id)->first();

                            if($Company) {

                                $anketa['company_id'] = $Company->hash_id;
                                $anketa['company_name'] = $Company->name;

                                if($Company->dismissed === 'Да') {
                                    $errMsg = 'Компания в черном списке. Необходимо связаться с руководителем!';

                                    array_push($errorsAnketa, $errMsg);

                                    continue;
                                }

                            } else {
                                $errMsg = "У Водителя не верно указано ID компании";

                                array_push($errorsAnketa, $errMsg);

                                $this->savePakForm($anketa, $errMsg);

                                continue;
                            }

                        } else {
                            $errMsg = "У Водителя не найдена компания";

                            array_push($errorsAnketa, $errMsg);

                            $this->savePakForm($anketa, $errMsg);

                            continue;
                        }
                    }
                }

                /**
                 * Проверка данных анкеты (добавляем в доп.осмотр или нет)
                 * Техосмотр:
                 *  - если ПЛ то добавляем доп
                 *
                 * Медосмотр:
                 *    - ID авто и номер ПЛ то добавляем доп
                 */
                $is_med_dop = $anketa['type_anketa'] === 'medic';
                $is_tech_dop = $anketa['type_anketa'] === 'tech';

                /**
                 * ПРОВЕРЯЕМ статус для поля "Заключение"
                 */
                $tonometer = explode('/', $anketa['tonometer']);
                if($proba_alko === 'Отрицательно' && ($test_narko === 'Отрицательно' || $test_narko === 'Не проводился')
                    && $anketa['med_view'] === 'В норме' && $anketa['t_people'] < 38 && $tonometer[0] < 150) {
                    $anketa['admitted'] = 'Допущен';
                } else {
                    $anketa['admitted'] = 'Не допущен';
                }

                /**
                 * ПРОВЕРЯЕМ СТАТУС для поля "Заключение" - от ПАК
                 */
                if(isset($anketa['sleep_status']) && isset($anketa['people_status']) && isset($anketa['alcometer_result'])) {

                    if($anketa['sleep_status'] === 'Да' && $anketa['people_status'] === 'Да' && $anketa['alcometer_result'] <= 0) {
                        $anketa['admitted'] = 'Допущен';
                    } else {
                        $anketa['admitted'] = 'Не допущен';
                    }

                }

                if(!empty($d_id) || !empty($c_id) || !empty($anketa['number_list_road'])) {
                    /**
                     * <КОНТРОЛЬ-ДАТ>
                     */
                    $dateCheckModel = null;

                    if($anketa['type_anketa'] === 'medic') {
                        $dateCheckModel = $Driver;
                        $dateCheck = DDates::where('item_model', 'Driver')->get();
                    } else {
                        $dateCheckModel = $Car;
                        $dateCheck = DDates::where('item_model', 'Car')->get();
                    }

                    if($dateCheck && $dateCheckModel) {
                        foreach($dateCheck as $dateCheckItem) {
                            $fieldDateCheck = $dateCheckItem->field;

                            if(isset($dateCheckModel[$fieldDateCheck])) {
                                $fieldDateItemValue = $dateCheckModel[$fieldDateCheck];

                                $dateAction = $dateCheckItem->action . ' ' . $dateCheckItem->days . ' days';

                                $dateCheckWithAnketa = date('Y-m-d', strtotime($fieldDateItemValue . ' ' . $dateAction));
                                $anketaDate = date('Y-m-d', strtotime($anketa['date']));

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
                    /**
                     * </КОНТРОЛЬ-ДАТ>
                     */
                }

                /**
                 * Проверка на дубликат из ТЗ
                 *
                 * Мы должны дать техническую возможность внесение осмотров любой даты (год назад, месяц назад.
                 * При внесении осмотра, система должна смотреть, есть ли подобный.
                 *
                 * Например сегодня 13.02.21 в 09.00 до 10.00.
                 */
                $hourdiff = 1;
                $anketaDublicate = [
                    'id' => 0,
                    'date' => ''
                ];

                if ($anketa['type_anketa'] === 'medic' || $anketa['type_anketa'] === 'pak') {
                    $anketaMedic = Anketa::where('driver_id', $d_id)
                        ->where('type_anketa', 'medic')
                        ->where('type_view', isset($anketa['type_view']) ? $anketa['type_view'] : '')
                        ->where('in_cart', 0)
                        ->orderBy('date', 'desc')
                        ->get();

                    if($anketaMedic) {
                        foreach($anketaMedic as $aM) {
                            if (!$aM->date || ($aM->is_dop && $aM->result_dop == null)) {
                                continue;
                            }

                            $hourdiff_check = round((strtotime($anketa['date']) - Carbon::parse($aM->date)->timestamp)/60, 1);

                            if($hourdiff_check < 1 && $hourdiff_check >= 0) {
                                $anketaDublicate['id'] = $aM->id;
                                $anketaDublicate['date'] = $aM->date;
                                $hourdiff = $hourdiff_check;
                            }
                        }
                    }
                } else if ($anketa['type_anketa'] === 'tech' || $anketa['type_anketa'] === 'vid_pl') {
                    $anketaTech = Anketa::where('car_id', $c_id)
                        ->where('type_anketa', 'tech')
                        ->where('type_view', isset($anketa['type_view']) ? $anketa['type_view'] : '')
                        ->where('in_cart', 0)
                        ->orderBy('date', 'desc')
                        ->get();


                    /**
                     * Уволенный АВТО
                     */
                    if($Car) {
                        if($Car->dismissed === 'Да') {
                            $errMsg = 'Автомобиль уволен. Осмотр зарегистрирован. Обратитесь к менеджеру';

                            array_push($errorsAnketa, $errMsg);
                        }
                    }

                    // Если нет водителя И есть Авто - то ставим компанию из Авто

                    if(!isset($Driver->id) && $Car) {
                        $Company_Car = Company::where('id', $Car->company_id)->first();

                        if($Company_Car) {

                            $anketa['company_id'] = $Company_Car->hash_id;
                            $anketa['company_name'] = $Company_Car->name;

                            if($Company_Car->dismissed === 'Да') {
                                $errMsg = 'Компания в черном списке. Необходимо связаться с руководителем!';

                                array_push($errorsAnketa, $errMsg);

                                continue;
                            }
                        } else {
                            $errMsg = "У Автомобиля не найдена компания";

                            array_push($errorsAnketa, $errMsg);

                            $this->savePakForm($anketa, $errMsg);

                            continue;
                        }
                    }

                    if($anketaTech && !$anketa['is_dop']) {
                        foreach($anketaTech as $aT) {
                            if (!$aT->date || ($aT->is_dop && $aT->result_dop == null)) {
                                continue;
                            }

                            $hourdiff_check = round((strtotime($anketa['date']) - Carbon::parse($aT->date)->timestamp)/60, 1);

                            if($hourdiff_check < 1 && $hourdiff_check >= 0) {
                                $anketaDublicate['id'] = $aT->id;
                                $anketaDublicate['date'] = $aT->date;
                                $hourdiff = $hourdiff_check;
                            }
                        }
                    }
                }

                if(($hourdiff < 1 && $hourdiff >= 0)) {
                    $errMsg = "Найден дубликат осмотра (ID: $anketaDublicate[id], Дата: $anketaDublicate[date])";

                    array_push($errorsAnketa, $errMsg);
                    continue;
                }

                /**
                 * Генерация номера ПЛ
                 */
                if(empty($anketa['number_list_road'])) {
                    if($anketa['type_anketa'] !== 'medic') {
                        // Генерируем номер ПЛ
                        $findCurrentPL = Anketa::where('created_at', '>=', Carbon::today())->where('in_cart', 0)->get();
                        $suffix_anketa = count($findCurrentPL) > 0 ? '/' . (count($findCurrentPL) + 1) : '';
                        $anketa['number_list_road'] = ((isset($d_id) && $anketa['type_anketa'] === 'medic') ? $d_id : $c_id) . '-' . date('d.m.Y', strtotime($anketa['date'])) . $suffix_anketa;
                    }

                    // Проверка записи в Журнале ПЛ, если у нас ТО
                    if($anketa['type_anketa'] === 'tech') {
                        $anketaPL = Anketa::where('car_id', $c_id)
                            ->where('type_anketa', 'Dop')
                            ->where('in_cart', 0)
                            ->orderBy('date', 'desc')
                            ->get();

                        if($anketaPL) {
                            foreach($anketaPL as $aPL) {
                                $hourdiff_check_minus = round((strtotime($anketa['date']) - strtotime($aPL->date))/1800, 1);
                                $hourdiff_check_plus = round((strtotime($anketa['date']) + strtotime($aPL->date))/1800, 1);

                                if(($hourdiff_check_minus < 1 && $hourdiff_check_minus >= 0) || ($hourdiff_check_plus < 1 && $hourdiff_check_plus >= 0)) {
                                    $aPL->delete();
                                }
                            }
                        }
                    }

                }

                /**
                 * Добавляем доп поля
                 */
                $anketa['user_id'] = $user->id;
                $anketa['user_name'] = $user->name;

                $anketa['user_eds'] = isset($anketa['user_eds']) ? $anketa['user_eds'] : $user->eds;

                $anketa['pulse'] = isset($anketa['pulse']) ? $anketa['pulse'] : mt_rand(60,80);

                $anketa['pv_id'] = Point::where('id', $pv_id)->first();

                // Проверка ПВ
                if($anketa['pv_id'])
                    $anketa['pv_id'] = $anketa['pv_id']->name;
                else
                    $anketa['pv_id'] = '';

                // Проверка АВТО
                if($Car) {
                    $anketa['car_id'] = $Car->hash_id;
                    $anketa['car_mark_model'] = $Car->mark_model;
                    $anketa['car_gos_number'] = $Car->gos_number;
                }

                /**
                 * Проверка дат при вводе БДД и Отчета
                 */
                if($Driver) {
                    if($anketa['type_anketa'] === 'bdd') {
                        $Driver->date_bdd = $anketa['date'];
                    } else if ($anketa['type_anketa'] === 'report_cart') {
                        $Driver->date_report_driver = $anketa['date'];
                    }

                    if($Driver->year_birthday) {
                        if($Driver->year_birthday !== '' && $Driver->year_birthday !== '0000-00-00') {
                            $anketa['driver_year_birthday'] = $Driver->year_birthday;
                        }
                    }

                    $anketa['driver_gender'] = isset($Driver->gender) ? $Driver->gender : '';

                    $Driver->save();
                }

                // Конвертация текущего времени Юзера
                date_default_timezone_set('UTC');
                $time = time();
                $timezone = $user->timezone ? $user->timezone : 3;
                $time += $timezone * 3600;
                $time = date('Y-m-d H:i:s', $time);

                $anketa['created_at'] = isset($anketa['created_at']) ? $anketa['created_at'] : $time;

                $connected_hash = sha1(time() . rand(99,9999));

                /**
                 * Проверка на "Дополнительный осмотр"
                 */
                if($is_tech_dop) {
                    $anketa['connected_hash'] = $connected_hash;
                    $dopAnketa = $anketa;
                    $dopAnketa['type_anketa'] = 'Dop';
                    Anketa::create($dopAnketa);
                }

                /**
                 * Проверяем ПАК на наличие осмотра
                 * Выставляем автоматический режим если осмотр пришел с ПАК
                 */
                if(isset($anketa['is_pak'])) {
                    if($anketa['is_pak'] && $anketa['type_anketa'] === 'pak_queue') {
                        $anketa['flag_pak'] = 'СДПО Р';
                    } else {
                        $anketa['flag_pak'] = 'СДПО А';
                    }
                }

                /**
                 * Создаем анкету
                 */
                $createdAnketa = Anketa::create($anketa);
                array_push($createdAnketas, $createdAnketa->id);
                $anketaCreated = Anketa::find($createdAnketa->id);

                /**
                 * Diff Date (ОСМОТР РЕАЛЬНЫЙ ИЛИ НЕТ)
                 */
                if($createdAnketa->type_anketa === 'medic') {
                    $timezone = $user->timezone ?? 3;
                    $diffDateCheck = Carbon::now()->addHour($timezone)->diffInMinutes($createdAnketa->date);

                    if($diffDateCheck <= 60*12) {
                        $anketaCreated->realy = 'да';
                        $anketaCreated->save();
                    } else {
                        $anketaCreated->realy = 'нет';
                        $anketaCreated->save();
                    }

                }

                /**
                 * ОТПРАВКА SMS
                 */
                if($anketa['admitted'] == 'Не допущен' && isset($Company)) {
                    $phone_to_call = Settings::setting('sms_text_phone');

                    if(isset($Driver)) {
                        $sms->sms($Company->where_call, Settings::setting('sms_text_driver') . " $Driver->fio. $phone_to_call");
                    } else if (isset($Car)) {
                        $sms->sms($Company->where_call, Settings::setting('sms_text_car') . " $Car->gos_number. $phone_to_call");
                    } else {
                        $sms->sms($Company->where_call, Settings::setting('sms_text_default') . " $createdAnketa. $phone_to_call");
                    }
                }

                if($isApiRoute) {
                    array_push($createdAnketasDataResponseApi, $createdAnketa);
                }
            }

            $responseData = [
                'createdId' => $createdAnketas,
                'errors' => $errorsAnketa,
                'type' => $data['type_anketa']
            ];

            if($isApiRoute) {
                $responseData['ankets'] = $createdAnketasDataResponseApi;
                return response()->json($responseData);
            }

            if(count($redDates) > 0) {
                $responseData['redDates'] = $redDates;
            }

            if($is_dop) {
                $responseData['is_dop'] = 1;
            }

            return redirect()->route('forms', $responseData);
        }
    }
}
