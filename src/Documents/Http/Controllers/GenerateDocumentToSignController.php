<?php

namespace Src\Documents\Http\Controllers;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use TCPDF;

class GenerateDocumentToSignController extends Controller
{
    public function __invoke(): BinaryFileResponse
    {
        $pdf = new TCPDF();

        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('PDF с ЭЦП');
        $pdf->SetSubject('Подписание документа');

        // Добавляем страницу
        $pdf->AddPage();

        // Добавляем текст
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Write(0, 'Этот документ требует подписи ЭЦП.');

        $pdf->setSignature(
            '', // Пустая подпись (будет добавлена позже)
            '', // Пустой сертификат
            '',  // Не подписывать сразу
            [],
            'SignatureField' ,
            [
                'Name' => 'Иван Иванов',
                'Location' => 'Москва',
                'Reason' => 'Подтверждение документа',
                'ContactInfo' => 'ivan@example.com',
            ],// Имя поля подписи
        );

        // Сохраняем PDF во временный файл
        $pdfPath = storage_path('app/public/document_with_signature.pdf');
        $pdf->Output($pdfPath, 'F');

        return response()->download($pdfPath, 'document_to_sign.pdf');
    }
}
