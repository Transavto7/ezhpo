import { GlobalEvent, ReminderAction, ReminderSubjectType } from '@/types'
import { useGlobalEvent } from '@/services/global-events/useGlobalEvent'

type Context = {
  city?: number
  company?: number
  point?: number
  roles?: number[]
  role?: number
  subject?: number
  subject_type?: ReminderSubjectType
}

export const useSendNotification = () => {
  const { dispatchGlobalEvent } = useGlobalEvent()

  const sendNotification = (action: ReminderAction) => {
    const context: Context = {}

    return {
      city(value: number) {
        context.city = value
        return this
      },
      company(value: number) {
        context.company = value
        return this
      },
      point(value: number) {
        context.point = value
        return this
      },
      roles(value: number[]) {
        context.roles = value
        return this
      },
      role(value: number) {
        context.role = value
        return this
      },
      subject(value: number) {
        context.subject = value
        return this
      },
      subjectType(value: ReminderSubjectType) {
        context.subject_type = value
        return this
      },
      exec() {
        dispatchGlobalEvent(GlobalEvent.SEND_NOTIFICATION, {
          context,
          action,
        })
      },
    }
  }

  return {
    sendNotification,
  }
}
