export const usePageSetup = () => {
  const canViewOther = window.PAGE_SETUP.canViewOther
  const canChangeOther = window.PAGE_SETUP.canChangeOther
  const canEmployeeRead = window.PAGE_SETUP.canEmployeeRead
  const canRemindersRead = window.PAGE_SETUP.canRemindersRead
  const statusOptions = window.PAGE_SETUP.statusOptions

  return {
    canViewOther,
    canChangeOther,
    canEmployeeRead,
    canRemindersRead,
    statusOptions,
  }
}