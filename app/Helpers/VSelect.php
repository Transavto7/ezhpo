<?php

namespace App\Helpers;

use App\Car;
use App\Company;
use App\Driver;
use App\Models\Service;
use App\Product;
use App\Req;
use Illuminate\Http\Request;

class VSelect
{
    public function companies(Request $request)
    {
        $query = $request->get("query", "");

        return response(
            Company::where('name', 'like', "%$query%")
                   ->orWhere('hash_id', 'like', "%$query%")
//                   ->selectRaw("CONCAT(name, ' [', hash_id, ']') as label, id as key")
                   ->selectRaw("CONCAT(name, ' [', hash_id, ']') as name, id")
                   ->limit(10)
                   ->get()
        );
    }

    public function our_companies(Request $request)
    {
        $query = $request->get("query", "");

        return response(
            Req::where('name', 'like', "%$query%")
                   ->orWhere('hash_id', 'like', "%$query%")
//                   ->selectRaw("CONCAT(name, ' [', hash_id, ']') as label, id as key")
                   ->selectRaw("CONCAT(name, ' [', hash_id, ']') as name, id")
                   ->limit(10)
                   ->get()
        );
    }

    public function cars(Request $request)
    {
        $query = $request->get("query", "");

        return response(
            Car::where('mark_model', 'like', "%$query%")
               ->orWhere('hash_id', 'like', "%$query%")
               ->selectRaw("CONCAT(mark_model, ' [', hash_id, ']') as mark_model, id")
               ->limit(10)
               ->get()
        );
    }

    public function drivers(Request $request)
    {
        $query = $request->get("query", "");

        return response(
            Driver::where('fio', 'like', "%$query%")
                  ->orWhere('hash_id', 'like', "%$query%")
                  ->selectRaw("CONCAT(fio, ' [', hash_id, ']') as fio, id")
                  ->limit(10)
                  ->get()
        );
    }

    public function services(Request $request)
    {
        $query = $request->get("query", "");

        return response(
            Service::where('name', 'like', "%$query%")
                  ->orWhere('hash_id', 'like', "%$query%")
                  ->selectRaw("CONCAT(name, ' [', hash_id, ']') as name, id, price_unit")
                  ->limit(10)
                  ->get()
        );
    }
}
