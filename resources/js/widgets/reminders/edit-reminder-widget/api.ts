import axios from 'axios'
import { Reminder } from '@/widgets/reminders/types'

export const updateReminder = async (reminder: Reminder) => {
  return axios.post(`/reminders/${reminder.id}`, reminder)
}
