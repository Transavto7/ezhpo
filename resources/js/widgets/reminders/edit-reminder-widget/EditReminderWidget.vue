<script setup>

import Preloader from "../../../common/Preloader.vue";
import {ref} from "vue";
import Notify from "../../../components/notify";
import ReminderForm from "../shared/ReminderForm.vue";
import {updateReminder} from "./api";
import {useEditPageSetup} from "./useEditPageSetup";

const {reminder} = useEditPageSetup()
const loading = ref(false)

const handleSubmit = (form) => {
    loading.value = true;
    updateReminder(form)
        .then(() => {
            Notify.success('Тариф успешно создан!')
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
        <reminder-form @submit="handleSubmit" @back="handleBackClick" :form="reminder" />
    </div>
</template>

<style scoped>

</style>
