export const fetchNotificationLogsTableItems = async (params) => {
    return await axios.post(`/notifications/logs`, params);
}
