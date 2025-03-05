<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Http\Controllers;

final class CreateTariffPageController
{
    public function __invoke()
    {
        return view('Tariffs::create');
    }
}
