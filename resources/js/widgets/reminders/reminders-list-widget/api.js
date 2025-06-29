import axios from 'axios'

export const fetchRemindersTableItems = async (params) => {
  return axios.post(`/reminders/list`, params)
}

export const deleteReminderApi = async (id) => {
  return axios.post(`/reminders/delete`, { reminder_id: id })
}

export const switchReminderStatus = async (id, enabled) => {
  return axios.post(`/reminders/${id}/switch-status`, { enabled })
}
