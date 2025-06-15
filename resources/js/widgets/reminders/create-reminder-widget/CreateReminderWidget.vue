<script setup lang="ts">
import { ref } from 'vue'
import Notify from '@/components/notify'
import Preloader from '@/ui/Preloader.vue'
import ReminderForm from '@/widgets/reminders/shared/form/ReminderForm.vue'
import { Reminder } from '@/widgets/reminders/types'
import { createReminder } from './api'

const loading = ref(false)
const reminder = ref<Reminder>({
  id: '',
  title: '',
  content: '',
  status: null,
  action: null,
  type: null,
  expiresAt: null,
  expiresInMinutes: null,
  hiddenFromInitiator: false,
  usersToNotify: [],
  oneTimePerUser: false,
  untilAnyUserCompletes: false,
  conditions: {
    subject: null,
    subject_type: null,
    company: null,
    role: null,
    user: null,
    point: null,
    city: null,
  },
})

const handleSubmit = async (form: Reminder) => {
  loading.value = true

  try {
    await createReminder(form)

    Notify.success('Напоминание успешно создано!')
    reminder.value = form
  } catch (e: any) {
    if (e?.response?.status === 422) {
      Notify.error(e?.response.data.errors.interval[0])
      return
    }

    Notify.error('Ошибка при создании напоминания!')
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
