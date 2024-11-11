<?php

namespace App\Actions\Anketa\ExportFormsLabelingPdf;

final class ExportFormsLabelingPdfCommand
{
    /**
     * @var string[]
     */
    private $formIds;

    /**
     * @param string[] $formIds
     */
    public function __construct(array $formIds)
    {
        $this->formIds = $formIds;
    }

    public function getFormIds(): array
    {
        return $this->formIds;
    }


}
