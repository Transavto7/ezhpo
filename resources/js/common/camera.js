import {Html5Qrcode} from "html5-qrcode";

$(document).ready(function() {
    const qrCodeReaderId = "qr-code-reader";
    const readerExists = document.getElementById(qrCodeReaderId);

    if (!readerExists) return;

    const html5QrCode = new Html5Qrcode(qrCodeReaderId, false);
    const config = { fps: 2, showTorchButtonIfSupported: true, qrbox: { width: 300, height: 300 } };

    const ui = {
        errorAlert: $('.qr-error'),
        cameraBtn: $('.camera-btn'),
        cameraModal: $('#cameraModal'),
        closeBtn: $('.close-modal'),
        errorDiv: $('.error-div'),
        restartBtn: $('.restart-btn'),
        camera: $('#reader'),
    }

    let $currentInput = null
    let fieldType = null

    function onScanSuccess(decodedText, decodedResult) {
        console.log(`Scan result: ${decodedText}`, decodedResult);
        html5QrCode.stop().then(r => console.log('close camera'));

        axios
            .get('/api/parse-qr-code', {
                params: {
                    decodedText,
                    fieldType,
                }
            })
            .then(response => {
                $('#cameraModal').modal('hide')
                const { data } = response
                $currentInput.val(data.id).trigger('input')
            })
            .catch(error => {
                ui.camera.attr('style', 'display:none')
                ui.errorAlert.html('Ошибка сканирования QR кода: ' + error.response.data.error)
                ui.errorDiv.attr('style', '')
            })
    }

    function onScanFailure(error) {
        console.warn(`Code scan error = ${error}`);
    }

    $(document).on('click', '.camera-btn', function (e) {
        e.stopPropagation()
        e.preventDefault()

        const $cameraBtn = $(this)

        $currentInput = $cameraBtn.siblings('input')
        fieldType = $cameraBtn.data('field-type')

        console.log($currentInput, fieldType)

        html5QrCode.start({facingMode: "environment"}, config, onScanSuccess, onScanFailure)

        ui.cameraModal.modal('show')
    })

    ui.restartBtn.click(function () {
        ui.errorDiv.attr('style', 'display:none')
        ui.camera.attr('style', '')

        html5QrCode.start({facingMode: "environment"}, config, onScanSuccess, onScanFailure)
    })

    ui.closeBtn.click(function (e) {
        e.stopPropagation()
        e.preventDefault()

        ui.cameraModal.modal('hide')
    })

    ui.cameraModal.on('hidden.bs.modal', function () {
        ui.errorDiv.attr('style', 'display:none')
        ui.camera.attr('style', '')
        html5QrCode.stop()
        $currentInput = null
        fieldType = null
    })
})
