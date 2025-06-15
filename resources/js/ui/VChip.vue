<script setup lang="ts">
import { PropType, computed } from 'vue'

const props = defineProps({
  type: {
    type: String as PropType<'primary' | 'warning' | 'info' | 'success' | 'danger' | 'secondary'>,
    default: 'primary',
  },
})

const classObject = computed(() => ({
  'bg-primary': props.type === 'primary',
  'bg-warning': props.type === 'warning',
  'bg-info': props.type === 'info',
  'bg-success': props.type === 'success',
  'bg-danger': props.type === 'danger',
  'bg-secondary': props.type === 'secondary',
}))
</script>

<template>
  <div
    class="badge text-white"
    :class="classObject"
    style="font-size: 11px"
  >
    <slot></slot>
  </div>
</template>

<style scoped></style>
