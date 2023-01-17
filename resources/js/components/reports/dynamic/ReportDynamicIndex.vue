<template>
    <div>
        <div class="card mb-4" style="overflow-x: inherit">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                Выбор информации

                <div class="d-flex align-items-center">
                  <select v-model="journal" @change="submitForm">
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
                            <label class="mb-1" for="company">Город</label>
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
                                    {{ item.name }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3">
                            <label class="mb-1" for="company">Пункт выпуска</label>
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
    props: ['towns', 'points', 'town', 'point', 'type', 'order', 'infos'],
    data() {
        return {
            selectedTown: null,
            journal: 'medic',
            pointList: [],
            //Дата осмотра - execute, дата создания - created.
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

        var _sTown = this.town;
        if (this.selectedTown != null) {
          $("#town_selector").children('option').each(function (i, e) {
            if (e.value == '' || e.value == 'Не установлено') {
              return;
            }
            if (_sTown.split(',').includes(e.value)) {
              e.selected = true;
            }
          });
          $(this.$refs.towns).trigger("change");

          //_sPoints - выделенные пункты.
          var _sPoints = this.point.split(',').map(e => Number(e)),
              points = this.points.filter(function (p) {
                //Если пункт был выбран - выделить.
                //Если пункт принадлежит городу, то включаем в список.
                if (_sTown.includes(p.pv_id)) {
                  return true;
                }
              });
        }
        this.pointList = points;
        this.selectedPoints = _sPoints;

        if (this.infos !== null) {
          var ctx = document.getElementById('chart').getContext('2d');
          var chart = new Chart(ctx, {
            type: 'bar',
            data: {
              labels: ['Январь', 'Декабрь', "Ноябрь", "Октябрь", "Сентябрь", "Август", "Июль", "Июнь", "Май", "Апрель", "Март", "Февраль"],
              datasets: [{
                label: 'Количество проведённых осмотров',
                backgroundColor: 'rgb(196, 219, 231)',
                borderColor: 'rgb(23,66,231)',
                minBarLength: 1,
                borderWidth: 1,
                data: Object.values(this.infos)
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
            let converterArray = selected.map(point => Number(point));

            this.selectedTowns = selected;
            this.pointList = this.points.filter(function (point) {
                  return converterArray.includes(point.pv_id);
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
        submitForm() {
          this.selectedTownsAsString = this.selectedTowns;
          this.selectedPvAsString = this.selectedPoints;
          window.location = `/report/dynamic/${this.journal}?town_id=${this.selectedTownsAsString}&pv_id=${this.selectedPvAsString}&order_by=${this.orderBy}`;
        }
    }
}

</script>

<style scoped>

</style>
