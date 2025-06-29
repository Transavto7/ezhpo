<script setup>
import { computed } from 'vue'
import ExpiredBadge from '@/widgets/notifications/notifications-list-widget/ExpiredBadge.vue'
import { usePageSetup } from '@/widgets/notifications/notifications-list-widget/usePageSetup'
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
    default: () => ({}),
  },
})

const { canViewOther, canEmployeeRead, canRemindersRead } = usePageSetup()

const displayedFields = computed(() => {
  if (canViewOther) {
    return tableFields
  }

  return tableFields.filter((field) => field.key !== 'user')
})

const emit = defineEmits([
  'update:sort-by',
  'update:sort-desc',
  'mark-as-read',
  'mark-as-completed',
])

const prettifyContent = (content) => {
  if (content.length > 250) {
    return `${content.substring(0, 250)}...`
  }

  return content
}

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
        :busy="props.busy"
        :current-page="props.currentPage"
        :fields="displayedFields"
        hover
        :items="props.items"
        no-local-sorting
        :sort-by="props.sortBy"
        :sort-desc="props.sortDesc"
        striped
        @sort-changed="handleSortUpdate"
      >
        <template #cell(title)="{ item }">
          <div>{{ item.title }}</div>

          <expired-badge
            v-if="item.is_expired"
            class="d-inline-block mt-1"
          />
        </template>

        <template #cell(content)="{ item }">
          <div v-html="prettifyContent(item.content)"></div>
        </template>

        <template #cell(user)="{ item }">
          <a
            v-if="canEmployeeRead"
            :href="item.user_url"
            rel="noopener noreferrer"
            target="_blank"
          >
            {{ item.user }}
          </a>
          <span v-else>{{ item.user }}</span>
        </template>

        <template #cell(initiator_user)="{ item }">
          <a
            v-if="canEmployeeRead"
            :href="item.initiator_user_url"
            rel="noopener noreferrer"
            target="_blank"
          >
            {{ item.initiator_user }}
          </a>
          <span v-else>{{ item.initiator_user }}</span>
        </template>

        <template #cell(reminder_title)="{ item }">
          <a
            v-if="canRemindersRead"
            :href="item.reminder_url"
            rel="noopener noreferrer"
            target="_blank"
          >
            {{ item.reminder_title }}
          </a>
          <span v-else>{{ item.reminder_title }}</span>
        </template>

        <template #cell(is_expired)="{ item }">
          <span
            v-if="item.is_expired"
            class="badge badge-danger"
          >
            да
          </span>
          <span
            v-else
            class="badge badge-success"
          >
            нет
          </span>
        </template>

        <template #cell(actions)="{ item }">
          <div
            class="d-flex justify-content-center align-items-start"
            style="gap: 5px"
          >
            <b-btn
              v-if="item.can_mark_as_read"
              class="btn btn-sm btn-info"
              style="width: 32px; height: 32px"
              title="Отметить как прочитанное"
              @click="handleMarkAsRead(item.id)"
            >
              <i class="fa fa-eye"></i>
            </b-btn>
            <b-btn
              v-if="item.can_mark_as_completed"
              class="btn btn-sm btn-success"
              color="primary"
              style="width: 32px; height: 32px"
              title="Отметить как выполненное"
              @click="handleMarkAsCompleted(item.id)"
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
