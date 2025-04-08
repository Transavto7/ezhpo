<script setup>
import {onMounted, watch} from "vue";
import debounce from "../../../helpers/debounce";
import Notify from "../../../components/notify";
import {useReminderLogsTable} from "./useReminderLogsTable";
import ReminderLogsFilter from "./ReminderLogsFilter.vue";
import ReminderLogsTable from "./ReminderLogsTable.vue";
import ReminderLogsTablePagination from "./ReminderLogsTablePagination.vue";

const { table, params, fetchTablePending, fetchRemindersTable, resetFilter, filter } = useReminderLogsTable()

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

watch([
        () => params.page,
        () => params.perPage,
        () => params.sortBy,
        () => params.sortDesc,
    ],
    debounce(async () => {
        await fetchRemindersTable()
    }, 200)
)
</script>

<template>
    <div>
        <div class="card mb-3">
            <div class="card-body">
                <reminder-logs-filter
                    :search.sync="filter.search"
                    :actions.sync="filter.actions"
                    :cities.sync="filter.cities"
                    :points.sync="filter.points"
                    :users.sync="filter.users"
                    :roles.sync="filter.roles"
                    :companies.sync="filter.companies"
                    :subject_type.sync="filter.subject_type"
                />

                <div class="mt-2 d-flex">
                    <b-btn class="btn btn-sm btn-success" :disabled="fetchTablePending" @click="handleFilterApply">Поиск</b-btn>
                    <b-btn class="btn btn-sm btn-danger ml-2" :disabled="fetchTablePending" @click="handleFilterReset">Сбросить</b-btn>
                </div>
            </div>
        </div>

        <reminder-logs-table
            :current-page="params.page"
            :pending="fetchTablePending"
            :items="table.items"
            :sort-by.sync="params.sortBy"
            :sort-desc.sync="params.sortDesc"
        />

        <reminder-logs-table-pagination
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
