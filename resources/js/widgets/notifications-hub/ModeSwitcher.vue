<script setup>
import VSwitcher from "@/ui/VSwitcher.vue";
import {computed, onMounted} from "vue";

const props = defineProps({
  value: {
    type: Boolean,
    required: true,
  }
})

const emit = defineEmits(['input'])

const switcherIconClass = computed(() => {
  return props.value ? 'fa fa-bell switcher-icon' : 'fa fa-bell-slash switcher-icon switcher-icon-slash'
})

const handleChangeSoftMode = (value) => {
  localStorage.setItem('isNotificationMode', value)
  emit('input', value)
}

onMounted(() => {
  const val = localStorage.getItem('isNotificationMode')

  if (val === null) {
    emit('input', true)
    localStorage.setItem('isNotificationMode', 'true')
    return
  }

  emit('input', val === 'true')
})
</script>

<template>
  <div class="switcher-wrapper d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-center" style="width: 25px; height: 25px;">
      <span :class="switcherIconClass"></span>
    </div>
    <v-switcher :value="props.value" @input="handleChangeSoftMode"/>
  </div>
</template>

<style lang="scss" scoped>
.switcher-wrapper {
  padding: 5px;
  border-radius: 5px;
  display: flex;
  align-items: center;

  .switcher-icon {
    padding: 0!important;
    transform: translateY(0px)!important;
  }

  .switcher-icon-slash {
    color: #c6c6c6;
    transform: translateY(0px)!important;
  }
}
</style>
