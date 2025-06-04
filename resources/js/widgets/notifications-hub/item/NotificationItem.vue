<script setup>
import {computed} from "vue";
import TypeBadge from "@/widgets/notifications-hub/shared/TypeBadge.vue";
import ExpiredBadge from "@/widgets/notifications-hub/shared/ExpiredBadge.vue";
import ExpiresAtBadge from "@/widgets/notifications-hub/shared/ExpiresAtBadge.vue";
import NotificationMeta from "@/widgets/notifications-hub/shared/NotificationMeta.vue";

const props = defineProps({
  active: {
    type: Boolean,
    required: true,
  },
  disabled: {
    type: Boolean,
    required: false,
    default: false,
  },
  item: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['click'])

const classObject = computed(() => {
  return {
    'active': props.active,
  }
})

const handleClick = () => {
  emit('click')
}
</script>

<template>
  <div class="notification-item" :class="classObject" @click="handleClick">
    <div class="notification-item-head">
      <type-badge :type="props.item.reminderType.value"/>
      <div class="notification-item-title">{{ item.title }}</div>
    </div>

    <notification-meta
      :action-title="props.item.reminderAction.title"
      :created-at="props.item.createdAtFormatted"
      class="notification-item-meta"
    />

    <div class="notification-item-content">
      <expires-at-badge
        v-if="!props.item.isExpired && props.item.expiresAt"
        :expires-at="props.item.expiresAt"
      />
      <expired-badge v-if="props.item.isExpired"/>
    </div>
  </div>
</template>

<style scoped lang="scss">
.notification-item {
  padding: 20px;
  cursor: pointer;
  transition: all 200ms ease-in-out;
  border-left: 3px solid transparent;

  &:hover {
    background-color: #f5f5f5;
    border-left: 3px solid #bdbdbd;
  }

  &.active {
    background-color: #f5f5f5;
    border-left: 3px solid #cccccc;
  }
}

.notification-item-head {
  display: flex;
  align-items: center;
  gap: 8px;
}

.notification-item-title {
  font-family: sans-serif;
  font-size: 12px;
  color: #404040;
  font-weight: 600;
  margin-top: 1px;
}

.notification-item-content {
  margin-top: 6px;
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.notification-item-meta {
  margin-top: 8px;
}
</style>
