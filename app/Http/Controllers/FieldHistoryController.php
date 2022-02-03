<?php

namespace App\Http\Controllers;

use App\FieldHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FieldHistoryController extends Controller
{
    public function save (Request $request) {
        $data = $request->all();

        $data['user_id'] = $request->user()->id;
        $data['hash_id'] = mt_rand(1000,9999) . date('s');

        $data = FieldHistory::create($data);

        return response()->json($data);
    }
}
