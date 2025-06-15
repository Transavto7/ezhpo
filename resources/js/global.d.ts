// globals.d.ts
export {}

declare global {
  interface Window {
    PAGE_SETUP: Record<string, any>
    APP_PAGE_SETUP: Record<string, any>
  }
}
