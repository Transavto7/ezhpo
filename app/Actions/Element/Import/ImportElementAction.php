<?php
declare(strict_types=1);

namespace App\Actions\Element\Import;

final class ImportElementAction
{
    /** @var string */
    private $filePath;

    /**
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }
}
