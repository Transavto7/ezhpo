export const usePageSetup = () => {
  const canEmployeeRead = window.PAGE_SETUP.canEmployeeRead

  return {
    canEmployeeRead,
  }
}
