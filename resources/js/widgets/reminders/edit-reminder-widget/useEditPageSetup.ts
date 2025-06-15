import { ref } from 'vue'
import { Reminder } from '@/widgets/reminders/types'

export const useEditPageSetup = () => {
  const reminder = ref<Reminder>(window.PAGE_SETUP.reminder)

  return {
    reminder,
  }
}
