import { onMounted, onUnmounted } from 'vue'
import { GlobalEvent } from '@/types'
import { GlobalEventDispatcher, GlobalEventHandler } from '@/services/global-events/types'

export const useGlobalEvent = () => {
  const dispatchGlobalEvent: GlobalEventDispatcher = (eventName, detail) => {
    window.dispatchEvent(new CustomEvent(eventName, { detail }))
  }

  const bindGlobalEventHandler = <T = any>(
    eventName: GlobalEvent,
    handler: GlobalEventHandler<T>,
  ) => {
    const listener = handler as EventListener

    onMounted(() => {
      window.addEventListener(eventName, listener)
    })

    onUnmounted(() => {
      window.removeEventListener(eventName, listener)
    })
  }

  return {
    dispatchGlobalEvent,
    bindGlobalEventHandler,
  }
}
