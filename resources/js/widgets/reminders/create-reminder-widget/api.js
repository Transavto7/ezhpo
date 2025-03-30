export const createReminder = (reminder) => {
    return axios.post('/reminders/create', reminder);
}
