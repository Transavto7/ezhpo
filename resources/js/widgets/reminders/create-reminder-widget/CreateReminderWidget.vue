<script setup>
import Preloader from "@/ui/Preloader.vue";
import {ref} from "vue";
import Notify from "../../../components/notify";
import ReminderForm from "../shared/ReminderForm.vue";
import {createReminder} from "./api";

const reminder = ref({
  title: null,
  content: null,
  status: null,
  action: null,
  type: null,
  expiresAt: null,
  expiresInMinutes: null,
  hiddenFromInitiator: false,
  usersToNotify: [],
  conditions: {
    subject: null,
    subject_type: null,
    company: null,
    role: null,
    user: null,
    point: null,
    city: null,
  }
})
const loading = ref(false)

const handleSubmit = (form) => {
  loading.value = true;
  createReminder(form)
    .then(() => {
      Notify.success('Напоминание успешно создано!')
      reminder.value = form
    }).catch(({response}) => {
    if (response.status === 422) {
      Notify.error(response.data.errors.interval[0])
      return;
    }
    Notify.error('Ошибка запроса!')
    console.log(response)
  }).finally(() => {
    loading.value = false;
  })
}

const handleBackClick = () => {
  window.location = '/reminders'
}
</script>

<template>
  <div class="card" style="overflow: visible">
    <preloader :loading="loading"/>
    <reminder-form
      :form="reminder"
      @submit="handleSubmit"
      @back="handleBackClick"
    />
  </div>
</template>

<style scoped>

</style>
