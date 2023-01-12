<template>
    <div>
        <div class="card mb-4" style="overflow-x: inherit">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                Выбор информации

                <div class="d-flex align-items-center">
                  <select v-model="journal" @change="changeJournalType">
                    <option value="tech">Техосмотры</option>
                    <option value="medic">Медосмотры</option>
                    <option value="all">Оба</option>
                  </select>
                </div>
            </h5>
            <div class="card-body">
                <form :action="'/report/dynamic/' + journal" method="GET"
                      onsubmit="document.querySelector('#page-preloader').classList.remove('hide')">
                    <div class="row">
                        <div class="form-group col-lg-3">
                            <label class="mb-1" for="company">Город</label>
                            <select
                                ref="towns"
                                name="town_id"
                                class="filled-select2 filled-select"
                                data-allow-clear="true"
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
                                ref="points"
                                name="pv_id"
                                class="filled-select2 filled-select"
                                data-allow-clear="true"
                            >
                                <option value="">Не установлено</option>
                                <option v-for="item in pointList"
                                        :key="item.id"
                                        :value="item.id"
                                        :selected="item.id === Number(point)"
                                        v-if="!item.hide"
                                >
                                    {{ item.name }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3">
                          <label class="mb-1" for="company">Построение</label>
                          <select
                              ref="order"
                              name="order_by"
                              class="filled-select2 filled-select"
                              data-allow-clear="false"
                              v-model="orderBy"
                              @change="changeJournalType"
                          >
                            <option value="execute">По дате осмотра</option>
                            <option value="created">По дате создания</option>
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
    props: ['towns', 'points', 'town', 'point', 'type', 'order'],
    data() {
        return {
            selectedTown: null,
            journal: 'medic',
            pointList: [],
            //Дата осмотра - execute, дата создания - created.
            orderBy: 'execute'
        }
    },
    mounted() {
        this.pointList = this.points;
        this.selectedTown = this.town;
        this.journal = this.type;
        this.orderBy = this.order;
        console.log(this.type);
        $(this.$refs.towns).on("change", this.selectTown);
    },
    methods: {
        changeJournalType() {
          window.location = `/report/dynamic/${this.journal}?town_id=${this.selectedTown}&pv_id=${this.point}&order_by=${this.orderBy}`
        },
        selectTown(event) {
            const selected = $(event.currentTarget).find("option:selected");
            this.selectedTown = Number(selected.val());
            this.pointList = this.points.filter(point => {
                return  Number(point.pv_id) === this.selectedTown || !this.selectedTown;
            });
        },
    }
}

</script>

<style scoped>

</style>
