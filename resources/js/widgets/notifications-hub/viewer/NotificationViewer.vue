<script setup>
import CloseBtn from "@/widgets/notifications-hub/shared/CloseBtn.vue";
import ExpiredBadge from "@/widgets/notifications-hub/shared/ExpiredBadge.vue";
import TypeBadge from "@/widgets/notifications-hub/shared/TypeBadge.vue";
import ExpiresAtBadge from "@/widgets/notifications-hub/shared/ExpiresAtBadge.vue";
import NotificationMeta from "@/widgets/notifications-hub/shared/NotificationMeta.vue";

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
  pending: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits(['close', 'mark-as-read', 'mark-as-completed'])

const handleClose = () => {
  emit('close')
}

const handleMarkAsRead = () => {
  emit('mark-as-read', props.item.id)
}

const handleMarkAsCompleted = () => {
  emit('mark-as-completed', props.item.id)
}
</script>

<template>
  <div class="notification-viewer">
    <div class="notification-viewer-head">
      <div class="notification-viewer-head-left">
        <type-badge
          :title="props.item.reminderType.title"
          :type="props.item.reminderType.value"
        />
        <div class="notification-viewer-title">{{ item.title }}</div>
      </div>
      <close-btn @click="handleClose"></close-btn>
    </div>

    <div class="notification-viewer-meta">
      <notification-meta
        :action-title="props.item.reminderAction.title"
        :created-at="props.item.createdAtFormatted"
      />
    </div>

    <div class="notification-viewer-content">
      <div v-html="props.item.content"></div>
    </div>

    <div class="notification-item-info">
      <expires-at-badge
        v-if="!props.item.isExpired && props.item.expiresAt"
        :expires-at="props.item.expiresAt"
      />
      <expired-badge v-if="props.item.isExpired" />
    </div>

    <div class="notification-item-actions">
      <button
        class="btn btn-sm btn-info"
        :disabled="props.pending"
        @click="handleMarkAsRead"
      >
        Прочитать
      </button>
      <button
        class="btn btn-sm btn-success"
        :disabled="props.pending"
        @click="handleMarkAsCompleted"
      >
        Выполнить
      </button>
    </div>
  </div>
</template>

<style scoped lang="scss">
.notification-viewer {
  padding: 20px;
  background-color: #fff;
  border-radius: 8px;
}

.notification-viewer-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.notification-viewer-meta {
  margin-top: 0;
}

.notification-viewer-head-left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.notification-viewer-title {
  font-size: 18px;
  font-weight: 600;
}

.notification-item-info {
  margin-top: 15px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.notification-viewer-content {
  padding: 10px;
  background-color: #f6f6f6;
  border-radius: 5px;
  font-size: 14px;
  margin-top: 15px;
}

.notification-item-actions {
  display: flex;
  gap: 10px;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #e3e3e3;
}
</style>

<style>
p:last-child {
  margin-bottom: 0!important;
}
</style>
