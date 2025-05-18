export const useAppPageSetup = () => {
  const baseUrl = window.APP_PAGE_SETUP.baseUrl
  const notificationsPoolingInterval = window.APP_PAGE_SETUP.notificationsPoolingInterval

  return {
    baseUrl,
    notificationsPoolingInterval,
  }
}
