<template>
  <div>
    <div class="card mb-4" style="overflow-x: inherit">
      <h5 class="card-header">Выбор информации</h5>
      <div class="card-body">
        <div class="row">
          <div class="form-group col-lg-3">
            <label class="mb-1" for="users">Сотрудник:</label>
            <multiselect
                v-model="user"
                @search-change="searchUsers"
                @select="(user) => user_id = user.id"
                :options="users"
                :searchable="true"
                :close-on-select="true"
                :show-labels="false"
                placeholder="Выберите сотрудника"
                label="name"
                class="is-invalid"
            >
              <span slot="noResult">Результатов не найдено</span>
              <span slot="noOptions">Список пуст</span>
            </multiselect>
          </div>
          <div class="form-group col-lg-3">
            <label class="mb-1" for="users">Пункт выпуска:</label>
            <multiselect
                v-model="point"
                @search-change="searchPoints"
                @select="(point) => point_id = point.id"
                :options="points"
                :searchable="true"
                :close-on-select="true"
                :show-labels="false"
                placeholder="Выберите пункт выпуска"
                label="name"
                class="is-invalid"
            >
              <span slot="noResult">Результатов не найдено</span>
              <span slot="noOptions">Список пуст</span>
            </multiselect>
          </div>
          <div class="form-group col-lg-2">
            <label class="mb-1" for="dateFrom">Период c:</label>
            <input type="date" required ref="dateFrom" v-model="dateFrom"
                   id="dateFrom" class="form-control form-date" name="dateFrom">
          </div>
          <div class="form-group col-lg-2">
            <label class="mb-1" for="dateTo">Период по:</label>
            <input type="date" required ref="dateTo" v-model="dateTo"
                   id="dateTo" class="form-control form-date" name="dateTo">
          </div>
        </div>
        <div class="row">
          <div class="form-group col-lg-12">
            <button type="submit" @click="submitFilters" class="btn btn-success" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              Сформировать отчёт
            </button>
            <button type="submit" @click="exportData" class="btn btn-info" :disabled="loadingExport">
              <span v-if="loadingExport" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              Экспортировать
            </button>
            <a href="?" class="btn btn-danger">Сбросить</a>
          </div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-body">
        <table class="table-bordered" v-for="item in work_reports">
          <thead>
            <th v-for="pointCell in item.pointRow">
              {{pointCell}}
            </th>
          </thead>
          <tbody>
            <tr v-for="reportData in item.reports">
              <td v-for="reportCell in reportData">
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      loading: false,
      loadingExport: false,
      user: null,
      users: [],
      dateFrom: null,
      dateTo: null,
      user_id: null,
      points: [],
      point: null,
      point_id: null,
      work_reports: []
    }
  },
  methods: {
    searchUsers(query = '') {
      axios.get('/api/users/find', {
        params: {
          search: query
        }
      }).then(({ data }) => {
        data.forEach(user => {
          user.name = `[${user.hash_id}] ${user.name}`;
        });
        this.users = data;
      });
    },
    searchPoints(query = '') {
      axios.get('/api/points/find', {
        params: {
          search: query
        }
      }).then(({ data }) => {
        data.forEach(point => {
          point.name = `[${point.hash_id}] ${point.name}`;
        });
        this.points = data;
      });
    },
    exportData() {
      alert('export')
    },
    submitFilters() {
      this.loading = true;
      axios.get('/api/reports/work/get', {
        params: {
          userId: this.user_id,
          pvId: this.point_id,
          dateFrom: this.dateFrom,
          dateTo: this.dateTo
        }
      }).then(({ data }) => {
        this.work_reports = data;
      }).finally(() => {
        this.loading = false;
      });
    },
  }
}
</script>

<style scoped>

</style>