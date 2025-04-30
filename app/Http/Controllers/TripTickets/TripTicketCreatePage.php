<?php

namespace App\Http\Controllers\TripTickets;

use App\Enums\FeaturesEnum;
use App\Http\Controllers\Controller;
use App\Models\Forms\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Unleash\Client\Unleash;

final class TripTicketCreatePage extends Controller
{
    public function __invoke(Request $request, Unleash $unleash)
    {
        if (!$unleash->isEnabled(FeaturesEnum::TRIP_TICKETS_ENABLED)) {
            return view('common.disabled-feature-page', ['title' => 'Создание путевого листа']);
        }

        date_default_timezone_set('UTC');
        $time = time();
        $user = Auth::user();

        $employee = $user->relatedEmployee;

        $timezone = $employee->timezone ?? 3;
        $time += $timezone * 3600;
        $time = date('Y-m-d', $time);
        $form = null;

        if ($request->query('form_id')) {
            $form = Form::find($request->query('form_id'));
        }

        return view('trip-tickets.create', [
            'title' => 'Создание путевого листа',
            'default_current_date' => $time,
            'form' => $form,
        ]);
    }
}
