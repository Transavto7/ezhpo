let pluginInitialized = false;

// 1. Проверка и инициализация плагина КриптоПРО
async function initCadesPlugin() {
    if (typeof cadesplugin === 'undefined') {
        throw new Error('КриптоПРО Browser Plug-in не загружен. Установите плагин с https://cryptopro.ru/products/cades/plugin');
    }
    try {
        // Ожидаем полной загрузки плагина
        await cadesplugin;
        return true;
    } catch (e) {
        throw new Error('Ошибка инициализации КриптоПРО: ' + e.message);
    }
}

function SignHashAsync(dataToSign, thumbprint) {
    return new Promise(function (resolve, reject) {
        cadesplugin.async_spawn(function* (arg) {
            var oStore;
            try {
                oStore = yield cadesplugin.CreateObjectAsync("CAdESCOM.Store");

                yield oStore.Open(
                    cadesplugin.CAPICOM_CURRENT_USER_STORE,
                    cadesplugin.CAPICOM_MY_STORE,
                    cadesplugin.CAPICOM_STORE_OPEN_MAXIMUM_ALLOWED
                );

                var allCertificates = yield oStore.Certificates;

                // var oCertificates = yield allCertificates.Find(cadesplugin.CAPICOM_CERTIFICATE_FIND_SHA1_HASH, thumbprint);

                var certificatesCount = yield allCertificates.Count;
                if (certificatesCount === 0) {
                    return reject("Сертификат не найден в хранилище.");
                }
                var oCertificate = yield allCertificates.Item(1);

                try {
                    var oSigner = yield cadesplugin.CreateObjectAsync("CAdESCOM.CPSigner");
                    oSigner.propset_Certificate(oCertificate);
                } catch (err) {
                    return reject("Не удалось создать Подписчика. " + (err));
                }
                // Подписываю данные base64 с сервера
                var oSignedData = yield cadesplugin.CreateObjectAsync("CAdESCOM.CadesSignedData");
                if (dataToSign) {
                    try {
                        yield oSignedData.propset_ContentEncoding(cadesplugin.CADESCOM_BASE64_TO_BINARY);
                        yield oSignedData.propset_Content(dataToSign);
                        var Signature = yield oSignedData.SignCades(oSigner, cadesplugin.CADESCOM_CADES_BES, true);
                    } catch (err) {
                        console.log(err)
                        return reject("Не удалось создать подпись по данным " + (err));
                    }
                }
                return resolve(Signature);
            } catch (err) {
                return reject("Не удалось создать подпись. " + (err));
            } finally {
                if (oStore) {
                    yield oStore.Close();
                }
            }
        });
    });
}

async function readAsBase64(blob) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result.split(',')[1]);
        reader.onerror = () => reject("Ошибка при чтении файла");
        reader.readAsDataURL(blob);
    });
}

async function uploadSignature(signature) {
    const blob = new Blob([signature], {type: 'application/pkcs7-signature'});
    const formData = new FormData();
    formData.append("signature", blob, "signature.p7s");

    const response = await fetch(window.PAGE_SETUP.SIGN_URLS.uploadSignature, {
        method: "POST",
        body: formData,
        headers: {
            "X-CSRF-Token": $('input[name="_token"]').val()
        },
    });

    if (!response.ok) throw new Error("Ошибка при загрузке подписи");
}

export const signPDF = async () => {
    try {
        if (pluginInitialized) {
            const btn = document.getElementById('signBtn');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Подписание...';
            }

            // 1. Получаем PDF
            const response = await fetch(window.PAGE_SETUP.SIGN_URLS.getPDF);
            const signData = await readAsBase64(await response.blob());

            const signedPdf = await SignHashAsync(signData)
            uploadSignature(signedPdf).then(() => {
                location.reload();
            });
        } else {
            throw new Error('КриптоПРО Browser Plug-in не загружен. Установите плагин с https://cryptopro.ru/products/cades/plugin');
        }
    } catch (e) {
        console.error('Ошибка инициализации:', e);
    } finally {
        const btn = document.getElementById('signBtn');
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'Подписать PDF';
        }
    }
}

window.initSignPlugin = () => {
    // 3. Обработчик кнопки
    const button = document.getElementById('signBtn');
    if (button) {
        button.addEventListener('click', signPDF);
    }

    //4. Проверка доступности плагина при загрузке страницы
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            pluginInitialized = await initCadesPlugin();
            console.log('КриптоПРО Plug-in готов к использованию');
        } catch (e) {
            console.log('Ошибка инициализации:', e);
            $('#signBtn').hide();
        }
    })
}




