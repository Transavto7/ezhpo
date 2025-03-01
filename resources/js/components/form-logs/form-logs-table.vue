<script>
import UuidCell from "./../common/uuid-cell";

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
    emits: ['update:page', 'update:limit'],
    data() {
        return {
            fields: [
                {key: 'uuid', label: 'UUID', class: 'text-left'},
                {key: 'user', label: 'Пользователь', class: 'text-left'},
                {key: 'created_at', label: 'Дата, время', class: 'text-left'},
                {key: 'type', label: 'Действие', class: 'text-left'},
                {key: 'model_type', label: 'Модель', class: 'text-left'},
                {key: 'form_id', label: 'ID Модели', class: 'text-center'},
                {key: 'payload', label: 'Изменения', class: 'text-left'},
            ],
        }
    },
    methods: {
        handleChangePage(value) {
            this.$emit('update:page', value)
        },

        handleChangeLimit(e) {
            this.$emit('update:limit', +e.target.value)
        }
    }
}
</script>

<template>
    <div>
        <div class="d-flex align-items-center mb-4">
            <span>Показывать</span>
            <select :value="limit" class="ml-2 mr-2" @input="handleChangeLimit">
                <option value="100">100</option>
                <option value="500">500</option>
                <option value="1000">1000</option>
                <option value="1500">1500</option>
                <option value="2000">2000</option>
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

            <template #cell(payload)="{ item }">
                <div v-for="(changeItem, index) of item.payload" :key="index" class="p-1 mb-1" style="line-height: 19px">
                    <div>
                        <b>{{ changeItem.name }}</b>
                    </div>
                    <span style="height: 18px">{{ changeItem.oldValue }}</span>
                    <span class="text-primary">&xrarr;</span>
                    <span> {{ changeItem.newValue }}</span>
                </div>
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
