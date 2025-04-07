export const fetchModalData = (data) => {
    return axios.post(`/reminders/by-context`, data);
}

export const sendActionOk = (id) => {
}

export const sendActionComplete = (id) => {
    axios.post(`/reminders/complete-reminder-modal/${id}`);
}

export const showModal = (id) => {
    axios.post(`/reminders/show-reminder-modal/${id}`);
}
