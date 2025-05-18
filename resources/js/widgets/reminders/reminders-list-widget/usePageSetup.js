export const usePageSetup = () => {
  const canCreate = window.PAGE_SETUP.canCreate
  const canEdit = window.PAGE_SETUP.canEdit
  const canDelete = window.PAGE_SETUP.canDelete

  return {
    canCreate,
    canEdit,
    canDelete,
  }
}