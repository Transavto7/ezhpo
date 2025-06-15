export type Classifier = {
  id: string | number
  name: string
}

type SelectFnParams = { search: string | null } & Record<string, unknown>
export type SelectFn<T extends SelectFnParams = SelectFnParams> = (
  params: T,
) => Promise<Classifier[]>

export type ReminderConditions = {
  city: Classifier | null
  company: Classifier | null
  point: Classifier | null
  role: Classifier | null
  subject: Classifier | null
  subject_type: Classifier | null
  user: Classifier | null
}

export type Reminder = {
  id: string
  title: string
  content: string
  conditions: ReminderConditions
  expiresAt: string | null
  expiresInMinutes: number | null
  status: Classifier | null
  action: Classifier | null
  type: Classifier | null
  hiddenFromInitiator: boolean
  usersToNotify: Classifier[]
}
