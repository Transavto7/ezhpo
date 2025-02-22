<script setup>
import vSelect from "vue-select";

const props = defineProps({
  value: {
    type: Array,
    required: true,
  },
  options: {
    type: Array,
    required: true,
  }
})

const emit = defineEmits(['input'])

const handleInput = (value) => {
  emit('input', value)
}
</script>

<template>
  <div>
    <label for="">Роль</label>
    <v-select
      :value="props.value"
      :multiple="true"
      :options="props.options"
      label="name"
      @input="handleInput"
    />
  </div>
</template>

<style scoped>

</style>