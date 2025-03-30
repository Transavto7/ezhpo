export const fetchRemindersTableItems = async (params) => {
    return await axios.post(`/reminders/list`, params);
}

export const deleteReminderApi = async (id) => {
    return await axios.post(`/reminders/delete`, {reminder_id: id});
}
