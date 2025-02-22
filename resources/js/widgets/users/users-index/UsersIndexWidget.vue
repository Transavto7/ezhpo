<script setup>
import {useIndexPageSetup} from "@/widgets/users/users-index/useIndexPageSetup";
import UsersFilter from "@/widgets/users/users-index/UsersFilter.vue";
import {useUsersTable} from "@/widgets/users/users-index/useUsersTable";
import {onMounted, ref} from "vue";
import UsersTablePagination from "@/widgets/users/users-index/UsersTablePagination.vue";
import UsersTable from "@/widgets/users/users-index/UsersTable.vue";
import LogsModal from "@/components/logs/logs-modal.vue";

const {permissions} = useIndexPageSetup()
const {table, params, filter, fetchTablePending, resetFilter, fetchUsersTable} = useUsersTable()
const logsModalElement = ref(null)
const logsModalShow = ref(false)

const handleFilterReset = async () => {
  resetFilter()
  await fetchUsersTable()
}

const handleUpdateUser = async (id) => {
  await fetchUsersTable()
}

const handleLogsRead = (id) => {
  logsModalShow.value = true
  logsModalElement.value.loadData(id)
}

onMounted(async () => {
  await fetchUsersTable()
})
</script>

<template>
  <div v-if="permissions.canRead">
    <div class="card mb-3" style="overflow: visible">
      <div class="card-body">
        <users-filter
          :search.sync="filter.search"
          :status.sync="filter.status"
          :entity-type.sync="filter.entityType"
          :users.sync="filter.users"
        />

        <div class="mt-1 d-flex">
          <b-btn class="btn btn-sm btn-danger" @click="handleFilterReset">Сбросить</b-btn>
        </div>
      </div>
    </div>

    <users-table
      :current-page="params.page"
      :pending="fetchTablePending"
      :items="table.items"
      :sort-by.sync="params.sortBy"
      :sort-desc.sync="params.sortDesc"
      @update-user="handleUpdateUser"
      @logs-read="handleLogsRead"
    />

    <users-table-pagination
      :page.sync="params.page"
      :per-page.sync="params.perPage"
      :total="table.total"
      :pending="fetchTablePending"
    />

    <b-modal
      v-model="logsModalShow"
      :title="'Журнал действий'"
      :static="true"
      size="lg"
      hide-footer>
      <logs-modal ref="logsModalElement"/>
    </b-modal>
  </div>
  <div v-else>
    <div class="alert alert-danger plug-403">У Вас нет прав на доступ к этому разделу</div>
  </div>
</template>

<style scoped>
.plug-403 {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 120px;
}
</style>