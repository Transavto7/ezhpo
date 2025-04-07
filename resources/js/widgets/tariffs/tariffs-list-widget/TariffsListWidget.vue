<script setup>
import {onMounted, watch} from "vue";
import {useTariffsTable} from "./useTariffsTable";
import debounce from "../../../helpers/debounce";
import TariffsTable from "./TariffsTable.vue";
import TariffsFilter from "./TariffsFilter.vue";
import TariffsTablePagination from "./TariffsTablePagination.vue";
import Notify from "../../../components/notify";

const { table, params, fetchTablePending, fetchTariffsTable, resetFilter, filter, performDeleteTariff } = useTariffsTable()

const handleFilterApply = async () => {
    params.page = 1
    await fetchTariffsTable()
}

const handleFilterReset = async () => {
    resetFilter()
    await fetchTariffsTable()
}

const handleDeleteTariff = async (id) => {
    performDeleteTariff(id).then(async () => {
        await fetchTariffsTable()
    }).catch(error => {
        Notify.error('Ошибка сервера')
    })
}


onMounted(async () => {
    await fetchTariffsTable()
})

watch([
        () => params.page,
        () => params.perPage,
        () => params.sortBy,
        () => params.sortDesc,
    ],
    debounce(async () => {
        await fetchTariffsTable()
    }, 200)
)
</script>

<template>
    <div>
        <div class="card mb-3">
            <div class="card-body">
                <tariffs-filter
                    :search.sync="filter.search"
                    :towns.sync="filter.towns"
                    :points.sync="filter.points"
                    :roles.sync="filter.roles"
                />

                <div class="mt-2 d-flex">
                    <b-btn class="btn btn-sm btn-success" :disabled="fetchTablePending" @click="handleFilterApply">Поиск</b-btn>
                    <b-btn class="btn btn-sm btn-danger ml-2" :disabled="fetchTablePending" @click="handleFilterReset">Сбросить</b-btn>
                </div>
            </div>
        </div>

        <tariffs-table
            :current-page="params.page"
            :pending="fetchTablePending"
            :items="table.items"
            :sort-by.sync="params.sortBy"
            :sort-desc.sync="params.sortDesc"
            @delete-tariff="handleDeleteTariff"
        />

        <tariffs-table-pagination
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
