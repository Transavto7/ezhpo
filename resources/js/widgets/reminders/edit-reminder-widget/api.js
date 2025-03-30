export const updateReminder = (reminder) => {
    return axios.post(`/reminders/${reminder.id}`, reminder);
}
