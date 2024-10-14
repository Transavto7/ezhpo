<?php

namespace App\Http\Controllers;

use App\Actions\Anketa\ChangeResultDopHandler;
use App\Actions\Anketa\CreateFormHandlerFactory;
use App\Actions\Anketa\CreateSdpoFormHandler;
use App\Actions\Anketa\TrashFormHandler;
use App\Actions\Anketa\UpdateFormHandler;
use App\Actions\AnketsExportPdfLabeling\AnketsExportPdfLabelingCommand;
use App\Actions\AnketsExportPdfLabeling\AnketsExportPdfLabelingHandler;
use App\Actions\PakQueue\ChangePakQueue\ChangePakQueueAction;
use App\Actions\PakQueue\ChangePakQueue\ChangePakQueueHandler;
use App\Anketa;
use App\Car;
use App\DDates;
use App\Driver;
use App\Enums\FormTypeEnum;
use App\Enums\QRCodeLinkParameter;
use App\Point;
use App\Traits\UserEdsTrait;
use App\User;
use App\ValueObjects\NotAdmittedReasons;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AnketsController extends Controller
{
    public static function ddateCheck($dateAnketa, $dateModel, $id)
    {
        $dateCheckModel = app("App\\$dateModel")->find($id);
        $dateCheck = DDates::where('item_model', $dateModel)->get();

        $redDates = [];

        if ($dateCheck && $dateCheckModel) {
            foreach ($dateCheck as $dateCheckItem) {
                $fieldDateCheck = $dateCheckItem->field;

                if (isset($dateCheckModel[$fieldDateCheck])) {
                    $fieldDateItemValue = $dateCheckModel[$fieldDateCheck];

                    $dateAction = $dateCheckItem->action . ' ' . $dateCheckItem->days . ' days';

                    $dateCheckWithAnketa = date('Y-m-d', strtotime($fieldDateItemValue . ' ' . $dateAction));
                    $anketaDate = date('Y-m-d', strtotime($dateAnketa));

                    if ($dateCheckWithAnketa <= $anketaDate) {
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

    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $type = $request->get('type');
        if (!$type) {
            if ($user->hasRole('manager') || $user->hasRole('engineer_bdd')) {
                return redirect()->route('renderElements', 'Company');
            }
            if ($user->hasRole('operator_sdpo')) {
                return redirect()->route('home', 'pak_queue');
            }
            if ($user->hasRole('client')) {
                return redirect()->route('home', ['type_ankets' => 'medic']);
            }
            if ($user->hasRole('tech')) {
                $type = 'tech';
            }
            if ($user->hasRole('medic')) {
                $type = 'medic';
            }
            if (!$type) {
                return redirect()->route('index');
            }
        }

        $forms = [
            'medic' => [
                'title' => 'Медицинский осмотр',
                'anketa_view' => 'profile.ankets.medic',
            ],
            'tech' => [
                'title' => 'Технический осмотр',
                'anketa_view' => 'profile.ankets.tech',
            ],
            'pechat_pl' => [
                'title' => 'Журнал печати путевых листов',
                'anketa_view' => 'profile.ankets.pechat_pl',
            ],
            'pak' => [
                'title' => 'СДПО',
                'anketa_view' => 'profile.ankets.pak',
            ],
            'pak_queue' => [
                'title' => 'Очередь на утверждение',
                'anketa_view' => 'profile.ankets.pak_queue',
            ],
            'bdd' => [
                'title' => 'Журнал инструктажей по БДД',
                'anketa_view' => 'profile.ankets.bdd',
            ],
            'report_cart' => [
                'title' => 'Журнал снятия отчетов с карт',
                'anketa_view' => 'profile.ankets.report_cart',
            ],
        ];

        // Отображаем данные
        $data = $forms[$type];

        // Конвертация текущего времени Юзера
        date_default_timezone_set('UTC');
        $time = time();
        $timezone = $user->timezone ?: 3;
        $time += $timezone * 3600;
        $time = date('Y-m-d\TH:i', $time);

        // Дефолтные значения
        $data['default_current_date'] = $time;
        $data['points'] = Point::getAll();
        $data['type_anketa'] = $type;
        $data['default_pv_id'] = $user->pv_id;
        $data['Driver'] = Driver::class;
        $data['Car'] = Car::class;
        $data['car_id'] = $request->input(QRCodeLinkParameter::CAR_ID);
        $data['driver_id'] = $request->input(QRCodeLinkParameter::DRIVER_ID);

        // Проверяем выставленный ПВ
        if (session()->exists('anketa_pv_id') && ((date('d.m') > session('anketa_pv_id')['expired']))) {
            session()->remove('anketa_pv_id');
        }

        return view('profile.anketa', $data);
    }

    public function Get(Request $request)
    {
        $form = Anketa::where('id', $request->id)->first();

        $data = [];

        foreach ($form->fillable as $attribute) {
            $data[$attribute] = $form[$attribute];
        }

        $companyFields = config('elements')['Driver']['fields']['company_id'];
        $companyFields['getFieldKey'] = 'name';

        $data['title'] = 'Редактирование осмотра';

        if ($form->date) {
            $data['default_current_date'] = date('Y-m-d\TH:i', strtotime($form->date));
        }

        $data['points'] = Point::getAll();
        $data['anketa_view'] = 'profile.ankets.' . $form->type_anketa;
        $data['pv_id'] = $form->point_id ?? $form->pv_id;
        $data['anketa_route'] = 'forms.update';
        $data['company_fields'] = $companyFields;

        if ($form->type_anketa === FormTypeEnum::PAK_QUEUE) {
            $data['not_admitted_reasons'] = NotAdmittedReasons::fromForm($form)->getReasons();
        }

        return view('profile.anketa', $data);
    }

    public function Delete(Request $request)
    {
        $id = $request->id;

        if (Anketa::find($id)->delete()) {
            return redirect(url()->previous());
        }

        return abort(403);
    }

    public function Trash(Request $request, TrashFormHandler $handler)
    {
        $id = $request->id;
        $action = $request->action;
        $anketa = Anketa::find($id);

        if ($anketa) {
            $saved = $handler->handle($anketa, $action);

            if ($saved) {
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

    public function ChangePakQueue(Request $request, $id, $admitted, ChangePakQueueHandler $handler)
    {
        try {
            DB::beginTransaction();

            /** @var User $user */
            $user = Auth::user();
            $handler->handle(new ChangePakQueueAction($id, $admitted, $user));

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(
                    ['message' => 'Осмотр успешно принят']
                );
            } else {
                return back();
            }
        } catch (Throwable $exception) {
            DB::rollBack();

            if ($request->wantsJson()) {
                return response()->json(
                    ['message' => $exception->getMessage()],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            } else {
                return back()->with('error', $exception->getMessage());
            }
        }
    }

    public function ChangeResultDop($id, $result_dop, ChangeResultDopHandler $handler): RedirectResponse
    {
        $form = Anketa::findOrFail($id);

        try {
            DB::beginTransaction();

            $handler->handle($form, $result_dop);

            DB::commit();

            return back();
        } catch (Throwable $exception) {
            DB::rollBack();

            return back()->with('error', $exception->getMessage());
        }
    }

    public function ChangeMultipleResultDop(Request $request, ChangeResultDopHandler $handler): JsonResponse
    {
        $ids = $request->input('ids', []);
        $result = $request->input('result', 'Утвержден');

        $errors = [];

        foreach ($ids as $id) {
            $form = Anketa::query()
                ->where('id', $id)
                ->where('is_dop', '=', 1)
                ->whereNull('result_dop')
                ->first();

            if ($form === null) {
                $errors[] = "Осмотр с id $id уже утвержден";
                continue;
            }

            try {
                DB::beginTransaction();

                $handler->handle($form, $result);

                DB::commit();
            } catch (Throwable $exception) {
                DB::rollBack();

                $errors[] = $exception->getMessage();
            }
        }

        if (count($errors)) {
            session()->flash('mass_approve_errors', $errors);
        }

        return response()->json();
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
                $response = redirect($referer);
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

        return redirect()->route('forms.index', $responseData);
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

                foreach ($photos as $photo) {
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

    public function print(Request $request, $id)
    {
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
            'validity' => UserEdsTrait::getValidityString(
                $anketa->user_validity_eds_start,
                $anketa->user_validity_eds_end
            )
        ]);

        $response = response()->make($pdf->output(), 200);
        $response->header('Content-Type', 'application/pdf');
        return $response;
    }

    public function exportPdfLabeling(Request $request, AnketsExportPdfLabelingHandler $handler)
    {
        $anketIds = $request->input('anket_ids');

        try {
            return $handler->handle(new AnketsExportPdfLabelingCommand($anketIds));
        } catch (Throwable $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
