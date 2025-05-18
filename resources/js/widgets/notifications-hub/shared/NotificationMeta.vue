<script setup lang="ts">
const props = defineProps({
  actionTitle: {
    type: String,
    required: true,
  },
  createdAt: {
    type: String,
    required: true,
  }
})
</script>

<template>
  <div class="notification-meta">
    <div>
      {{ props.actionTitle }}
    </div>
    <span>•</span>
    <div>
      {{ props.createdAt }}
    </div>
  </div>
</template>

<style scoped lang="scss">
.notification-meta {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 11px;
  color: #999;
  font-weight: 300;
  font-family: sans-serif;
}
</style>
