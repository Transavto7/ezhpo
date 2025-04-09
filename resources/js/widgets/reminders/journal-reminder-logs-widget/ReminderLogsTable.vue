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
])

const handleSortUpdate = (e) => {
    emit('update:sort-by', e.sortBy)
    emit('update:sort-desc', e.sortDesc)
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
                <template #cell(title)="{ item }">
                    {{ item.title  }}
                </template>

                <template #cell(action)="{ item }">
                    {{ item.action.name }}
                </template>

                <template #cell(user)="{ item }">
                    {{ item.user.name }}
                </template>

                <template #cell(payload)="{ item }">
                    <pre>{{item.payload}}</pre>
                </template>

                <template #cell(created_at)="{ item }">
                    {{ item.created_at }}
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
