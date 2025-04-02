export const fetchModalData = (data) => {
    console.log('Запросил данные для JSON: ', data)

    return new Promise((resolve) => {
        resolve({
            id: 123,
            name: 'Важное уведомление!',
            content: '<span>Текст уведомления <b>с HTML</b></span>'
        });
    })
}

export const sendActionOk = (id) => {
    console.log('Отправлен ответ OK для модального окна с id:', id);
}

export const sendActionComplete = (id) => {
    console.log('Отправлен ответ COMPLETE для модального окна с id:', id);
}
