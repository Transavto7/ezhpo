<script setup lang="ts">
const emit = defineEmits(['click'])

const handleClose = () => {
  emit('click')
}
</script>

<template>
  <div class="hab-overlay-close-button" @click="handleClose"></div>
</template>

<style scoped lang="scss">
.hab-overlay-close-button {
  width: 15px;
  height: 15px;
  position: relative;
  cursor: pointer;
}

.hab-overlay-close-button::before,
.hab-overlay-close-button::after {
  content: '';
  position: absolute;
  left: 0;
  top: 7px;
  width: 15px;
  height: 1px;
  background-color: #333;
  transform-origin: center;
}

.hab-overlay-close-button::before {
  transform: rotate(45deg);
}

.hab-overlay-close-button::after {
  transform: rotate(-45deg);
}
</style>
