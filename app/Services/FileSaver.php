<?php
declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

final class FileSaver
{
    public static function save(UploadedFile $file, $disk = 'public'): string
    {
        $fileName = sprintf('%s_%s', Carbon::now()->timestamp, $file->getClientOriginalName());
        $file->storeAs('', $fileName, ['disk' => $disk]);

        return $fileName;
    }
}
