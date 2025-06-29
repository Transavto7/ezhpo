export const GlobalEvent = {
  SEND_NOTIFICATION: 'send_notification',
} as const
export type GlobalEvent = (typeof GlobalEvent)[keyof typeof GlobalEvent]

export const ReminderAction = {
  CREATE_INSPECTION: 'create_inspection',
  AUTH: 'auth',
} as const
export type ReminderAction = (typeof ReminderAction)[keyof typeof ReminderAction]

export const ReminderSubjectType = {
  CAR: 'car',
  DRIVER: 'driver',
} as const
export type ReminderSubjectType = (typeof ReminderSubjectType)[keyof typeof ReminderSubjectType]
