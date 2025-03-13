<?php

namespace Src\Employees\Workdays\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Point;

final class WorkdayCreatePageController extends Controller
{
    public function __invoke()
    {
        $data['points'] = Point::getAll();

        return view('Workdays::create', $data);
    }
}
