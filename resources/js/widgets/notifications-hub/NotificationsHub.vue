<script setup>
import { watch } from 'vue'
import { GlobalEvent } from '@/types'
import { useGlobalEvent } from '@/services/global-events/useGlobalEvent'
import ModeSwitcher from '@/widgets/notifications-hub/ModeSwitcher.vue'
import { createNotificationsByContext } from '@/widgets/notifications-hub/api'
import { useNotifications } from '@/widgets/notifications-hub/useNotifications'
import HubOverlay from './HubOverlay.vue'

const { bindGlobalEventHandler } = useGlobalEvent()
const {
  isNotificationMode,
  isShowModal,
  pendingPerform,
  activeNotification,
  notifications,
  fetchNotificationItems,
  performMarkAsCompleted,
  performMarkAsRead,
} = useNotifications()

bindGlobalEventHandler(GlobalEvent.SEND_NOTIFICATION, async (event) => {
  await createNotificationsByContext(event.detail)
  await fetchNotificationItems()
})

const handleSelect = (item) => {
  activeNotification.value = item
}

const handleClose = () => {
  isShowModal.value = false
}

const handleMarkAsRead = async (id) => {
  await performMarkAsRead(id)
}

const handleMarkAsCompleted = async (id) => {
  await performMarkAsCompleted(id)
}

watch(
  () => isShowModal.value,
  (newValue) => {
    if (newValue) {
      document.body.classList.add('overflow-hidden')
    } else {
      document.body.classList.remove('overflow-hidden')

      activeNotification.value = null
      notifications.value = []
    }
  },
)
</script>

<template>
  <div class="mr-4">
    <mode-switcher v-model="isNotificationMode" />

    <hub-overlay
      :active-notification="activeNotification"
      :is-show="isShowModal"
      :notifications="notifications"
      :pending="pendingPerform"
      @close="handleClose"
      @mark-as-completed="handleMarkAsCompleted"
      @mark-as-read="handleMarkAsRead"
      @select="handleSelect"
    />
  </div>
</template>

<style lang="scss" scoped></style>
