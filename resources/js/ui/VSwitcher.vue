<script setup lang="ts">

const props = defineProps<{
  value: boolean,
  label?: string,
}>()

const emit = defineEmits(['input'])

const handleInput = (e: Event) => {
  if (e.target instanceof HTMLInputElement) {
    emit('input', e.target.checked)
  }
}
</script>

<template>
  <div class="switcher-wrapper">
    <span
      v-if="props.label"
      class="switcher-label"
    >
      {{ props.label }}:
    </span>
    <label class="switch">
      <input
        :checked="props.value"
        type="checkbox"
        @change="handleInput"
      />
      <span class="slider round"/>
    </label>
  </div>
</template>

<style scoped lang="scss">
.switcher-wrapper {
  display: flex;
  justify-content: flex-start;
  align-items: center;
}

.switcher-label {
  margin-right: 10px;
  font-size: 13px;
  color: #212529;
}

.switch {
  position: relative;
  display: inline-block;
  width: 30px;
  height: 17px;
  margin-bottom: 0;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
  user-select: none;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: 0.4s;
  transition: 0.4s;
  border-radius: 50%;
}

.slider:before {
  position: absolute;
  content: '';
  height: 13px;
  width: 13px;
  left: 2px;
  bottom: 2px;
  background-color: white;
  -webkit-transition: 0.4s;
  transition: 0.4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #5671f0;
}

input:checked + .slider:before {
  transform: translateX(13px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
