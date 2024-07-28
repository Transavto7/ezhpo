<script>
import UuidCell from "../common/uuid-cell";

export default {
    name: "logs-table",
    components: {
        UuidCell
    },
    props: {
        items: {
            type: Array,
            required: true,
        },
        total: {
            type: Number,
            required: true,
        },
        page: {
            type: Number,
            required: true,
        },
        limit: {
            type: Number,
            required: true,
        },
    },
    emits: ['update:page', 'update:limit', 'showCrashData'],
    data() {
        return {
            limits: [
                25,
                50,
                100
            ],
            fields: [
                {key: 'uuid', label: 'UUID', class: 'text-left'},
                {key: 'terminal', label: 'Терминал', class: 'text-left'},
                {key: 'point', label: 'ПВ', class: 'text-left'},
                {key: 'created_at', label: 'Дата, время получения', class: 'text-left'},
                {key: 'happened_at', label: 'Дата, время возникновения', class: 'text-left'},
                {key: 'type', label: 'Тип отказа', class: 'text-left'},
                {key: 'version', label: 'Версия', class: 'text-left'},
                {key: 'data', label: 'Детали', class: 'text-left'},
            ],
        }
    },
    methods: {
        handleChangePage(value) {
            this.$emit('update:page', value)
        },

        handleChangeLimit(e) {
            this.$emit('update:limit', +e.target.value)
        },

        handleShowDetailsClick(details) {
            this.$emit('showCrashData', details)
        }
    }
}
</script>

<template>
    <div>
        <div class="d-flex align-items-center mb-4">
            <span>Показывать</span>
            <select :value="limit" class="ml-2 mr-2" @input="handleChangeLimit">
                <option v-for="(limit, index) in limits" :value="limit">{{limit}}</option>
            </select>
            <span>записей</span>
        </div>

        <b-table
            :fields="fields"
            :items="items"
            head-variant="light"
            striped
            responsive
            bordered
        >
            <template #cell(uuid)="{ item }">
                <uuid-cell :uuid="item.uuid"></uuid-cell>
            </template>

            <template #cell(data)="{ item }">
                <b-button v-if="item.data" variant="success" size="sm" @click="handleShowDetailsClick(item.data)">
                    <i class="fa fa-info"></i>
                </b-button>
            </template>
        </b-table>

        <b-pagination
            :value="page"
            :total-rows="total"
            :per-page="limit"
            @input="handleChangePage"
        ></b-pagination>
    </div>
</template>

<style scoped>
.table-responsive {
    max-height: 80vh !important;
    overscroll-behavior: none !important;
}
</style>
