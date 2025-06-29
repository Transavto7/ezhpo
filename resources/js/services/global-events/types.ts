import { GlobalEvent } from '@/types'

export type GlobalEventDispatcher = <T extends object = object>(
  eventName: GlobalEvent,
  detail: T,
) => void

export type GlobalEventHandler<T = any> = (event: CustomEvent<T>) => void
