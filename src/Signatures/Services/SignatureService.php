<?php

declare(strict_types=1);

namespace Src\Signatures\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Src\Signatures\Eloquent\Signature;
use Src\Signatures\Enums\DocumentType;
use Src\Signatures\Enums\SignatureStatus;

final class SignatureService
{
    private const HASH_ALGORITHM = 'md5';

    public function create(?UploadedFile $signatureFile, int $formId, DocumentType $documentType, string $documentPath): Signature
    {
        $signature = $this->get($formId, $documentType);

        if ($signature === null) {
            $signature = new Signature();
        }

        $signature->document_type = $documentType->getValue();
        $signature->form_id = $formId;
        $signature->status = SignatureStatus::NEED_SIGN()->getValue();

        if ($signatureFile instanceof UploadedFile) {
            $signature->path = Storage::disk('public')
                ->putFileAs(
                    $documentType->getValue(),
                    $signatureFile,
                    $signature->document_hash.'.'.$signatureFile->extension()
                );
            $signature->document_hash = $this->getFileHashByPath($documentPath);
            $signature->status = SignatureStatus::SIGNED()->getValue();
        }

        $signature->save();

        return $signature;
    }

    public function get(int $formId, DocumentType $documentType): ?Signature
    {
        /** @var Signature|null $signature */
        $signature = Signature::query()
            ->where('form_id', $formId)
            ->where('document_type', $documentType->getValue())
            ->first();

        return $signature;
    }

    public function check(string $filePath, int $formId, DocumentType $documentType): bool
    {
        $signature = $this->get($formId, $documentType);

        if ($signature === null) {
            return false;
        }

        if (
            Storage::disk('public')->exists($filePath) &&
            $signature->document_hash === $this->getFileHashByPath($filePath)
        ) {
            return true;
        }

        $signature->status = SignatureStatus::INVALIDATED;
        $signature->save();

        return false;
    }

    private function getFileHashByPath(string $filePath): string
    {
        return hash_file(self::HASH_ALGORITHM, $filePath);
    }
}
