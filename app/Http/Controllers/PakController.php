<?php

namespace App\Http\Controllers;

use App\Anketa;
use App\FieldPrompt;
use Illuminate\Http\Request;

class PakController extends Controller
{
    public function index(Request $request) {
        $fields = FieldPrompt::where('type', 'pak_queue')->get();

        if ($request->clear) {
            Anketa::where('type_anketa', 'pak_queue')
                ->where('user_id', $request->user()->id)->delete();
            return redirect(route('pak.index'));
        }

        return view('pak.index', [
            'fields' => $fields
        ]);
    }

    public function list(Request $request) {
        $anketas = Anketa::where('type_anketa', 'pak_queue');

        if (!$request->user()->hasRole('admin')) {
            $anketas = $anketas->where('user_id', $request->user()->id);
        }

        if ($request->order_key) {
            $anketas = $anketas->orderBy($request->order_key, $request->order_by ?? 'ASC');
        }

        return response()->json($anketas->get());
    }
}
