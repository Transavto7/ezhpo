<script>
export default {
    name: "logs-filter",
    props: {
        filter: {
            type: Object,
            required: true,
        },
        terminalsOptions: {
            type: Array,
            required: true,
        },
        typesOptions: {
            type: Array,
            required: true,
        },
        pointsOptions: {
            type: Array,
            required: true,
        },
        versionsOptions: {
            type: Array,
            required: true,
        }
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
                <label class="mb-1">Терминалы</label>
                <multiselect
                    :value="filter.terminals"
                    :options="terminalsOptions"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="false"
                    placeholder="Выберите несколько значений"
                    track-by="id"
                    label="text"
                    multiple
                    @input="(value) => handleFilterUpdate('terminals', value)"
                >
                    <span slot="noResult">Результатов не найдено</span>
                    <span slot="noOptions">Список пуст</span>
                </multiselect>
            </div>

            <div class="col-12 col-lg-6 col-xl-3">
                <label class="mb-1">ПВ</label>
                <multiselect
                    :value="filter.points"
                    :options="pointsOptions"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="false"
                    placeholder="Выберите несколько значений"
                    track-by="id"
                    label="text"
                    multiple
                    @input="(value) => handleFilterUpdate('points', value)"
                >
                    <span slot="noResult">Результатов не найдено</span>
                    <span slot="noOptions">Список пуст</span>
                </multiselect>
            </div>

            <div class="col-12 col-lg-6 col-xl-3">
                <label class="mb-1">Тип ошибки</label>
                <multiselect
                    :value="filter.types"
                    :options="typesOptions"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="false"
                    placeholder="Выберите несколько значений"
                    track-by="id"
                    label="text"
                    multiple
                    @input="(value) => handleFilterUpdate('types', value)"
                >
                    <span slot="noResult">Результатов не найдено</span>
                    <span slot="noOptions">Список пуст</span>
                </multiselect>
            </div>

            <div class="col-12 col-lg-6 col-xl-3">
                <label class="mb-1">Версия</label>
                <multiselect
                    :value="filter.versions"
                    :options="versionsOptions"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="false"
                    placeholder="Выберите несколько значений"
                    track-by="id"
                    label="text"
                    multiple
                    @input="(value) => handleFilterUpdate('versions', value)"
                >
                    <span slot="noResult">Результатов не найдено</span>
                    <span slot="noOptions">Список пуст</span>
                </multiselect>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12 col-lg-6 col-xl-3">
                <label class="mb-1">Дата получения с</label>
                <b-form-datepicker
                    :value="filter.created_at_start"
                    class="mb-2"
                    @input="(value) => handleFilterUpdate('created_at_start', value)"
                />
            </div>

            <div class="col-12 col-lg-6 col-xl-3">
                <label class="mb-1">Дата получения по</label>
                <b-form-datepicker
                    :value="filter.created_at_end"
                    class="mb-2"
                    @input="(value) => handleFilterUpdate('created_at_end', value)"
                />
            </div>

            <div class="col-12 col-lg-6 col-xl-3">
                <label class="mb-1">Дата возникновения с</label>
                <b-form-datepicker
                    :value="filter.happened_at_start"
                    class="mb-2"
                    @input="(value) => handleFilterUpdate('happened_at_start', value)"
                />
            </div>

            <div class="col-12 col-lg-6 col-xl-3">
                <label class="mb-1">Дата возникновения по</label>
                <b-form-datepicker
                    :value="filter.happened_at_end"
                    class="mb-2"
                    @input="(value) => handleFilterUpdate('happened_at_end', value)"
                />
            </div>
        </div>

        <div class="row mt-2">
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
