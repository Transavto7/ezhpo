<?php
declare(strict_types=1);

namespace App\Http\Controllers\Elements;

use App\Actions\Element\Export\ExportElementHandlerFactory;
use App\Enums\ElementType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

final class ExportElementController extends Controller
{
    public function __invoke(string $type)
    {
        $handler = ExportElementHandlerFactory::make(ElementType::fromString(mb_strtolower($type)));
        $fileName = $handler->handle();

        return Storage::disk('export')->download($fileName);
    }
}
