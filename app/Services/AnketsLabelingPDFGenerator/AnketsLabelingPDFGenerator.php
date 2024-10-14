<?php

namespace App\Services\AnketsLabelingPDFGenerator;

use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade as PDF;

final class AnketsLabelingPDFGenerator
{
    public function generate(array $qrCodes): Response
    {
        $imageBg = $this->getBg();
        $customPaper = array(0, 0, 354.00, 685.00);

        $views = [];

        foreach ($qrCodes as $qrCode) {
            $views[] = [
                'qrCode' => $qrCode,
                'imageBg' => $imageBg,
            ];
        }

        $pdf = PDF::loadView('templates.anket-labeling', ['pages' => $views])->setPaper($customPaper, 'landscape');

        $response = response()->make($pdf->output(), 200);
        $response->header('Content-Type', 'application/pdf');

        return $response;
    }

    private function getBg(): string
    {
        $imagePath = public_path('images/anket_labeling.png');
        $imageData = file_get_contents($imagePath);
        $base64Image = base64_encode($imageData);

        return 'data:image/png;base64,' . $base64Image;
    }
}
