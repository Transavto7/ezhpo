<script setup>
const props = defineProps({
  expiresAt: {
    type: String,
    required: true,
  },
})
</script>

<template>
  <div class="notification-expired-badge">
    <span class="fa fa-exclamation-circle"></span>
    Истекает: {{ props.expiresAt }}
  </div>
</template>

<style scoped lang="scss">
.notification-expired-badge {
  padding: 5px 12px;
  border-radius: 18px;
  font-size: 11px;
  display: flex;
  align-items: center;
  gap: 5px;
  color: #402511;
  background-color: #fff6ee;

  span {
    margin-top: 1px;
    border-radius: 100%;
    color: #ff9950;
  }
}
</style>
