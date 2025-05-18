<script setup>
import {computed} from "vue";

const props = defineProps({
  type: {
    type: String,
    required: true,
  },
})

const classObject = computed(() => ({
  info: props.type === 'info',
  warning: props.type === 'warning',
  danger: props.type === 'danger',
}))

const classIcon = computed(() => ({
  'fa-info-circle': props.type === 'info',
  'fa-question-circle': props.type === 'warning',
  'fa-exclamation-circle': props.type === 'danger',
}))
</script>

<template>
  <div class="notification-badge" :class="classObject">
    <span class="fa" :class="classIcon"></span>
  </div>
</template>

<style scoped lang="scss">
.notification-badge {
  font-size: 13px;

  &.info {
    color: #5096ff;
  }

  &.warning {
    color: #ffb350;
  }

  &.danger {
    color: #ff5050;
  }
}
</style>
