// globals.d.ts
import { GlobalEventDispatcher } from '@/services/global-events/types'

export {}

declare global {
  interface Window {
    PAGE_SETUP: Record<string, any>
    APP_PAGE_SETUP: Record<string, any>
    dispatchGlobalEvent: GlobalEventDispatcher
  }
}
