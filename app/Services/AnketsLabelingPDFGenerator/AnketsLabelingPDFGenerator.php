<?php

namespace App\Services\AnketsLabelingPDFGenerator;

use App\Enums\AnketLabelingType;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade as PDF;

final class AnketsLabelingPDFGenerator
{
    /**
     * @param AnketLabelingPDFGeneratorItem[] $items
     * @return Response
     * @throws BindingResolutionException
     */
    public function generate(array $items): Response
    {
        $logoImage = $this->getAssetImage('anket_labeling_1.png');
        $arrowsImage = $this->getAssetImage('anket_labeling_2.png');
        $customPaper = array(0, 0, 250.00, 484.00);
        $domain = preg_replace('/^https?:\/\//', '', config('app.url'));

        $pages = [];

        foreach ($items as $item) {
            $prefixes = [
                AnketLabelingType::MEDIC => 'МО',
                AnketLabelingType::TECH => 'ТО',
            ];

            $id = $prefixes[$item->getAnketType()->value()] . '-' . $item->getId();

            $pages[] = [
                'qrCode' => $item->getQrCode(),
                'id' => $id,
            ];
        }

        $pdf = PDF::loadView('templates.anket-labeling', [
            'pages' => $pages,
            'logoImage' => $logoImage,
            'arrowsImage' => $arrowsImage,
            'domain' => $domain,
        ])->setPaper($customPaper, 'landscape');

        $response = response()->make($pdf->output(), 200);
        $response->header('Content-Type', 'application/pdf');

        return $response;
    }

    private function getAssetImage(string $assetName): string
    {
        $imagePath = public_path('images/anket_labeling/'.$assetName);
        $imageData = file_get_contents($imagePath);
        $base64Image = base64_encode($imageData);

        return 'data:image/png;base64,' . $base64Image;
    }
}
