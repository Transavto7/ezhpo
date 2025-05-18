<script setup>
import TariffForm from "../shared/TariffForm.vue";
import {ref} from "vue";
import {createTariff} from "./api";
import Notify from "../../../components/notify";
import Preloader from "@/ui/Preloader.vue";
import moment from "moment/moment";

const tariff = ref({
    town: null,
    point: null,
    role: null,
    name: null,
    price_cfg: 1.5,
    date_from: moment().startOf('year').format('YYYY-MM-DD'),
    date_to: moment().format('YYYY-MM-DD'),
    default_price: 1000,
    hours: [],
})
const loading = ref(false)

const handleSubmit = (form) => {
    loading.value = true;
    createTariff(form)
        .then(() => {
            Notify.success('Тариф успешно создан!')
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
