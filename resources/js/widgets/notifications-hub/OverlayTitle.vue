<script setup>
import CloseBtn from "@/widgets/notifications-hub/shared/CloseBtn.vue";

const props = defineProps({
  count: {
    type: Number,
    required: true,
  },
})

const emit = defineEmits(['close'])

const handleClose = () => {
  emit('close')
}
</script>

<template>
  <div class="hab-overlay-title">
    <close-btn @click="handleClose"></close-btn>
    <span>Уведомления</span>
    <div
      v-if="props.count > 0"
      class="badge-count"
    >
      {{ props.count }}
    </div>
  </div>
</template>

<style scoped lang="scss">
.hab-overlay-title {
  display: flex;
  align-content: center;
  gap: 15px;

  span {
    font-family: sans-serif;
    font-weight: 300;
    font-size: 17px;
    line-height: 1;
  }
}

.badge-count {
  padding: 1px 5px;
  background-color: #eeeeee;
  border-radius: 11px;
  font-size: 10px;
  color: #6f6f6f;
  margin-top: 1px;
}
</style>
