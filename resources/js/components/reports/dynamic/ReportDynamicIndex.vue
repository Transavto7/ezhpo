<template>
    <div>
        <div class="card mb-4" style="overflow-x: inherit">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                Выбор информации

                <div class="d-flex align-items-center">
                    <span class="pr-2 font-weight-normal">{{ journal === 'tech' ? 'Техосмотры' : 'Медосмотры' }}</span>
                    <label class="switch d-flex align-items-center">
                        <input v-model="journal" true-value="tech" false-value="medic" type="checkbox">
                        <span class="slider round"></span>
                    </label>
                </div>
            </h5>
            <div class="card-body">
                <form :action="'/report/dynamic/' + journal" method="GET"
                      onsubmit="document.querySelector('#page-preloader').classList.remove('hide')">
                    <div class="row">
                        <div class="form-group col-lg-3">
                            <label class="mb-1" for="company">Город</label>
                            <select
                                model="Town"
                                ref="towns"
                                field-key="id"
                                field="name"
                                name="town_id"
                                data-field="Town_town_id"
                                class="filled-select2 filled-select"
                            >
                                <option value="">Не установлено</option>
                                <option v-for="item in towns"
                                        :key="item.id"
                                        :value="item.id"
                                        :selected="item.id === Number(town)"
                                >
                                    {{ item.name }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3">
                            <label class="mb-1" for="company">Пункт выпуска</label>
                            <select
                                model="Point"
                                ref="points"
                                field-key="id"
                                field="name"
                                name="pv_id"
                                data-field="Point_pv_id"
                                class="filled-select2 filled-select"
                            >
                                <option value="">Не установлено</option>
                                <option v-for="item in points"
                                        :key="item.id"
                                        :value="item.id"
                                        :selected="item.id === Number(point)"
                                        v-if="!(selectedTown && selectedTown !== item.pv_id)"
                                >
                                    {{ item.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <button class="btn btn-info">
                                Сформировать отчет
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <slot></slot>
    </div>
</template>

<script>
export default {
    name: "ReportDynamicIndex",
    props: ['towns', 'points', 'town', 'point', 'type'],
    data() {
        return {
            selectedTown: null,
            journal: 'medic'
        }
    },
    mounted() {
        this.selectedTown = this.town;
        this.journal = this.type;
        console.log(this.type);
        $(this.$refs.towns).on("change", this.selectTown);
    },
    methods: {
        selectTown(event) {
            const selected = $(event.currentTarget).find("option:selected");
            this.selectedTown = selected.val();
        },
    }
}
</script>

<style scoped>

</style>
