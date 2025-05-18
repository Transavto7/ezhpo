export const fetchNotificationTableItems = async (params) => {
    return await axios.post(`/notifications`, params);
}

export const markAsRead = async (id) => {
    return await axios.post(`/notifications/${id}/mark-as-read`);
}

export const markAsCompleted = async (id) => {
    return await axios.post(`/notifications/${id}/mark-as-completed`);
}
