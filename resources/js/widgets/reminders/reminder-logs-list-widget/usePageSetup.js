export const usePageSetup = () => {
  const canRemindersRead = window.PAGE_SETUP.canRemindersRead
  const canEmployeeRead = window.PAGE_SETUP.canEmployeeRead

  return {
    canEmployeeRead,
    canRemindersRead,
  }
}
