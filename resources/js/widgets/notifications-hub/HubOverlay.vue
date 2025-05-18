<script setup>
import {watch} from "vue";
import OverlayTitle from "@/widgets/notifications-hub/OverlayTitle.vue";
import NotificationItem from "@/widgets/notifications-hub/item/NotificationItem.vue";
import NotificationViewer from "@/widgets/notifications-hub/viewer/NotificationViewer.vue";

const props = defineProps({
  activeNotification: {
    type: Object,
    required: false,
    default: null,
  },
  isShow: {
    type: Boolean,
    required: true,
  },
  notifications: {
    type: Array,
    required: true,
  },
  pending: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits(['select', 'close', 'mark-as-read', 'mark-as-completed'])

const handleNotificationSelect = (item) => {
  emit('select', item)
}

const handleClose = () => {
  emit('close')
}

const handleMarkAsRead = (id) => {
  emit('mark-as-read', id)
}

const handleMarkAsCompleted = (id) => {
  emit('mark-as-completed', id)
}

watch(() => props.show, (newValue) => {
  if (newValue) {
    document.body.classList.add('overflow-hidden');
  } else {
    document.body.classList.remove('overflow-hidden');
  }
});
</script>

<template>
  <div v-if="isShow" class="overlay-wrapper">
    <div class="overlay-bg" @click="handleClose"></div>

    <div class="overlay-list-inner">
      <div class="overlay-title">
        <overlay-title :count="notifications.length" @close="handleClose"/>
      </div>

      <div class="overlay-content">
        <transition-group name="fade-slide" tag="div">
          <notification-item
            v-for="item of props.notifications"
            :key="item.id"
            :item="item"
            :active="props.activeNotification?.id === item.id"
            @click="handleNotificationSelect(item)"
          />
        </transition-group>
      </div>
    </div>

    <notification-viewer
      v-if="props.activeNotification"
      class="overlay-viewer-element"
      :item="props.activeNotification"
      :pending="props.pending"
      @close="handleClose"
      @mark-as-read="handleMarkAsRead"
      @mark-as-completed="handleMarkAsCompleted"
    />
  </div>
</template>


<style scoped lang="scss">
$list-width: 400px;
$list-margin: 20px;
$title-height: 60px;

.overlay-wrapper {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  overflow: hidden;
  z-index: 1000;
  color: black;
}

.overlay-bg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1001;
  background-color: rgba(0, 0, 0, 0.5);
}

.overlay-list-inner {
  position: fixed;
  top: $list-margin;
  right: 20px;
  width: $list-width;
  height: calc(100vh - $list-margin*2);
  max-height: calc(100vh - $list-margin*2);
  min-height: calc(100vh - $list-margin*2);
  background-color: #fff;
  border-radius: 8px;
  z-index: 1002;
  overflow: hidden;
}

.overlay-title {
  display: flex;
  align-items: center;
  padding-inline: 20px;
  height: $title-height;
}

.overlay-content {
  overflow-y: auto;
  height: calc(100% - $title-height);
  max-height: calc(100% - $title-height);
}

.overlay-viewer-element {
  position: fixed;
  z-index: 1002;
  top: 50%;
  right: calc(50% + ($list-width + $list-margin) / 2);
  transform: translate(50%, -50%);
  width: 800px;
  max-width: calc(100vw - $list-width - $list-margin*2);
  max-height: calc(100% - $list-margin*2);
  overflow-y: auto;
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.4s ease;
}

.fade-slide-enter,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateX(10px);
}
</style>
