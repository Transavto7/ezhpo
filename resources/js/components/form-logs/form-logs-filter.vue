<script>
export default {
    name: "logs-filter",
    props: {
        filter: {
            type: Object,
            required: true,
        },
        usersOptions: {
            type: Array,
            required: true,
        },
        modelsOptions: {
            type: Array,
            required: true,
        },
        actionsOptions: {
            type: Array,
            required: true,
        },
    },
    emits: ['update:filter', 'apply', 'reset'],
    methods: {
        handleFilterUpdate(property, value) {
            const filter = { ...this.filter, [property]: value }

            this.$emit('update:filter', filter)
        },

        handleApply() {
            this.$emit('apply')
        },

        handleReset() {
            this.$emit('reset')
        }
    }
}
</script>

<template>
    <div>
        <div class="row">
            <div class="col-12 col-lg-6 col-xl-3">
                <label class="mb-1">Пользователи</label>
                <multiselect
                    :value="filter.users"
                    :options="usersOptions"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="false"
                    placeholder="Выберите несколько значений"
                    track-by="id"
                    label="text"
                    multiple
                    @input="(value) => handleFilterUpdate('users', value)"
                >
                    <span slot="noResult">Результатов не найдено</span>
                    <span slot="noOptions">Список пуст</span>
                </multiselect>
            </div>

            <div class="col-12 col-lg-6 col-xl-3">
                <label class="mb-1">Модели</label>
                <multiselect
                    :value="filter.models"
                    :options="modelsOptions"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="false"
                    placeholder="Выберите несколько значений"
                    track-by="id"
                    label="text"
                    multiple
                    @input="(value) => handleFilterUpdate('models', value)"
                >
                    <span slot="noResult">Результатов не найдено</span>
                    <span slot="noOptions">Список пуст</span>
                </multiselect>
            </div>

            <div class="col-12 col-lg-6 col-xl-3">
                <label class="mb-1">ID модели</label>
                <b-form-input
                    :value="filter.id"
                    size="sm"
                    placeholder="Введите значение"
                    @input="(value) => handleFilterUpdate('id', value)"
                />
            </div>

            <div class="col-12 col-lg-6 col-xl-3">
                <label class="mb-1">UUID</label>
                <b-form-input
                    :value="filter.uuid"
                    size="sm"
                    placeholder="Введите значение"
                    @input="(value) => handleFilterUpdate('uuid', value)"
                />
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12 col-lg-6 col-xl-3">
                <label class="mb-1">Действия</label>
                <multiselect
                    :value="filter.actions"
                    :options="actionsOptions"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="false"
                    placeholder="Выберите несколько значений"
                    track-by="id"
                    label="text"
                    multiple
                    @input="(value) => handleFilterUpdate('actions', value)"
                >
                    <span slot="noResult">Результатов не найдено</span>
                    <span slot="noOptions">Список пуст</span>
                </multiselect>
            </div>

            <div class="col-12 col-lg-6 col-xl-3">
                <label class="mb-1">Дата с</label>
                <b-form-datepicker
                    :value="filter.date_start"
                    class="mb-2"
                    @input="(value) => handleFilterUpdate('date_start', value)"
                />
            </div>

            <div class="col-12 col-lg-6 col-xl-3">
                <label class="mb-1">Дата по</label>
                <b-form-datepicker
                    :value="filter.date_end"
                    class="mb-2"
                    @input="(value) => handleFilterUpdate('date_end', value)"
                />
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                <div class="d-flex">
                    <b-button
                        class="mr-2"
                        variant="success"
                        size="sm"
                        @click="handleApply"
                    >
                        Поиск
                    </b-button>
                    <b-button
                        variant="danger"
                        size="sm"
                        @click="handleReset"
                    >
                        Сбросить
                    </b-button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
