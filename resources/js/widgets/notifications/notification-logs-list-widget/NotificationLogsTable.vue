<script setup>
import tableFields from "./tableFields";
import {usePageSetup} from "@/widgets/notifications/notification-logs-list-widget/usePageSetup";

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
    mapList: {
        type: Object,
        required: false,
    }
})

const emit = defineEmits([
    'update:sort-by',
    'update:sort-desc',
])

const { canEmployeeRead } = usePageSetup()

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
                  <a target="_blank" :href="item.notification_url">{{ item.title  }}</a>
                </template>

                <template #cell(action)="{ item }">
                  <span v-if="item.action.value === 'create'" class="badge badge-secondary">{{ item.action.name }}</span>
                  <span v-if="item.action.value === 'view'" class="badge badge-warning">{{ item.action.name }}</span>
                  <span v-if="item.action.value === 'read'" class="badge badge-info">{{ item.action.name }}</span>
                  <span v-if="item.action.value === 'complete'" class="badge badge-success">{{ item.action.name }}</span>
                </template>

                <template #cell(user)="{ item }">
                  <a v-if="canEmployeeRead" target="_blank" :href="item.user_url">{{ item.user }}</a>
                  <span v-else>{{ item.user }}</span>
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

pre.payload {
    white-space: pre-wrap;
}
</style>
