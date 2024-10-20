<?php

namespace App\Actions\Anketa\ExportAnketasLabelingPdf;

use App\Anketa;
use App\Enums\AnketLabelingType;
use App\Services\AnketsLabelingPDFGenerator\AnketLabelingPDFGeneratorItem;
use App\Services\AnketsLabelingPDFGenerator\AnketsLabelingPDFGenerator;
use App\Services\QRCode\QRCodeGeneratorInterface;
use DomainException;
use Illuminate\Http\Response;

final class ExportAnketasLabelingPdfHandler
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

    public function handle(ExportAnketasLabelingPdfCommand $command): Response
    {
        $ankets = Anketa::query()
            ->select([
                'id',
                'uuid',
                'type_anketa'
            ])
            ->whereIn('id', $command->getAnketIds())
            ->whereNotNull('uuid')
            ->whereNull('deleted_at')
            ->where('in_cart', '<>', 1)
            ->get()
            ->toArray();

        if (!count($ankets)) {
            throw new DomainException('Ankets not found');
        }

        $items = array_map(function (array $item) {
            $url = route('anketa.verification.page', [
                'uuid' => $item['uuid'],
            ]);
            $qrCode = $this->qrCodeGenerator->generate($url, QRCodeGeneratorInterface::VERSION_6);

            return new AnketLabelingPDFGeneratorItem(
                $qrCode,
                $item['id'],
                AnketLabelingType::fromString($item['type_anketa']),
            );
        }, $ankets);

        return $this->pdfGenerator->generate($items);
    }
}
