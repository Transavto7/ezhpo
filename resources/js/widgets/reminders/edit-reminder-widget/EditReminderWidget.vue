<script setup lang="ts">
import { ref } from 'vue'
import Notify from '@/components/notify'
import Preloader from '@/ui/Preloader.vue'
import ReminderForm from '@/widgets/reminders/shared/form/ReminderForm.vue'
import { Reminder } from '@/widgets/reminders/types'
import { updateReminder } from './api'
import { useEditPageSetup } from './useEditPageSetup'

const { reminder } = useEditPageSetup()
const loading = ref(false)

const handleSubmit = async (form: Reminder) => {
  loading.value = true

  try {
    await updateReminder(form)

    Notify.success('Напоминание успешно обновлено!')
    reminder.value = form
  } catch (e: any) {
    if (e?.response?.status === 422) {
      Notify.error(e?.response.data.errors.interval[0])
      return
    }

    Notify.error('Ошибка обновления напоминания!')
  } finally {
    loading.value = false
  }
}

const handleBackClick = () => {
  window.location.href = '/reminders'
}
</script>

<template>
  <div
    class="card"
    style="overflow: visible"
  >
    <preloader :loading="loading" />
    <reminder-form
      :form="reminder"
      @back="handleBackClick"
      @submit="handleSubmit"
    />
  </div>
</template>

<style scoped></style>
