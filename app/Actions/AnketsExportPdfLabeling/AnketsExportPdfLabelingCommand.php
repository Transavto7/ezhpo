<?php

namespace App\Actions\AnketsExportPdfLabeling;

final class AnketsExportPdfLabelingCommand
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
