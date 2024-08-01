import {Html5QrcodeScanner} from "html5-qrcode";

let html5QrcodeScanner = new Html5QrcodeScanner(
    "reader",
    { fps: 2, qrbox: {width: 250, height: 250} },
    /* verbose= */ true);

function onScanSuccess(decodedText, decodedResult) {
    console.log(`Scan result: ${decodedText}`, decodedResult);
    html5QrcodeScanner.clear().then(r => console.log('close camera'));

    axios
        .get('/api/parse-qr-code', {
            params: {
                decodedText
            }
        })
        .then(response => {
            $('#cameraModal').modal('hide')
            const { data } = response

            console.log(data)
        })
        .catch(error => {
            alert(error.response.data.error)
        })
}

function onScanFailure(error) {
    console.warn(`Code scan error = ${error}`);
}

$(document).ready(function() {
    $('.camera-btn').click(function (e) {
        e.stopPropagation()
        e.preventDefault()

        html5QrcodeScanner.render(onScanSuccess, onScanFailure);

        $('#cameraModal').modal('show')
    })

    $('.close-modal').click(function (e) {
        e.stopPropagation()
        e.preventDefault()

        $('#cameraModal').modal('hide')

        html5QrcodeScanner.clear()
    })
})
