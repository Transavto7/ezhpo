<script setup>
import {useIndexPageSetup} from "@/widgets/users/users-index/useIndexPageSetup";
import {computed} from "vue";
import tableFields from "@/widgets/users/users-index/tableFields";
import EntityTypeChip from "@/widgets/users/shared/EntityTypeChip.vue";
import UserRestrictionControl from "@/features/user-restriction-control/UserRestrictionControl.vue";

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
  'update-user',
  'logs-read',
])

const { permissions } = useIndexPageSetup()

const handleSortUpdate = (e) => {
  emit('update:sort-by', e.sortBy)
  emit('update:sort-desc', e.sortDesc)
}

const handleUpdateUser = () => {
  emit('update-user')
}

const handleLogsRead = (id) => {
  emit('logs-read', id)
}

const handleCopyToken = (token) => {
  navigator.clipboard.writeText(token)
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
        <template #cell(entity_type)="{ item }">
          <div class="d-flex justify-content-center align-items-center">
            <entity-type-chip
              :label="item.entity_type_label"
              :entity-type="item.entity_type"
            />
          </div>
        </template>

        <template #cell(login)="{ item }">
          <b-link :href="item.show_url">{{ item.login }}</b-link>
        </template>

        <template #cell(email)="{ item }">
          <span v-if="item.entity_type !== 'terminal'">{{ item.email }}</span>
        </template>

        <template #cell(company)="{ item }">
          <div class="text-center">
            {{ item.company }}
          </div>
        </template>

        <template #cell(roles)="{ item }">
          <div class="d-flex flex-wrap justify-content-center" style="gap: 5px">
            <span
              v-for="(roleName, index) of item.roles"
              :key="index"
              class="badge badge-success"
            >
              {{ roleName }}
            </span>
          </div>
        </template>

        <template #cell(api_token)="{ item }">
          <div v-if="item.entity_type === 'terminal' || item.entity_type === 'employee'" class="d-flex justify-content-center">
            <b-tooltip :target="`table-api-btn-${item.id}`" placement="top">
              {{ item.api_token }}
            </b-tooltip>
            <b-btn
              :id="`table-api-btn-${item.id}`"
              class="btn btn-sm" style="font-size: 12px; width: 32px; height: 32px;"
              @click="handleCopyToken(item.api_token)"
            >
              <i class="fa fa-copy"></i>
            </b-btn>
          </div>
        </template>

        <template #cell(updated_at)="{ item }">
          {{ item.updated_at }}
        </template>

        <template #cell(actions)="{ item }">
          <div class="d-flex justify-content-center align-items-start" style="gap: 5px">
            <b-btn
              class="btn btn-sm btn-secondary"
              style="width: 32px; height: 32px;"
              v-if="permissions.canReadLogs"
              @click="handleLogsRead(item.id)"
              title="Журнал действий"
            >
              <i class="fa fa-book"></i>
            </b-btn>
            <user-restriction-control
              v-if="permissions.canBlock"
              :user-blocked="item.blocked"
              :user-id="item.id"
              @changed="handleUpdateUser"
            />
          </div>
        </template>
      </b-table>
    </div>
  </div>
</template>

<style lang="scss">
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

td, th {
  vertical-align: middle!important;
}
</style>
