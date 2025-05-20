<?php

namespace Src\Documents\Http\Controllers;

use App\Http\Controllers\Controller;

class DocumentsPageController extends Controller
{
    public function __invoke()
    {
        return view('Documents::index');
    }
}
