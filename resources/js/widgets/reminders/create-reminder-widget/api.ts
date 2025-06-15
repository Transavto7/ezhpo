import axios from 'axios'
import { Reminder } from '@/widgets/reminders/types'

export const createReminder = (reminder: Reminder) => {
  return axios.post('/reminders/create', reminder)
}
