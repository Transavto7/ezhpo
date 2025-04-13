export const fetchModalData = (data) => {
    return axios.post(`/notifications/by-context`, data);
}

export const fetchUnreadNotifications = () => {
    return axios.post(`/notifications/unread`);
}

export const sendActionOk = (id) => {
}

export const sendActionComplete = (id) => {
    axios.post(`/notifications/${id}/complete`);
}

export const showModal = (id) => {
    axios.post(`/notifications/${id}/show`);
}
