<?php

namespace App\Helpers;

use App\Car;
use App\Company;
use App\Driver;
use App\Models\Service;
use App\Req;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Класс хелпер для поисковиков v-select
 */
class VSelect
{

    /**
     * Возвращает данные формата: [id:{id}, name:[{name} [h_ID: {hash_id}][ИНН: {inn}]]
     *
     * @param Request $request // _GET[{query}]
     *
     * @return Application|ResponseFactory|Response
     */
    public function companies(Request $request)
    {
        $query = $request->get("query", "");

        return response(
            Company::where('name', 'like', "%$query%")
                   ->orWhere('hash_id', 'like', "%$query%")
                   ->orWhere('inn', 'like', "%$query%")
                   ->selectRaw("CONCAT(name, ' [h_ID:', hash_id, '][ИНН:', COALESCE(inn, ''), ']') as name, id")
                   ->limit(10)
                   ->get()
        );
    }


    /**
     * Возвращает данные формата: [id:{id}, name:[{name} [h_ID: {hash_id}][ИНН: {inn}]]
     *
     * @param Request $request // _GET[{query}]
     *
     * @return Application|ResponseFactory|Response
     */
    public function our_companies(Request $request)
    {
        $query = $request->get("query", "");

        return response(
            Req::where('name', 'like', "%$query%")
               ->orWhere('inn', 'like', "%$query%")
               ->orWhere('hash_id', 'like', "%$query%")
               ->selectRaw("CONCAT(name, ' [h_ID:', hash_id, '][ИНН:', COALESCE(inn, ''), ']') as name, id")
               ->limit(10)
               ->get()
        );
    }


    /**
     * Возвращает данные формата: [id:{id}, name:[{name} [h_ID: {hash_id}]]
     *
     * @param Request $request // _GET[{query}]
     *
     * @return Application|ResponseFactory|Response
     */
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


    /**
     * Возвращает данные формата: [id:{id}, fio:[{fio} [h_ID: {hash_id}]]
     *
     * @param Request $request // _GET[{query}]
     *
     * @return Application|ResponseFactory|Response
     */
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


    /**
     * Возвращает данные формата: [id:{id}, price_unit:{price_unit}, name:[{name} [h_ID: {hash_id}]]
     *
     * @param Request $request // _GET[{query}]
     *
     * @return Application|ResponseFactory|Response
     */
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
