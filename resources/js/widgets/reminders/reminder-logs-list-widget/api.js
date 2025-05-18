export const fetchRemindersTableItems = async (params) => {
    return await axios.post(`/reminders/logs`, params);
}
