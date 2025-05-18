import {useSidebarCounter} from "@/widgets/notifications-hub/useSidebarCounter";
import {ref} from "vue";
import {fetchNotifications, markAsViewed, markAsRead, markAsCompleted} from "@/widgets/notifications-hub/api";
import {onMounted, onUnmounted, watch} from "vue";
import {useAppPageSetup} from "@/composables/useAppPageSetup";
import Notify from "@/components/notify";
import {formatCreatedAt} from "@/widgets/notifications-hub/formatCreatedAt";

export const useNotifications = () => {
  const { notificationsPoolingInterval } = useAppPageSetup()
  const { updateSidebarCounter } = useSidebarCounter()

  let intervalId
  const isNotificationMode = ref(false)
  const isShowModal = ref(false)
  const pendingPerform = ref(false)
  const activeNotification = ref(null)
  const notifications = ref([])

  function removeNotificationById(id) {
    const index = notifications.value.findIndex((n) => n.id === id)

    if (index === -1) {
      return
    }

    notifications.value.splice(index, 1)

    if (notifications.value.length === 0) {
      activeNotification.value = null
      isShowModal.value = false
    } else if (index < notifications.value.length) {
      activeNotification.value = notifications.value[index]
    } else {
      activeNotification.value = notifications.value[index - 1]
    }
  }

  const fetchNotificationItems = async () => {
    try {
      const { data } = await fetchNotifications()

      const newNotifications = data
        .filter((item) => !notifications.value.find((i) => i.id === item.id))
        .map((item) => ({
          ...item,
          createdAtFormatted: formatCreatedAt(item.createdAt)
        }))

      notifications.value = [
        ...notifications.value,
        ...newNotifications,
      ]

      if (!activeNotification.value) {
        activeNotification.value = notifications.value?.[0] ?? null
      }

      const immediateNotification = notifications.value.find((item) => item.isImmediate)

      isShowModal.value = !!immediateNotification || (!!notifications.value.length && isNotificationMode.value)
    } catch (e) {
      console.error('Ошибка при загрузке уведомлений', e)
    }
  }

  const performAsViewed = async (id) => {
    try {
      pendingPerform.value = true

      await markAsViewed(id)
      notifications.value = notifications.value.map((item) => {
        if (item.id === id) {
          return {
            ...item,
            isViewed: true,
          }
        }

        return item;
      })

      pendingPerform.value = false
    } catch (e) {
      console.error(e)
    }
  }

  const performMarkAsRead = async (id) => {
    pendingPerform.value = true

    try {
      await markAsRead(id)
      pendingPerform.value = false

      removeNotificationById(id)
    } catch (e) {
      Notify.error('Ошибка при попытке отметить уведомление прочитанным')
    }
  }

  const performMarkAsCompleted = async (id) => {
    pendingPerform.value = true

    try {
      await markAsCompleted(id)
      pendingPerform.value = false

      removeNotificationById(id)
    } catch (e) {
      Notify.error('Ошибка при попытке отметить уведомление выполненным')
    }
  }

  watch(
    () => notifications.value,
    () => {
      updateSidebarCounter({
        count: notifications.value.length,
        countExpired: notifications.value.filter((item) => item.isExpired).length,
      })
    }
  )

  onMounted(async () => {
    await fetchNotificationItems()

    if (notificationsPoolingInterval) {
      intervalId = setInterval(async () => {
        await fetchNotificationItems()
      }, notificationsPoolingInterval)
    }
  })

  onUnmounted(() => {
    if (intervalId !== undefined) {
      clearInterval(intervalId)
    }
  })

  return {
    isNotificationMode,
    isShowModal,
    pendingPerform,
    activeNotification,
    notifications,
    fetchNotificationItems,
    performAsViewed,
    performMarkAsRead,
    performMarkAsCompleted,
  }
}
