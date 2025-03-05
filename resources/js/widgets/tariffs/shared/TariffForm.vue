<script setup>
import {computed, ref, watch} from "vue";
import HoursBlock from "./components/HoursBlock.vue";
import VSelectRemote from "../../../v-components/VSelectRemote.vue";
import {fetchPointsForSelectWithTown, fetchRolesForSelect, fetchTownsForSelect} from "./api";
import moment from "moment";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    }
})

const emit = defineEmits(["submit", 'back']);

const tariff = ref(props.form)

const hours = ref(props.form?.hours ?? [])

const handleChangeHour = (index, hour) => {
    hours.value[index] = hour
}

const handleChangeTown = (town) => {
    tariff.value.town = town;
}

const handleChangePoint = (point) => {
    tariff.value.point = point;
}

const handleChangeRole = (role) => {
    tariff.value.role = role;
}

const handleAddHours = () => {
    hours.value.push({start: 8, end: 17, price: 1000})
}

const handleRemoveHour = (index) => {
    hours.value.splice(index, 1);
}

const fetchPoint = ({search}) => {
    return fetchPointsForSelectWithTown(search, tariff.value.town.id);
}

const pointSelectDisabled = computed(() => {
    return !tariff.value.town
})

const intervalsHasOverlap = computed(() => {
    const intervals = JSON.parse(JSON.stringify(hours.value));
    intervals.sort((a, b) => a.start - b.start);
    for (let i = 1; i < intervals.length; i++) {
        const previous = intervals[i - 1];
        const current = intervals[i];

        if (previous.end >= current.start) {
            return true;
        }
    }

    return false;
})

const validateErrors = computed(() => {
    let errors = [];

    if (intervalsHasOverlap.value) {
        errors.push('Интервалы имеют пересечения!')
    }

    if (!tariff.value.town) {
        errors.push('Поле Город обязательно для заполнения!')
    }

    if (!tariff.value.role) {
        errors.push('Поле Роль обязательно для заполнения!')
    }

    if (!tariff.value.name) {
        errors.push('Поле Наименование обязательно для заполнения!')
    }

    if (!moment(tariff.value.date_to).isAfter(moment(tariff.value.date_from))) {
        errors.push('Дата в поле Тариф начинается должно быть раньше чем дата в поле Тариф заканчивается!')
    }

    return errors;
})

const hasErrors = computed(() => {
    return validateErrors.value.length > 0
})

const handleSaveClick = () => {
    emit("submit", {
        ...tariff.value,
        hours: hours.value,
    })
}

const handleBackClick = () => {
    emit("back")
}


watch(props.form, () => {
    tariff.value = props.form
})
</script>

<template>
    <b-container fluid class="py-3">
        <b-alert variant="danger" :show="hasErrors">
            Ошибка валидации!
            <ul>
                <li v-for="error in validateErrors">{{ error }}</li>
            </ul>
        </b-alert>
        <b-row>
            <b-col>
                <b-form-group
                    id="name"
                    label="Наименование:"
                    label-for="name"
                >
                    <b-form-input
                        id="name"
                        v-model="tariff.name"
                        type="text"
                        placeholder="Введите имя тарифа"
                        required
                    ></b-form-input>
                </b-form-group>
            </b-col>
            <b-col>
                <b-form-group
                    id="price_cfg"
                    label="Коэффициент выходного дня:"
                    label-for="price_cfg"
                >
                    <b-form-input
                        id="price_cfg"
                        v-model="tariff.price_cfg"
                        type="number"
                        placeholder="Укажите коэффициент выходного дня"
                        step="0.1"
                        min="0"
                        required
                    ></b-form-input>
                </b-form-group>
            </b-col>
            <b-col>
                <b-form-group
                    id="default_price"
                    label="Базовая стоимость часа:"
                    label-for="default_price"
                >
                    <b-form-input
                        id="default_price"
                        v-model="tariff.default_price"
                        placeholder="Укажите базовую стоимость часа"
                        type="number"
                        step="100"
                        required
                    ></b-form-input>
                </b-form-group>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-form-group
                    id="date_from"
                    label="Тариф начинается:"
                    label-for="date_from"
                >
                    <b-form-input
                        id="date_from"
                        v-model="tariff.date_from"
                        type="date"
                        required
                    ></b-form-input>
                </b-form-group>
            </b-col>
            <b-col>
                <b-form-group
                    id="date_to"
                    label="Тариф заканчивается:"
                    label-for="date_to"
                >
                    <b-form-input
                        id="price_cfg"
                        v-model="tariff.date_to"
                        type="date"
                        required
                    ></b-form-input>
                </b-form-group>
            </b-col>
        </b-row>

        <b-row>
            <b-col class="mb-4">
                <v-select-remote
                    id="role"
                    :value="tariff.role"
                    label="Роль"
                    @input="handleChangeRole"
                    :fetch-options-action="fetchRolesForSelect"
                />
            </b-col>

            <b-col class="mb-4">
                <v-select-remote
                    id="town"
                    :value="tariff.town"
                    label="Город"
                    @input="handleChangeTown"
                    :fetch-options-action="fetchTownsForSelect"
                />
            </b-col>

            <b-col class="mb-4">
                <v-select-remote
                    id="point"
                    :value="tariff.point"
                    label="ПВ"
                    :disabled="pointSelectDisabled"
                    @input="handleChangePoint"
                    :fetch-options-action="fetchPoint"
                    :clearable="true"
                />
            </b-col>

        </b-row>

        <hours-block :hours="hours" @update:hour="handleChangeHour" @add:hours="handleAddHours"
                     @remove:hour="handleRemoveHour"/>

        <div class="mt-2 d-flex justify-content-center align-items-center">
            <button class="btn btn-secondary mr-2" @click.prevent="handleBackClick">Назад</button>
            <button class="btn btn-primary" @click.prevent="handleSaveClick" :disabled="hasErrors">Сохранить</button>
        </div>
    </b-container>
</template>

<style scoped>

</style>
