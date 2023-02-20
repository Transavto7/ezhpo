<template>
  <div>
    <div class="card mb-4" style="overflow-x: inherit">
      <h5 class="card-header d-flex justify-content-between align-items-center">
        Выбор информации
        <div class="d-flex align-items-center">
          <select v-model="journal">
            <option value="tech">Техосмотры</option>
            <option value="medic">Медосмотры</option>
            <option value="all">Все</option>
          </select>
        </div>
      </h5>
      <div class="card-body">
        <form :action="'/report/dynamic/' + journal" method="GET"
              onsubmit="document.querySelector('#page-preloader').classList.remove('hide')">
          <div class="row">
          <div class="form-group col-lg-3">
              <label class="mb-1" for="company_id">Компания</label>
              <select model="Company"
                      field-key="hash_id"
                      field="name"
                      field-concat="hash_id"
                      field-trashed="true"
                      multiple="true"
                      name="company_id[]"
                      data-label="company_id"
                      data-field="medic_company_id"
                      class="filled-select2 filled-select select2-hidden-accessible"
                      data-allow-clear="true" data-select2-id="1"
                      tabindex="-1" aria-hidden="true">
                  <option value="">Не установлено</option>
                  <option v-for="item in companies"
                          :key="item.hash_id"
                          :value="item.hash_id"
                          :selected="selectedCompanies.includes(item.hash_id)"
                  >
                      [{{ item.hash_id }}] {{ item.name }}
                  </option>
              </select>
          </div>

            <div class="form-group col-lg-3">
              <label class="mb-1" for="town_id">Город</label>
              <select
                  ref="towns"
                  name="town_id[]"
                  id="town_selector"
                  class="filled-select2 filled-select"
                  data-allow-clear="true"
                  multiple
              >
                <option value="">Не установлено</option>
                <option v-for="item in towns"
                        :key="item.id"
                        :value="item.id"
                        :selected="selectedTowns.includes(item.id)"
                >
                  [{{ item.hash_id }}] {{ item.name }}
                </option>
              </select>
            </div>

            <div class="form-group col-lg-3">
              <label class="mb-1" for="pv_id">Пункт выпуска</label>
              <select
                  ref="points"
                  name="pv_id[]"
                  id="point_selector"
                  class="filled-select2 filled-select"
                  data-allow-clear="true"
                  multiple
              >
                <option value="">Не установлено</option>
                <option v-for="item in pointList"
                        :key="item.id"
                        :value="item.id"
                        :selected="selectedPoints.includes(item.id)"
                >
                    [{{ item.hash_id }}] {{ item.name }}
                </option>
              </select>
            </div>

            <div class="form-group col-lg-3">
              <label class="mb-1" for="company">Построение</label>
              <select
                  ref="order"
                  id="order_by"
                  name="order_by"
                  class="filled-select2 filled-select"
                  data-allow-clear="false"
                  v-model="orderBy"
              >
                <option value="execute">По дате осмотра</option>
                <option value="created">По дате создания</option>
              </select>
            </div>
          </div>

          <div class="row" id="make-report-block">
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
  props: ['towns', 'points', 'selTowns', 'selPoints', 'type', 'order', 'companies', 'selCompanies'],
  data() {
    return {
      journal: 'medic',
      pointList: [],
      orderBy: 'execute',
      selectedTowns: [],
      selectedPoints: [],
      selectedCompanies: [],
      selectedPoint: 0
    }
  },
  mounted() {
    this.selectedTowns = this.selTowns || [];
    this.selectedPoints = this.selPoints || [];
    this.selectedCompanies = this.selCompanies || [];
    this.journal = this.type;
    this.orderBy = this.order;
    $(this.$refs.towns).on("change", this.selectTown);
  },
  methods: {
    selectTown(event) {
      const selected = $(event.currentTarget).val();
      console.log(selected);
      this.pointList = this.points.filter(function (point) {
          console.log(point.pv_id, selected.includes(point.pv_id));
        return selected.includes(point.pv_id);
      });
    },
  }
}

</script>

<style scoped>

</style>
