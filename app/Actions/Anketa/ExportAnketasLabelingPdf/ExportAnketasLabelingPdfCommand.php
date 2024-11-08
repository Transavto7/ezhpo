<?php

namespace App\Actions\Anketa\ExportAnketasLabelingPdf;

final class ExportAnketasLabelingPdfCommand
{
    /**
     * @var string[]
     */
    private $anketIds;

    /**
     * @param string[] $anketIds
     */
    public function __construct(array $anketIds)
    {
        $this->anketIds = $anketIds;
    }

    public function getAnketIds(): array
    {
        return $this->anketIds;
    }


}
