<script setup>
import tableFields from "./tableFields";
import {usePageSetup} from "@/widgets/notifications/notifications-list-widget/usePageSetup";
import {computed} from "vue";

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

const {canViewOther, canEmployeeRead, canRemindersRead} = usePageSetup()

const displayedFields = computed(() => {
  if (canViewOther) {
    return tableFields
  }

  return tableFields.filter(field => field.key !== 'user')
})

const emit = defineEmits([
  'update:sort-by',
  'update:sort-desc',
  'mark-as-read',
  'mark-as-completed',
])

const handleSortUpdate = (e) => {
  emit('update:sort-by', e.sortBy)
  emit('update:sort-desc', e.sortDesc)
}

const handleMarkAsRead = (id) => {
  emit('mark-as-read', id)
}

const handleMarkAsCompleted = (id) => {
  emit('mark-as-completed', id)
}
</script>

<template>
  <div class="card table-card">
    <div class="card-body pt-0">
      <b-table
        :fields="displayedFields"
        :items="props.items"
        :busy="props.busy"
        :sort-by="props.sortBy"
        :sort-desc="props.sortDesc"
        :current-page="props.currentPage"
        striped hover
        no-local-sorting
        @sort-changed="handleSortUpdate"
      >
        <template #cell(content)="{ item }">
          <div v-html="item.content"></div>
        </template>

        <template #cell(user)="{ item }">
          <a v-if="canEmployeeRead" target="_blank" :href="item.user_url">{{ item.user }}</a>
          <span v-else>{{ item.user }}</span>
        </template>

        <template #cell(initiator_user)="{ item }">
          <a v-if="canEmployeeRead" target="_blank" :href="item.initiator_user_url">{{ item.initiator_user }}</a>
          <span v-else>{{ item.initiator_user }}</span>
        </template>

        <template #cell(reminder_title)="{ item }">
          <a v-if="canRemindersRead" target="_blank" :href="item.reminder_url">{{ item.reminder_title }}</a>
          <span v-else>{{ item.reminder_title }}</span>
        </template>

        <template #cell(is_expired)="{ item }">
          <span v-if="item.is_expired" class="badge badge-danger">да</span>
          <span v-else class="badge badge-success">нет</span>
        </template>

        <template #cell(actions)="{ item }">
          <div class="d-flex justify-content-center align-items-start" style="gap: 5px">
            <b-btn
              v-if="item.can_mark_as_read"
              class="btn btn-sm btn-info"
              style="width: 32px; height: 32px;"
              @click="handleMarkAsRead(item.id)"
              title="Отметить как прочитанное"
            >
              <i class="fa fa-eye"></i>
            </b-btn>
            <b-btn
              v-if="item.can_mark_as_completed"
              color="primary"
              class="btn btn-sm btn-success"
              style="width: 32px; height: 32px;"
              @click="handleMarkAsCompleted(item.id)"
              title="Отметить как выполненное"
            >
              <i class="fa fa-check"></i>
            </b-btn>
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

pre.payload {
  white-space: pre-wrap;
}
</style>
