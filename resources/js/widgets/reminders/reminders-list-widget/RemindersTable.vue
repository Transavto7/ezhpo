<script setup>
import { usePageSetup } from '@/widgets/reminders/reminders-list-widget/usePageSetup'
import ContextReminderCell from './ContextReminderCell.vue'
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
})

const emit = defineEmits(['update:sort-by', 'update:sort-desc', 'delete', 'switch-status'])

const { canEdit, canDelete } = usePageSetup()

const handleSortUpdate = (e) => {
  emit('update:sort-by', e.sortBy)
  emit('update:sort-desc', e.sortDesc)
}

const handleDeleteButtonClick = (id) => {
  emit('delete', id)
}

const handleSwitchStatus = (id, enabled) => {
  emit('switch-status', {
    id,
    enabled,
  })
}
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
          {{ item.title }}
        </template>

        <template #cell(action)="{ item }">
          {{ item.action }}
        </template>

        <template #cell(context)="{ item }">
          <context-reminder-cell :context="item.context" />
        </template>

        <template #cell(enabled)="{ item }">
          <span
            v-if="item.enabled"
            class="badge badge-success"
          >
            активно
          </span>
          <span
            v-else
            class="badge badge-warning"
          >
            неактивно
          </span>
        </template>

        <template #cell(updated_at)="{ item }">
          {{ item.updated_at }}
        </template>

        <template #cell(actions)="{ item }">
          <div class="d-flex justify-content-center align-items-start">
            <button
              v-if="canEdit"
              class="btn btn-sm action-btn mr-2"
              :class="{
                'btn-warning': !item.enabled,
                'btn-success': item.enabled,
              }"
              :title="item.enabled ? 'Деактивировать' : 'Активировать'"
              @click.prevent="handleSwitchStatus(item.id, !item.enabled)"
            >
              <span
                v-if="item.enabled"
                class="fa fa-lock"
              ></span>
              <span
                v-else
                class="fa fa-unlock"
              ></span>
            </button>
            <a
              v-if="canEdit"
              class="btn btn-sm btn-info action-btn mr-2"
              :href="`/reminders/${item.id}`"
            >
              <span class="fa fa-edit"></span>
            </a>
            <button
              v-if="canDelete"
              class="btn btn-sm btn-danger action-btn"
              @click.prevent="handleDeleteButtonClick(item.id)"
            >
              <span class="fa fa-trash"></span>
            </button>
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
