<?php

namespace Src\DocsGeneration\Http\Controllers;

use App\Enums\UserActionTypesEnum;
use App\Events\UserActions\ClientDocumentRequest;
use App\Http\Controllers\Controller;
use App\Models\Forms\Form;
use Src\DocsGeneration\DocDataService;
use App\User;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

abstract class DocsController extends Controller
{
    protected function sendEvent(string $actionType)
    {
        /** @var User $user */
        $user = Auth::user();
        event(new ClientDocumentRequest($user, $actionType));
    }
}
