<script setup>
import {watch} from "vue";
import HubOverlay from "./HubOverlay.vue";
import {useGlobalEvent} from "@/composables/useGlobalEvent";
import {GLOBAL_EVENTS} from "@/conf";
import ModeSwitcher from "@/widgets/notifications-hub/ModeSwitcher.vue";
import {useNotifications} from "@/widgets/notifications-hub/useNotifications";
import {createNotificationsByContext} from "@/widgets/notifications-hub/api";

const {bindGlobalEventHandler} = useGlobalEvent()
const {
  isNotificationMode,
  isShowModal,
  pendingPerform,
  activeNotification,
  notifications,
  fetchNotificationItems,
  performAsViewed,
  performMarkAsCompleted,
  performMarkAsRead,
} = useNotifications()

bindGlobalEventHandler(GLOBAL_EVENTS.showModalNotificationWindow, async (event) => {
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
  () => activeNotification.value,
  async (newValue) => {
    if (newValue.isViewed || !isShowModal.value) {
      return
    }

    await performAsViewed(newValue.id)
  }
)

watch(() => isShowModal.value, (newValue) => {
  if (newValue) {
    document.body.classList.add('overflow-hidden')
  } else {
    document.body.classList.remove('overflow-hidden')

    activeNotification.value = null
    notifications.value = []
  }
})
</script>

<template>
  <div class="mr-4">
    <mode-switcher v-model="isNotificationMode"/>

    <hub-overlay
      :is-show="isShowModal"
      :active-notification="activeNotification"
      :notifications="notifications"
      :pending="pendingPerform"
      @close="handleClose"
      @select="handleSelect"
      @mark-as-read="handleMarkAsRead"
      @mark-as-completed="handleMarkAsCompleted"
    />
  </div>
</template>

<style lang="scss" scoped>
</style>
