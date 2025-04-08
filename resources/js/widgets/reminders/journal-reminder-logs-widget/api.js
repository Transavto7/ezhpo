export const fetchRemindersTableItems = async (params) => {
    return await axios.post(`/reminders/log/show`, params);
}
