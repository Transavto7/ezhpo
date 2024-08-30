<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DriverController extends Controller
{
    public function index(): View
    {
        return view('pages.driver');
    }
}
