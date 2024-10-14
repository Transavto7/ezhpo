<?php

namespace App\Actions\AnketsExportPdfLabeling;

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
        $this->checkAnkets($command->getAnketIds());

        // todo: uuids
//        $anketIds = Anketa::query()
//            ->whereIn('id', $command->getAnketIds())
//            ->pluck('hash');

        $anketIds = $command->getAnketIds();

        $links = array_map(function (string $id) {
            // todo: route
            return route('ankets.validate', ['uuid' => $id]);
        }, $anketIds);

        $cqrCodes = array_map(function (string $link) {
            return $this->qrCodeGenerator->generate($link);
        }, $links);

        return $this->pdfGenerator->generate($cqrCodes);
    }

    /**
     * @param string[] $anketIds
     * @return void
     */
    private function checkAnkets(array $anketIds) {
        // todo: check ankets
    }
}
