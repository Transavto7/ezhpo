export const fetchModalData = (data) => {
    return axios.post(`/reminders/by-context`, data);
}

export const sendActionOk = (id) => {
    console.log('Отправлен ответ OK для модального окна с id:', id);
}

export const sendActionComplete = (id) => {
    console.log('Отправлен ответ COMPLETE для модального окна с id:', id);
}
