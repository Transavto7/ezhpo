<script setup lang="ts">
const props = defineProps<{
  errors: string[]
}>()
</script>

<template>
  <div
    v-show="props.errors.length"
    class="alert alert-danger"
  >
    <div
      v-for="(error, index) in errors"
      :key="index"
      style="font-size: 14px"
    >
      {{ error }}
    </div>
  </div>
</template>
