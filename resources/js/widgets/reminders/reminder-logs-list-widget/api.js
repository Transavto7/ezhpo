export const fetchRemindersTableItems = async (params) => {
  return axios.post(`/reminders/logs`, params)
}
