import {onMounted, onUnmounted} from "vue";

export const useGlobalEvent = () => {
  const dispatchGlobalEvent = (eventName, detail) => {
    window.dispatchEvent(new CustomEvent(eventName, { detail: detail }))
  }

  const bindGlobalEventHandler = (eventName, handler) => {
    onMounted(() => {
      window.addEventListener(eventName, handler)
    })

    onUnmounted(() => {
      window.removeEventListener(eventName, handler)
    })
  }

  return {
    dispatchGlobalEvent,
    bindGlobalEventHandler,
  }
}
