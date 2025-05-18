<script setup>
import {
  useNotificationLogsTable
} from "@/widgets/notifications/notification-logs-list-widget/useNotificationLogsTable";
import {onMounted, watch} from "vue";
import debounce from "@/helpers/debounce";
import NotificationLogsTablePagination
  from "@/widgets/notifications/notification-logs-list-widget/NotificationLogsTablePagination.vue";
import NotificationLogsTable from "@/widgets/notifications/notification-logs-list-widget/NotificationLogsTable.vue";
import NotificationLogsFilter from "@/widgets/notifications/notification-logs-list-widget/NotificationLogsFilter.vue";

const {table, params, fetchTablePending, fetchNotificationsLogsTable, resetFilter, filter} = useNotificationLogsTable()

const handleFilterApply = async () => {
  params.page = 1
  await fetchNotificationsLogsTable()
}

const handleFilterReset = async () => {
  resetFilter()
  await fetchNotificationsLogsTable()
}

onMounted(async () => {
  await fetchNotificationsLogsTable()
})

watch([
    () => params.page,
    () => params.perPage,
    () => params.sortBy,
    () => params.sortDesc,
  ],
  debounce(async () => {
    await fetchNotificationsLogsTable()
  }, 200)
)
</script>

<template>
  <div>
    <div class="card mb-3">
      <div class="card-body">
        <notification-logs-filter
          :search.sync="filter.search"
          :actions.sync="filter.actions"
          :notifications.sync="filter.notifications"
          :users.sync="filter.users"
        />

        <div class="mt-2 d-flex">
          <b-btn class="btn btn-sm btn-success" :disabled="fetchTablePending" @click="handleFilterApply">Поиск</b-btn>
          <b-btn class="btn btn-sm btn-danger ml-2" :disabled="fetchTablePending" @click="handleFilterReset">Сбросить
          </b-btn>
        </div>
      </div>
    </div>

    <notification-logs-table
      :current-page="params.page"
      :pending="fetchTablePending"
      :items="table.items"
      :mapList="table.mapList"
      :sort-by.sync="params.sortBy"
      :sort-desc.sync="params.sortDesc"
    />

    <notification-logs-table-pagination
      :page.sync="params.page"
      :per-page.sync="params.perPage"
      :total="table.total"
      :pending="fetchTablePending"
    />
  </div>
</template>

<style scoped lang="scss">
.card {
  overflow: visible;
}
</style>
