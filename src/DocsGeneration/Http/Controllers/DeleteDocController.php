<?php
declare(strict_types=1);

namespace Src\DocsGeneration\Http\Controllers;

use App\Enums\UserActionTypesEnum;
use App\Http\Controllers\Controller;
use App\Models\Forms\Form;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Src\DocsGeneration\DocDataService;

final class DeleteDocController extends Controller
{
    public function __invoke($type, $anketaId)
    {
        $form = Form::withTrashed()->find($anketaId);

        if (! $form) {
            return back()->withErrors(['Осмотр не найден']);
        }

        $form->details->update([
            $type.'_path' => null,
        ]);

        return back();
    }
}
