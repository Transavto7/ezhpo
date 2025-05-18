export const useSidebarCounter = () => {
  const updateSidebarCounter = (params) => {
    let { count, countExpired } = params

    if (count > 99) {
      count = '99+'
    }
    if (countExpired > 99) {
      countExpired = '99+'
    }

    const countElement = document.getElementById('countUnreadNotifications');
    if (countElement) {
      if (count > 0) {
        countElement.style.display = ''
        countElement.textContent = count
      } else {
        countElement.style.display = 'none'
      }
    }

    const countExpiredElement = document.getElementById('expiredNotificationsCount');
    if (countExpiredElement) {
      if (countExpired > 0) {
        countExpiredElement.style.display = ''
        countExpiredElement.textContent = countExpired
      } else {
        countExpiredElement.style.display = 'none'
      }
    }
  }

  return {
    updateSidebarCounter,
  }
}
