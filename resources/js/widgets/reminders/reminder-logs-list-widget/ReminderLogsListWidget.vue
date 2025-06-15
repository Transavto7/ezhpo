<script setup>
import { onMounted, watch } from 'vue'
import Notify from '../../../components/notify'
import debounce from '../../../helpers/debounce'
import ReminderLogsFilter from './ReminderLogsFilter.vue'
import ReminderLogsTable from './ReminderLogsTable.vue'
import ReminderLogsTablePagination from './ReminderLogsTablePagination.vue'
import { useReminderLogsTable } from './useReminderLogsTable'

const { table, params, fetchTablePending, fetchRemindersTable, resetFilter, filter } =
  useReminderLogsTable()

const handleFilterApply = async () => {
  params.page = 1
  await fetchRemindersTable()
}

const handleFilterReset = async () => {
  resetFilter()
  await fetchRemindersTable()
}

onMounted(async () => {
  await fetchRemindersTable()
})

watch(
  [() => params.page, () => params.perPage, () => params.sortBy, () => params.sortDesc],
  debounce(async () => {
    await fetchRemindersTable()
  }, 200),
)
</script>

<template>
  <div>
    <div class="card mb-3">
      <div class="card-body">
        <reminder-logs-filter
          :actions.sync="filter.actions"
          :cities.sync="filter.cities"
          :companies.sync="filter.companies"
          :points.sync="filter.points"
          :roles.sync="filter.roles"
          :search.sync="filter.search"
          :subject_type.sync="filter.subject_type"
          :users.sync="filter.users"
        />

        <div class="mt-2 d-flex">
          <b-btn
            class="btn btn-sm btn-success"
            :disabled="fetchTablePending"
            @click="handleFilterApply"
            >Поиск</b-btn
          >
          <b-btn
            class="btn btn-sm btn-danger ml-2"
            :disabled="fetchTablePending"
            @click="handleFilterReset"
            >Сбросить</b-btn
          >
        </div>
      </div>
    </div>

    <reminder-logs-table
      :current-page="params.page"
      :items="table.items"
      :map-list="table.mapList"
      :pending="fetchTablePending"
      :sort-by.sync="params.sortBy"
      :sort-desc.sync="params.sortDesc"
    />

    <reminder-logs-table-pagination
      :page.sync="params.page"
      :pending="fetchTablePending"
      :per-page.sync="params.perPage"
      :total="table.total"
    />
  </div>
</template>

<style scoped>
.card {
  overflow: visible;
}
</style>
