<?php
declare(strict_types=1);

namespace App\Actions\Element\Import;

final class ImportElementResponse
{
    /** @var int */
    private $allRows;

    /** @var int */
    private $acceptedRow;

    /** @var int */
    private $errorRows;

    /** @var string|null */
    private $errorFileUrl;

    /**
     * @param int $allRows
     * @param int $acceptedRow
     * @param int $errorRows
     * @param string|null $errorFileUrl
     */
    public function __construct(
        int     $allRows,
        int     $acceptedRow,
        int     $errorRows = 0,
        ?string $errorFileUrl = null
    )
    {
        $this->allRows = $allRows;
        $this->acceptedRow = $acceptedRow;
        $this->errorRows = $errorRows;
        $this->errorFileUrl = $errorFileUrl;
    }

    public function hasErrors(): bool
    {
        return $this->errorRows > 0;
    }

    public function toArray(): array
    {
        return [
            'allRows' => $this->allRows,
            'acceptedRows' => $this->acceptedRow,
            'errorRows' => $this->errorRows,
            'errorFileUrl' => $this->errorFileUrl,
            'hasError' => $this->hasErrors(),
        ];
    }
}
