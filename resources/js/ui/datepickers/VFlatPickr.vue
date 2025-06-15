<script setup lang="ts">
import 'flatpickr/dist/flatpickr.css'
import 'flatpickr/dist/themes/light.css'
// @ts-ignore
import flatPickr from 'vue-flatpickr-component'
import { ref } from 'vue'
import { ru } from './ru'

const props = defineProps<{
  value?: string
  label?: string
  placeholder?: string
  name?: string
  altFormat?: string
  dateFormat?: string
  enableTime?: boolean
  disabled?: boolean
}>()

const emit = defineEmits<{
  (e: 'input', value: string | null): void
}>()

const config = ref({
  wrap: true,
  altFormat: props.altFormat ?? 'd.m.Y',
  altInput: true,
  dateFormat: props.dateFormat ?? 'Y-m-d',
  enableTime: props.enableTime,
  time_24hr: true,
  locale: ru,
})

const handleInput = (value: string | null) => {
  emit('input', value)
}
</script>

<template>
  <div class="form-group mb-0">
    <label
      v-if="props.label"
      :for="props.name"
    >
      {{ label }}
    </label>
    <div class="input-group">
      <flat-pickr
        class="form-control"
        :config="config"
        :disabled="props.disabled"
        :name="props.name ?? undefined"
        :placeholder="props.placeholder ?? 'Выберите дату'"
        :value="props.value"
        @input="handleInput"
      />
      <div class="input-group-append">
        <button
          class="btn btn-sm btn-default"
          data-toggle
          :disabled="props.disabled"
          title="Toggle"
          type="button"
        >
          <i class="fa fa-calendar" />
          <span
            aria-hidden="true"
            class="sr-only"
            >Toggle</span
          >
        </button>
        <button
          class="btn btn-sm btn-default"
          data-clear
          :disabled="props.disabled"
          title="Clear"
          type="button"
        >
          <i class="fa fa-times" />
          <span
            aria-hidden="true"
            class="sr-only"
            >Clear</span
          >
        </button>
      </div>
    </div>
  </div>
</template>
