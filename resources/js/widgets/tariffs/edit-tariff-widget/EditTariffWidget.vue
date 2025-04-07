<script setup>
import TariffForm from "../shared/TariffForm.vue";
import {ref} from "vue";
import {updateTariff} from "./api";
import Notify from "../../../components/notify";
import Preloader from "../../../common/Preloader.vue";
import {useEditPageSetup} from "./useEditPageSetup";

const {tariff} = useEditPageSetup();
const loading = ref(false)

const handleSubmit = (form) => {
    loading.value = true;
    updateTariff(form)
        .then((res) => {
            Notify.success('Тариф успешно обновлен!')
            tariff.value = form
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
    window.location = '/employees/tariffs'
}
</script>

<template>
    <div class="card" style="overflow: visible">
        <preloader :loading="loading"/>
        <tariff-form @submit="handleSubmit" @back="handleBackClick" :form="tariff"/>
    </div>
</template>

<style scoped>

</style>
