<?php

declare(strict_types=1);

namespace Src\Signatures\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Facade;
use Src\Signatures\Eloquent\Signature;
use Src\Signatures\Enums\DocumentType;

/**
 * @method static Signature create(?UploadedFile $signatureFile, int $formId, DocumentType $documentType, string $documentPath)
 * @method static Signature|null get(int $formId, DocumentType $documentType)
 * @method static bool check(string $filePath, int $formId, DocumentType $documentType)
 *
 * @see SignatureService
 */
final class SignatureWorker extends Facade
{
    const NAME = 'signature-worker';

    protected static function getFacadeAccessor(): string
    {
        return self::NAME;
    }
}
