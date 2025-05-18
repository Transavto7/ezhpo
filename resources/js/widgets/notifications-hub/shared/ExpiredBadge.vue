<script setup></script>

<template>
  <div class="notification-expired-badge">
    <span class="fa fa-exclamation-triangle"></span>
    Просрочено
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
  color: #401111;
  background-color: #ffeeee;

  span {
    margin-top: 1px;
    border-radius: 100%;
    color: #ff5050;
  }
}
</style>
