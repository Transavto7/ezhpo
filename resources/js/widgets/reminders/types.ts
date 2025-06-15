export type Classifier = {
    id: string | number
    name: string
}

type SelectFnParams = { search: string | null }
export type SelectFn<T extends SelectFnParams = SelectFnParams> = (params: T) => Promise<Classifier[]>

export type Reminder = {
    id: string
    title: string
    content: string
    conditions: Classifier[]
    expiresAt: string | null
    expiresInMinutes: number | null
    status: Classifier | null
    action: Classifier | null
    type: Classifier | null
    hiddenFromInitiator: boolean
    usersToNotify: Classifier[]
}
