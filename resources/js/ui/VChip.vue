<script setup>
import {computed} from "vue";

const props = defineProps({
  type: {
    type: String,
    default: 'primary'
  }
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
    style="font-size: 11px"
    :class="classObject"
  >
    <slot></slot>
  </div>
</template>

<style scoped>
</style>