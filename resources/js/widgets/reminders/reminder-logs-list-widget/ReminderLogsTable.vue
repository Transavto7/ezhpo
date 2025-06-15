<script setup>
import { usePageSetup } from '@/widgets/reminders/reminder-logs-list-widget/usePageSetup'
import PayloadReminderCell from './PayloadReminderCell.vue'
import tableFields from './tableFields'

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
  },
})

const emit = defineEmits(['update:sort-by', 'update:sort-desc'])

const { canEmployeeRead, canRemindersRead } = usePageSetup()

const handleSortUpdate = (e) => {
  emit('update:sort-by', e.sortBy)
  emit('update:sort-desc', e.sortDesc)
}

const showPayloadWithAction = tableFields.find((field) => field.key === 'payload').showWithAction
</script>

<template>
  <div class="card table-card">
    <div class="card-body pt-0">
      <b-table
        :busy="props.busy"
        :current-page="props.currentPage"
        :fields="tableFields"
        hover
        :items="props.items"
        no-local-sorting
        :sort-by="props.sortBy"
        :sort-desc="props.sortDesc"
        striped
        @sort-changed="handleSortUpdate"
      >
        <template #cell(title)="{ item }">
          <a
            v-if="canRemindersRead"
            :href="item.reminder_url"
            target="_blank"
            >{{ item.title }}</a
          >
          <span v-else>{{ item.title }}</span>
        </template>

        <template #cell(action)="{ item }">
          {{ item.action.name }}
        </template>

        <template #cell(user)="{ item }">
          <a
            v-if="canEmployeeRead"
            :href="item.user_url"
            target="_blank"
            >{{ item.user }}</a
          >
          <span v-else>{{ item.user }}</span>
        </template>

        <template #cell(payload)="{ item }">
          <PayloadReminderCell
            :map-list="mapList"
            :payload="item.payload"
          />
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
