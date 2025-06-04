export const createNotificationsByContext = (data) => {
    return axios.post(`/notifications/by-context`, data);
}

export const fetchNotifications = () => {
    return axios.post(`/notifications/unread`);
}

export const markAsRead = (id) => {
  axios.post(`/notifications/${id}/mark-as-read`);
}

export const markAsCompleted = (id) => {
    axios.post(`/notifications/${id}/mark-as-completed`);
}
