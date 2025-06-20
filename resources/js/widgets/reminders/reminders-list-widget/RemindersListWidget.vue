<script setup>
import { GLOBAL_EVENTS } from '@/conf'
import { onMounted, watch } from 'vue'
import { useGlobalEvent } from '@/composables/useGlobalEvent'
import { ACTIONS, CONDITIONS, SUBJECT_TYPES } from '@/common/reminderContextEnums'
import { switchReminderStatus } from '@/widgets/reminders/reminders-list-widget/api'
import Notify from '../../../components/notify'
import debounce from '../../../helpers/debounce'
import RemindersFilter from './RemindersFilter.vue'
import RemindersTable from './RemindersTable.vue'
import RemindersTablePagination from './RemindersTablePagination.vue'
import { useRemindersTable } from './useRemindersTable'

const {
  table,
  params,
  fetchTablePending,
  fetchRemindersTable,
  resetFilter,
  filter,
  performDeleteReminder,
} = useRemindersTable()

const { dispatchGlobalEvent } = useGlobalEvent()

const handleFilterApply = async () => {
  params.page = 1
  await fetchRemindersTable()
}

const handleFilterReset = async () => {
  resetFilter()
  await fetchRemindersTable()
}

const handleSend = async () => {
  dispatchGlobalEvent(GLOBAL_EVENTS.showModalNotificationWindow, {
    context: {
      [CONDITIONS.SUBJECT_TYPE]: SUBJECT_TYPES.CAR,
    },
    action: ACTIONS.CREATE_INSPECTION,
  })
}

const handleDelete = async (id) => {
  performDeleteReminder(id)
    .then(async () => {
      await fetchRemindersTable()
    })
    .catch((error) => {
      Notify.error('Ошибка сервера')
    })
}

const handleSwitchStatus = async (payload) => {
  await switchReminderStatus(payload.id, payload.enable)
  await fetchRemindersTable()
}

watch(
  [() => params.page, () => params.perPage, () => params.sortBy, () => params.sortDesc],
  debounce(async () => {
    await fetchRemindersTable()
  }, 200),
)

onMounted(async () => {
  const urlParams = new URLSearchParams(window.location.search)
  const id = urlParams.get('id')
  const title = urlParams.get('title')

  if (id && title) {
    filter.reminders = [{ id, name: title }]

    const url = new URL(window.location.href)
    url.searchParams.delete('id')
    url.searchParams.delete('title')

    history.replaceState(null, '', url.toString())
  }

  await fetchRemindersTable()
})
</script>

<template>
  <div>
    <div class="card mb-4">
      <div class="card-body">
        <reminders-filter
          :actions.sync="filter.actions"
          :cities.sync="filter.cities"
          :companies.sync="filter.companies"
          :points.sync="filter.points"
          :reminders.sync="filter.reminders"
          :roles.sync="filter.roles"
          :search.sync="filter.search"
          :subject_type.sync="filter.subject_type"
          :subjects.sync="filter.subjects"
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
            >Сбросить
          </b-btn>
        </div>
      </div>
    </div>

    <reminders-table
      :current-page="params.page"
      :items="table.items"
      :pending="fetchTablePending"
      :sort-by.sync="params.sortBy"
      :sort-desc.sync="params.sortDesc"
      @delete="handleDelete"
      @switch-status="handleSwitchStatus"
    />

    <reminders-table-pagination
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
