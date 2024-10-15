<?php

namespace App\Actions\AnketsExportPdfLabeling;

use App\Anketa;
use App\Services\AnketsLabelingPDFGenerator\AnketsLabelingPDFGenerator;
use App\Services\QRCode\QRCodeGeneratorInterface;
use Illuminate\Http\Response;

final class AnketsExportPdfLabelingHandler
{
    /**
     * @var QRCodeGeneratorInterface
     */
    private $qrCodeGenerator;
    /**
     * @var AnketsLabelingPDFGenerator
     */
    private $pdfGenerator;

    /**
     * @param QRCodeGeneratorInterface $qrCodeGenerator
     * @param AnketsLabelingPDFGenerator $pdfGenerator
     */
    public function __construct(QRCodeGeneratorInterface $qrCodeGenerator, AnketsLabelingPDFGenerator $pdfGenerator)
    {
        $this->qrCodeGenerator = $qrCodeGenerator;
        $this->pdfGenerator = $pdfGenerator;
    }

    public function handle(AnketsExportPdfLabelingCommand $command): Response
    {
        $anketIds = Anketa::query()
            ->whereIn('id', $command->getAnketIds())
            ->pluck('uuid')
            ->toArray();

        $links = array_map(function (string $uuid) {
            return route('anket.validate', [
                'uuid' => $uuid
            ]);
        }, $anketIds);

        $cqrCodes = array_map(function (string $link) {
            return $this->qrCodeGenerator->generate($link);
        }, $links);

        return $this->pdfGenerator->generate($cqrCodes);
    }
}
