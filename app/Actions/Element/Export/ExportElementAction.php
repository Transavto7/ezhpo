<?php
declare(strict_types=1);

namespace App\Actions\Element\Export;

final class ExportElementAction
{
    /** @var int|null */
    private $companyId;

    /** @var bool */
    private $exportAll;

    /**
     * @param int|null $companyId
     * @param bool $exportAll
     */
    public function __construct(?int $companyId, bool $exportAll)
    {
        $this->companyId = $companyId;
        $this->exportAll = $exportAll;
    }

    public function exportAll(): bool
    {
        return $this->exportAll;
    }

    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }
}
