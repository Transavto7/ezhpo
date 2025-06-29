export const useAppPageSetup = () => {
  const baseUrl: string = window.APP_PAGE_SETUP.baseUrl
  const notificationsPoolingInterval: number = +window.APP_PAGE_SETUP.notificationsPoolingInterval

  return {
    baseUrl,
    notificationsPoolingInterval,
  }
}
