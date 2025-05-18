<script setup>
import {useNotificationTable} from "@/widgets/notifications/notifications-list-widget/useNotificationsTable";
import {onMounted, watch} from "vue";
import debounce from "@/helpers/debounce";
import NotificationsTablePagination
  from "@/widgets/notifications/notifications-list-widget/NotificationsTablePagination.vue";
import NotificationsTable from "@/widgets/notifications/notifications-list-widget/NotificationsTable.vue";
import NotificationsFilter from "@/widgets/notifications/notifications-list-widget/NotificationsFilter.vue";
import {markAsCompleted, markAsRead} from "@/widgets/notifications/notifications-list-widget/api";
import Notify from "@/components/notify";

const {table, params, fetchTablePending, fetchNotificationsTable, resetFilter, filter} = useNotificationTable()

const handleFilterApply = async () => {
  params.page = 1
  await fetchNotificationsTable()
}

const handleFilterReset = async () => {
  resetFilter()
  await fetchNotificationsTable()
}

const handleUpdateFilter = (payload) => {
  filter[payload.prop] = payload.value
}

const handleMarkAsRead = async (id) => {
  try {
    await markAsRead(id)
    Notify.success('Уведомление отмечено как просмотренное')
    await fetchNotificationsTable()
  } catch (e) {
    console.error(e)
    Notify.error('Ошибка при попытке отметить уведомление как просмотренное')
  }
}

const handleMarkAsCompleted = async (id) => {
  try {
    await markAsCompleted(id)
    Notify.success('Уведомление отмечено как выполненное')
    await fetchNotificationsTable()
  } catch (e) {
    console.error(e)
    Notify.error('Ошибка при попытке отметить уведомление как выполненное')
  }
}

onMounted(async () => {
  const urlParams = new URLSearchParams(window.location.search)
  const id = urlParams.get('id')
  const title = urlParams.get('title')

  if (id && title) {
    filter.notifications = [{id, name: title}]

    const url = new URL(window.location.href);
    url.searchParams.delete('id');
    url.searchParams.delete('title');

    history.replaceState(null, '', url.toString());
  }

  await fetchNotificationsTable()
})

watch([
    () => params.page,
    () => params.perPage,
    () => params.sortBy,
    () => params.sortDesc,
  ],
  debounce(async () => {
    await fetchNotificationsTable()
  }, 200)
)
</script>

<template>
  <div>
    <div class="card mb-3">
      <div class="card-body">
        <notifications-filter :filter="filter" @update="handleUpdateFilter" />

        <div class="mt-2 d-flex">
          <b-btn class="btn btn-sm btn-success" :disabled="fetchTablePending" @click="handleFilterApply">Поиск</b-btn>
          <b-btn class="btn btn-sm btn-danger ml-2" :disabled="fetchTablePending" @click="handleFilterReset">Сбросить
          </b-btn>
        </div>
      </div>
    </div>

    <notifications-table
      :current-page="params.page"
      :pending="fetchTablePending"
      :items="table.items"
      :mapList="table.mapList"
      :sort-by.sync="params.sortBy"
      :sort-desc.sync="params.sortDesc"
      @mark-as-read="handleMarkAsRead"
      @mark-as-completed="handleMarkAsCompleted"
    />

    <notifications-table-pagination
      :page.sync="params.page"
      :per-page.sync="params.perPage"
      :total="table.total"
      :pending="fetchTablePending"
    />
  </div>
</template>

<style scoped>
.card {
  overflow: visible;
}
</style>
