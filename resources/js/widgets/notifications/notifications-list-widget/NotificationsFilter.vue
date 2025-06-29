<script setup>
import VSelect from '@/ui/select/VSelect.vue'
import VSelectRemote from '@/ui/select/VSelectRemote.vue'
import { usePageSetup } from '@/widgets/notifications/notifications-list-widget/usePageSetup'
import {
  fetchNotificationsForSelect,
  fetchRemindersForSelect,
  fetchUsersForSelect,
} from '../shared/api'

const props = defineProps({
  filter: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['update'])

const { canViewOther, statusOptions } = usePageSetup()

const update = (prop, value) => {
  emit('update', {
    prop,
    value,
  })
}

const handleSearchInput = (e) => {
  update('search', e.target.value)
}

const handleNotificationsInput = (value) => {
  update('notifications', value)
}

const handleUsersInput = (value) => {
  update('users', value)
}

const handleInitiatorUsersInput = (value) => {
  update('initiatorUsers', value)
}

const handleRemindersInput = (value) => {
  update('reminders', value)
}

const handleReadAtBeginInput = (e) => {
  update('readAtBegin', e.target.value)
}

const handleReadAtEndInput = (e) => {
  update('readAtEnd', e.target.value)
}

const handleExpiresAtBeginInput = (e) => {
  update('expiresAtBegin', e.target.value)
}

const handleExpiresAtEndInput = (e) => {
  update('expiresAtEnd', e.target.value)
}

const handleCompletedAtBeginInput = (e) => {
  update('completedAtBegin', e.target.value)
}

const handleCompletedAtEndInput = (e) => {
  update('completedAtEnd', e.target.value)
}

const handleCreatedAtBeginInput = (e) => {
  update('createdAtBegin', e.target.value)
}

const handleCreatedAtEndInput = (e) => {
  update('createdAtEnd', e.target.value)
}

const handleStatusInput = (value) => {
  update('status', value)
}
</script>

<template>
  <div>
    <div class="row">
      <div class="col-12 col-md-6 col-xl-4 form-group">
        <label for="">Поиск</label>
        <input
          class="form-control"
          placeholder="Введите значение для поиска"
          type="text"
          :value="props.filter.search"
          @input="handleSearchInput"
        />
      </div>
      <div class="col-12 col-md-6 col-xl-4 form-group">
        <v-select-remote
          id="notifications"
          clearable
          :fetch-options-action="fetchNotificationsForSelect"
          label="Уведомления"
          multiple
          :value="props.filter.notifications"
          @input="handleNotificationsInput"
        />
      </div>
      <div class="col-12 col-md-6 col-xl-4 form-group">
        <v-select
          clearable
          label="Статус"
          :options="statusOptions"
          :value="props.filter.status"
          @input="handleStatusInput"
        />
      </div>
      <div
        v-if="canViewOther"
        class="col-12 col-md-6 col-xl-4 form-group"
      >
        <v-select-remote
          id="users"
          clearable
          :fetch-options-action="fetchUsersForSelect"
          label="Получатели"
          multiple
          :value="props.filter.users"
          @input="handleUsersInput"
        />
      </div>
      <div class="col-12 col-md-6 col-xl-4 form-group">
        <v-select-remote
          id="initiatorUsers"
          clearable
          :fetch-options-action="fetchUsersForSelect"
          label="Инициаторы"
          multiple
          :value="props.filter.initiatorUsers"
          @input="handleInitiatorUsersInput"
        />
      </div>
      <div class="col-12 col-md-6 col-xl-4 form-group">
        <v-select-remote
          id="reminders"
          clearable
          :fetch-options-action="fetchRemindersForSelect"
          label="Напоминания"
          multiple
          :value="props.filter.reminders"
          @input="handleRemindersInput"
        />
      </div>
    </div>

    <div class="row">
      <div class="col-12 col-lg-6 col-xl-6 form-group">
        <div class="range-filter-block">
          <label for="readAtBegin">Дата прочтения</label>
          <div
            class="d-flex align-items-center justify-content-center"
            style="gap: 15px"
          >
            <div
              class="d-flex align-items-center"
              style="gap: 5px; width: 100%"
            >
              <span class="sub-label">С</span>
              <input
                class="form-control"
                name="readAtBegin"
                type="date"
                :value="props.filter.readAtBegin"
                @input="handleReadAtBeginInput"
              />
            </div>
            <div
              class="d-flex align-items-center"
              style="gap: 5px; width: 100%"
            >
              <span class="sub-label">По</span>
              <input
                class="form-control"
                name="readAtEnd"
                type="date"
                :value="props.filter.readAtEnd"
                @input="handleReadAtEndInput"
              />
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6 col-xl-6 form-group">
        <div class="range-filter-block">
          <label for="completedAtBegin">Дата выполнения</label>
          <div
            class="d-flex align-items-center justify-content-center"
            style="gap: 15px"
          >
            <div
              class="d-flex align-items-center"
              style="gap: 5px; width: 100%"
            >
              <span class="sub-label">С</span>
              <input
                class="form-control"
                name="completedAtBegin"
                type="date"
                :value="props.filter.completedAtBegin"
                @input="handleCompletedAtBeginInput"
              />
            </div>
            <div
              class="d-flex align-items-center"
              style="gap: 5px; width: 100%"
            >
              <span class="sub-label">По</span>
              <input
                class="form-control"
                name="completedAtEnd"
                type="date"
                :value="props.filter.completedAtEnd"
                @input="handleCompletedAtEndInput"
              />
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6 col-xl-6 form-group">
        <div class="range-filter-block">
          <label for="expiredAtBegin">Когда истекает</label>
          <div
            class="d-flex align-items-center justify-content-center"
            style="gap: 15px"
          >
            <div
              class="d-flex align-items-center"
              style="gap: 5px; width: 100%"
            >
              <span class="sub-label">С</span>
              <input
                class="form-control"
                name="expiresAtBegin"
                type="date"
                :value="props.filter.expiresAtBegin"
                @input="handleExpiresAtBeginInput"
              />
            </div>
            <div
              class="d-flex align-items-center"
              style="gap: 5px; width: 100%"
            >
              <span class="sub-label">По</span>
              <input
                class="form-control"
                name="expiresAtEnd"
                type="date"
                :value="props.filter.expiresAtEnd"
                @input="handleExpiresAtEndInput"
              />
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6 col-xl-6 form-group">
        <div class="range-filter-block">
          <label for="createdAtBegin">Дата создания</label>
          <div
            class="d-flex align-items-center justify-content-center"
            style="gap: 15px"
          >
            <div
              class="d-flex align-items-center"
              style="gap: 5px; width: 100%"
            >
              <span class="sub-label">С</span>
              <input
                class="form-control"
                name="createdAtBegin"
                type="date"
                :value="props.filter.createdAtBegin"
                @input="handleCreatedAtBeginInput"
              />
            </div>
            <div
              class="d-flex align-items-center"
              style="gap: 5px; width: 100%"
            >
              <span class="sub-label">По</span>
              <input
                class="form-control"
                name="createdAtEnd"
                type="date"
                :value="props.filter.createdAtEnd"
                @input="handleCreatedAtEndInput"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.form-group {
  overflow: visible;
}

.row {
  overflow: visible;
}

.range-filter-block {
  padding: 10px;
  border-radius: 5px;
  background-color: #f6f6f6;

  label {
    font-size: 12px !important;
    color: #323232;
  }

  .sub-label {
    font-size: 12px;
    color: #686868;
    text-transform: lowercase;
  }

  input[type='date'] {
    width: 100%;
  }
}
</style>
