import { GlobalEventDispatcher } from '@/services/global-events/types'

/**
 * Этот хелпер нужен исключительно для того, чтобы эмитить глобальные события из блейдов или старых компонентов с Options API.
 * В идеале вообще его не использовать и переходить на useGlobalEvents.
 *
 * А вот обработка глобальных событий предоставляется только хуком useGlobalEvents
 *
 * @param eventName
 * @param detail
 */
export const dispatchGlobalEvent: GlobalEventDispatcher = (eventName, detail) => {
  window.dispatchEvent(new CustomEvent(eventName, { detail }))
}

export const registerDispatchGlobalEventHelper = () => {
  // @ts-ignore
  window.dispatchGlobalEvent = dispatchGlobalEvent
}
