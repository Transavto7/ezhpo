@extends('layouts.app')

@section('title', 'Подписание документа')
@section('sidebar', 1)

@section('custom-scripts')
    <script language="javascript" src="https://www.cryptopro.ru/sites/default/files/products/cades/cadesplugin_api.js"></script>
    <script type="text/javascript">
        document.getElementById('signButton').addEventListener('click', async () => {
            try {
                // Загружаем PDF с сервера
                const response = await fetch('/documents/generate-pdf');
                const pdfBlob = await response.blob();

                // Подписываем PDF через КриптоПро
                const signedPdf = await cadesplugin.sign(
                    pdfBlob,
                    {
                        type: 'PDF',
                        detached: false, // Подпись встраивается в PDF
                        signatureField: 'SignatureField' // Имя поля подписи
                    }
                );

                // Скачиваем подписанный PDF
                const link = document.createElement('a');
                link.href = URL.createObjectURL(signedPdf);
                link.download = 'signed_document.pdf';
                link.click();
            } catch (error) {
                console.error('Ошибка подписания:', error);
                alert('Ошибка подписания: ' + error.message);
            }
        });
    </script>
@endsection

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h1>Подписание PDF</h1>
                <button id="signButton">Подписать PDF</button>
            </div>
        </div>
    </div>
@endsection
