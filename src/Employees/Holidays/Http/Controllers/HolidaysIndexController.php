<?php
declare(strict_types=1);

namespace Src\Employees\Holidays\Http\Controllers;

use Src\Employees\Holidays\Eloquent\Holiday;

final class HolidaysIndexController
{
    public function __invoke()
    {
        return view('Holidays::index', [
            'holidays' => Holiday::query()->pluck('date')->toArray(),
        ]);
    }
}
