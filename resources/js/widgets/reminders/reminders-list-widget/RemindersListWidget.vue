<script setup>
import {onMounted, watch} from "vue";
import debounce from "../../../helpers/debounce";
import Notify from "../../../components/notify";
import {useRemindersTable} from "./useRemindersTable";
import RemindersFilter from "./RemindersFilter.vue";
import RemindersTable from "./RemindersTable.vue";
import RemindersTablePagination from "./RemindersTablePagination.vue";

const { table, params, fetchTablePending, fetchRemindersTable, resetFilter, filter, performDeleteReminder } = useRemindersTable()

const handleFilterApply = async () => {
    params.page = 1
    await fetchRemindersTable()
}

const handleFilterReset = async () => {
    resetFilter()
    await fetchRemindersTable()
}

const handleDelete = async (id) => {
    performDeleteReminder(id).then(async () => {
        await fetchRemindersTable()
    }).catch(error => {
        Notify.error('Ошибка сервера')
    })
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
                <reminders-filter
                    :search.sync="filter.search"
                    :actions.sync="filter.actions"
                    :cities.sync="filter.cities"
                    :points.sync="filter.points"
                    :users.sync="filter.users"
                    :roles.sync="filter.roles"
                    :companies.sync="filter.companies"
                    :subject_type.sync="filter.subject_type"
                    :subjects.sync="filter.subjects"
                />

                <div class="mt-2 d-flex">
                    <b-btn class="btn btn-sm btn-success" :disabled="fetchTablePending" @click="handleFilterApply">Поиск</b-btn>
                    <b-btn class="btn btn-sm btn-danger ml-2" :disabled="fetchTablePending" @click="handleFilterReset">Сбросить</b-btn>
                </div>
            </div>
        </div>

        <reminders-table
            :current-page="params.page"
            :pending="fetchTablePending"
            :items="table.items"
            :sort-by.sync="params.sortBy"
            :sort-desc.sync="params.sortDesc"
            @delete="handleDelete"
        />

        <reminders-table-pagination
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
