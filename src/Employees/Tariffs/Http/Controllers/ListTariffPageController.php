<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Http\Controllers;

final class ListTariffPageController
{
    public function __invoke()
    {
        return view('Tariffs::list');
    }
}
