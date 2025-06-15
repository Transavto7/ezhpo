<script setup>
import {useTariffsFilter} from "./useTariffsFilter";
import VSelectRemote from "@/ui/select/VSelectRemote.vue";

const {fetchPoints, fetchTowns, fetchRoles} = useTariffsFilter()

const props = defineProps({
    search: {
        type: String,
        required: false,
        default: null,
    },
    towns: {
        type: Array,
        required: false,
        default: null,
    },
    points: {
        type: Array,
        required: false,
        default: null,
    },
    roles: {
        type: Array,
        required: false,
        default: null,
    },
})

const emit = defineEmits(['update:search', 'update:towns', 'update:points'])


const handleInputSearch = (e) => {
    emit('update:search', e.target.value)
}

const handleChangeTown = (value) => {
    emit('update:towns', value)
}

const handleChangePoints = (value) => {
    emit('update:points', value)
}

const handleChangeRoles = (value) => {
    emit('update:roles', value)
}
</script>

<template>
    <div>
        <div class="row">
            <div class="col-12 col-md-4 col-lg-3 form-group">
                <label for="">Поиск</label>
                <input class="form-control" type="text" :value="props.search" placeholder="Введите значение для поиска"
                       @input="handleInputSearch">
            </div>
            <div class="col-12 col-md-4 col-lg-3 form-group">
                <v-select-remote
                    id="town"
                    :value="towns"
                    label="Город"
                    @input="handleChangeTown"
                    :fetch-options-action="fetchTowns"
                    multiple
                    clearable
                />
            </div>
            <div class="col-12 col-md-4 col-lg-3 form-group">
                <v-select-remote
                    id="points"
                    :value="points"
                    label="ПВ"
                    @input="handleChangePoints"
                    :fetch-options-action="fetchPoints"
                    multiple
                    clearable
                />
            </div>

            <div class="col-12 col-md-4 col-lg-3 form-group">
                <v-select-remote
                    id="points"
                    :value="roles"
                    label="Роли"
                    @input="handleChangeRoles"
                    :fetch-options-action="fetchRoles"
                    multiple
                    clearable
                />
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.form-group {
    overflow: visible;
}

.row {
    overflow: visible;
}
</style>
