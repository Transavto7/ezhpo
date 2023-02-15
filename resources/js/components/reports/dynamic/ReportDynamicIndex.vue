<template>
  <div>
    <div class="card mb-4" style="overflow-x: inherit">
      <h5 class="card-header d-flex justify-content-between align-items-center">
        Выбор информации
        <div class="d-flex align-items-center">
          <select v-model="journal" @change="changeFormParams">
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
              <multiselect
                  v-model="company"
                  @search-change="searchCompany"
                  :options="companies"
                  :searchable="true"
                  :close-on-select="false"
                  :show-labels="false"
                  label="name"
                  track-by="id"
                  :multiple="true"
                  :taggable="true"
                  placeholder="Выберите компании"
              >
                  <span slot="noResult">Результатов не найдено</span>
                  <span slot="noOptions">Список пуст</span>
              </multiselect>
          </div>

            <div class="form-group col-lg-3">
              <label class="mb-1" for="town_id">Город</label>
              <select
                  ref="towns"
                  name="town_id"
                  id="town_selector"
                  class="filled-select2 filled-select"
                  data-allow-clear="true"
                  multiple
              >
                <option value="">Не установлено</option>
                <option v-for="item in towns"
                        :key="item.id"
                        :value="item.id"
                        :selected="item.id === Number(town)"
                >
                  [{{ item.hash_id }}] {{ item.name }}
                </option>
              </select>
            </div>

            <div class="form-group col-lg-3">
              <label class="mb-1" for="pv_id">Пункт выпуска</label>
              <select
                  ref="points"
                  name="pv_id"
                  id="point_selector"
                  class="filled-select2 filled-select"
                  data-allow-clear="true"
                  multiple
              >
                <option value="">Не установлено</option>
                <option v-for="item in pointList"
                        :key="item.id"
                        :value="item.id"
                        :selected="item.id === Number(point) || selectedPoints.includes(item.id)"
                        v-if="!item.hide"
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
              <button class="btn btn-info" type="button" v-on:click="submitForm">
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
  props: ['towns', 'points', 'town', 'point', 'type', 'order', 'infos', 'monthnames', 'monthtotal'],
  data() {
    return {
      selectedTown: null,
      journal: 'medic',
      pointList: [],
      //Дата осмотра - execute, дата создания - created.
      companies: [],
      company: [],
      orderBy: 'execute',
      selectedTowns: [],
      selectedPoints: [],
      selectedTownsAsString: '',
      selectedPvAsString: '',
      selectedPoint: 0
    }
  },
  mounted() {
    this.selectedTown = this.town;
    this.journal = this.type;
    this.orderBy = this.order;
    $(this.$refs.towns).on("change", this.selectTown);
    $(this.$refs.points).on("change", this.selectPoint);
    this.searchCompany();

    if (this.infos !== null) {
      var ctx = document.getElementById('chart').getContext('2d');
      var chart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: Object.values(this.monthnames).map((month) => month.charAt(0).toUpperCase() + month.slice(1)),
          datasets: [{
            label: 'Количество проведённых осмотров',
            backgroundColor: 'rgb(196, 219, 231)',
            borderColor: 'rgb(23,66,231)',
            minBarLength: 1,
            borderWidth: 1,
            data: Object.values(this.monthtotal)
          }]
        },
        options: {
          scales: {
            y: {
              beginAtZero: true
            }
          }
        },
      })
    }
  },
  methods: {
    selectTown(event) {
      const selected = $(event.currentTarget).val();
      this.selectedTowns = selected;
      this.pointList = this.points.filter(function (point) {
        return selected.includes(point.pv_id);
      });
    },
    selectPoint(event) {
      const selected = $(event.currentTarget).val();
      let converterArray = selected.map(point => Number(point));
      this.selectedPoints = converterArray;
    },
    getAvailablePoints() {
      var selectedTown = this.selectedTown,
          pointSelects = document.querySelectorAll('select.select-points'),
          pointsList = this.points,
          points = [],
          result = [];

      pointSelects.forEach(function (e, i) { points.push(e.value) })
      result = pointsList.filter((p) => !points.includes(p.id) && p.pv_id == selectedTown);

      return result;
    },
    changeFormParams() {
      this.selectedTownsAsString = this.selectedTowns;
      this.selectedPvAsString = this.selectedPoints;
      this.orderBy = $("select#order_by").val();
    },
    submitForm() {
      this.changeFormParams();
      window.location = `/report/dynamic/${this.journal}?
      town_id=${this.selectedTownsAsString}
      &pv_id=${this.selectedPvAsString}
      &order_by=${this.orderBy}`;
    },
      searchCompany(query = '') {
          axios.get('/api/companies/find', {
              params: {
                  search: query
              }
          }).then(({ data }) => {
              data.forEach(company => {
                  company.name = `[${company.hash_id}] ${company.name}`;
              });

              this.companies = data;
          });
      },
  }
}

</script>

<style scoped>

</style>
