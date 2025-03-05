<script setup>
import tableFields from "./tableFields";

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    pending: {
        type: Boolean,
        default: false,
    },
    currentPage: {
        type: Number,
        required: true,
    },
    sortBy: {
        type: String,
        required: false,
        default: null,
    },
    sortDesc: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits([
    'update:sort-by',
    'update:sort-desc',
    'delete-tariff',
])

const handleSortUpdate = (e) => {
    emit('update:sort-by', e.sortBy)
    emit('update:sort-desc', e.sortDesc)
}

const handleDeleteButtonClick = (id) => {
    emit('delete-tariff', id)
}
</script>

<template>
    <div class="card table-card">
        <div class="card-body pt-0">
            <b-table
                :fields="tableFields"
                :items="props.items"
                :busy="props.busy"
                :sort-by="props.sortBy"
                :sort-desc="props.sortDesc"
                :current-page="props.currentPage"
                striped hover
                no-local-sorting
                @sort-changed="handleSortUpdate"
            >
                <template #cell(name)="{ item }">
                    {{ item.name  }}
                </template>

                <template #cell(town)="{ item }">
                    {{ item.town.name }}
                </template>

                <template #cell(point)="{ item }">
                    {{ item.point?.name }}
                </template>

                <template #cell(role)="{ item }">
                    {{ item.role.guard_name }}
                </template>

                <template #cell(date_from)="{ item }">
                    {{ item.date_from }}
                </template>

                <template #cell(date_to)="{ item }">
                    {{ item.date_to }}
                </template>

                <template #cell(updated_at)="{ item }">
                    {{ item.updated_at }}
                </template>

                <template #cell(actions)="{ item }">
                    <div class="d-flex justify-content-center align-items-start">
                        <a class="btn btn-sm btn-success action-btn mr-2" :href="`/employees/tariffs/${item.id}`"><span class="fa fa-edit"></span></a>
                        <button class="btn btn-sm btn-danger action-btn" @click.prevent="handleDeleteButtonClick(item.id)"><span class="fa fa-trash"></span></button>
                    </div>
                </template>
            </b-table>
        </div>
    </div>
</template>

<style scoped>
.table-card {
    max-height: 55vh;
    overflow: hidden;
}

.table-card > .card-body {
    overflow: scroll;
    padding: 0 !important;
    margin: 15px !important;
    overscroll-behavior: contain;
}

.action-btn {
    width: 32px;
    height: 32px;
}
</style>
