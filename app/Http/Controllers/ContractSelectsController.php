<?php

namespace App\Http\Controllers;

use App\Car;
use App\Company;
use App\Driver;
use App\Product;
use App\Req;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractSelectsController extends Controller
{
    public function companies(Request $request)
    {
        $query = $request->get("query", "");

        $data = Company::query()
            ->select([
                'id',
                DB::raw("CONCAT(name, ' [h_ID:', hash_id, '][ИНН:', COALESCE(inn, ''), ']') as name")
            ])
            ->where('name', 'like', "%$query%")
            ->orWhere('hash_id', 'like', "%$query%")
            ->orWhere('inn', 'like', "%$query%")
            ->limit(10)
            ->get();

        return response($data);
    }

    public function ourCompanies(Request $request)
    {
        $query = $request->get("query", "");

        $data = Req::query()
            ->select([
                'id',
                DB::raw("CONCAT(name, ' [h_ID:', hash_id, '][ИНН:', COALESCE(inn, ''), ']') as name")
            ])
            ->where('name', 'like', "%$query%")
            ->orWhere('inn', 'like', "%$query%")
            ->orWhere('hash_id', 'like', "%$query%")
            ->limit(10)
            ->get();

        return response($data);
    }

    public function cars(Request $request)
    {
        $query = $request->get("query", "");

        $data = Car::query()
            ->select([
                'id',
                DB::raw("CONCAT(mark_model, ' [', hash_id, ']') as mark_model")
            ])
            ->where('mark_model', 'like', "%$query%")
            ->orWhere('hash_id', 'like', "%$query%")
            ->limit(10)
            ->get();

        return response($data);
    }

    public function drivers(Request $request)
    {
        $query = $request->get("query", "");

        $data = Driver::query()
            ->select([
                "id",
                DB::raw("CONCAT(fio, ' [', hash_id, ']') as fio")
            ])
            ->where('fio', 'like', "%$query%")
            ->orWhere('hash_id', 'like', "%$query%")
            ->limit(10)
            ->get();

        return response($data);
    }

    public function products(Request $request)
    {
        $query = $request->get("query", "");

        $data = Product::query()
            ->select([
                "id",
                "price_unit",
                DB::raw("CONCAT(name, ' [', hash_id, ']') as name")
            ])
            ->where('name', 'like', "%$query%")
            ->orWhere('hash_id', 'like', "%$query%")
            ->limit(10)
            ->get();

        return response($data);
    }
}
