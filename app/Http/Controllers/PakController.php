<?php

namespace App\Http\Controllers;

use App\Enums\FormTypeEnum;
use App\FieldPrompt;
use App\Models\Forms\Form;
use App\Models\Forms\MedicForm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PakController extends Controller
{
    public function index(Request $request)
    {
        if ($request->clear) {
            Form::query()->pakQueueByUser($request->user())->delete();

            return redirect(route('pak.index'));
        }

        return view('pak.index', [
            'fields' => FieldPrompt::where('type', FormTypeEnum::PAK_QUEUE)->get()
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $forms = Form::query()
            ->select([
                'forms.*',
                'drivers.fio as driver_fio',
                'points.name as pv_id'
            ])
            ->join('drivers', 'drivers.hash_id', '=', 'forms.driver_id')
            ->join('points', 'points.id', '=', 'forms.point_id');

        $forms->pakQueueByUser($request->user());

        if ($request->order_key) {
            $forms = $forms->orderBy($request->order_key, $request->order_by ?? 'ASC');
        }

        $forms->get();

        $data = $forms->get()->map(function (Form $form) {
            $formData = $form->toArray();
            /** @var MedicForm $formDetails */
            $formDetails = $form->details;
            $formDetails->append('not_admitted_reasons');

            return $formData + $formDetails->toArray();
        });

        return response()->json($data);
    }
}
