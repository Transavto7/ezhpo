<?php

namespace App\Http\Controllers;

use App\Anketa;
use App\Enums\FormTypeEnum;
use App\FieldPrompt;
use App\ValueObjects\NotAdmittedReasons;
use Illuminate\Http\Request;

class PakController extends Controller
{
    public function index(Request $request) {
        if ($request->clear) {
            Anketa::query()->pakQueueByUser($request->user())->delete();

            return redirect(route('pak.index'));
        }

        return view('pak.index', [
            'fields' => FieldPrompt::where('type', FormTypeEnum::PAK_QUEUE)->get()
        ]);
    }

    public function list(Request $request) {
        $forms = Anketa::query();

        $forms->pakQueueByUser($request->user());

        if ($request->order_key) {
            $forms = $forms->orderBy($request->order_key, $request->order_by ?? 'ASC');
        }

        $forms->get();

        $data = $forms->get()->map(function (Anketa $form) {
            $form->append('not_admitted_reasons');

            return $form;
        });

        return response()->json($data);
    }
}
