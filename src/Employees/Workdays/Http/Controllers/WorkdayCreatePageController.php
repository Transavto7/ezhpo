<?php

namespace Src\Employees\Workdays\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Point;
use App\User;
use Illuminate\Support\Facades\Auth;

final class WorkdayCreatePageController extends Controller
{
    public function __invoke()
    {
        /** @var User $user */
        $user = Auth::user();

        date_default_timezone_set('UTC');
        $time = time();
        $timezone = $user->timezone ?: 3;
        $time += $timezone * 3600;
        $time = date('Y-m-d\TH:i', $time);

        // Дефолтные значения
        $data['default_current_date'] = $time;
        $data['default_pv_id'] = $user->pv_id;
        $data['points'] = Point::getAll();

        // Проверяем выставленный ПВ
        $pvIdSessionKey = 'anketa_pv_id';
        if (session()->exists($pvIdSessionKey) && ((date('d.m') > session($pvIdSessionKey)['expired']))) {
            session()->remove($pvIdSessionKey);
        }

        return view('Workdays::create', $data);
    }
}
