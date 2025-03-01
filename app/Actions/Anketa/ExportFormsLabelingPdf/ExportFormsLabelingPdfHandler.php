<?php

namespace App\Actions\Anketa\ExportFormsLabelingPdf;

use App\Enums\FormLabelingType;
use App\Models\Forms\Form;
use App\Services\FormsLabelingPDFGenerator\FormLabelingPDFGeneratorItem;
use App\Services\FormsLabelingPDFGenerator\FormsLabelingPDFGenerator;
use App\Services\QRCode\QRCodeGeneratorInterface;
use DomainException;
use Illuminate\Http\Response;

final class ExportFormsLabelingPdfHandler
{
    /**
     * @var QRCodeGeneratorInterface
     */
    private $qrCodeGenerator;
    /**
     * @var FormsLabelingPDFGenerator
     */
    private $pdfGenerator;

    /**
     * @param QRCodeGeneratorInterface $qrCodeGenerator
     * @param FormsLabelingPDFGenerator $pdfGenerator
     */
    public function __construct(QRCodeGeneratorInterface $qrCodeGenerator, FormsLabelingPDFGenerator $pdfGenerator)
    {
        $this->qrCodeGenerator = $qrCodeGenerator;
        $this->pdfGenerator = $pdfGenerator;
    }

    public function handle(ExportFormsLabelingPdfCommand $command): Response
    {
        $forms = Form::query()
            ->select([
                'id',
                'uuid',
                'type_anketa'
            ])
            ->whereIn('id', $command->getFormIds())
            ->get()
            ->toArray();

        if (!count($forms)) {
            throw new DomainException('Forms not found');
        }

        $items = array_map(function (array $item) {
            $url = route('anketa.verification.page', [
                'uuid' => $item['uuid'],
            ]);
            $qrCode = $this->qrCodeGenerator->generate($url, QRCodeGeneratorInterface::VERSION_6);

            return new FormLabelingPDFGeneratorItem(
                $qrCode,
                $item['id'],
                FormLabelingType::fromString($item['type_anketa']),
            );
        }, $forms);

        return $this->pdfGenerator->generate($items);
    }
}
