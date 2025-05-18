<script setup>
import {fetchNotificationsForSelect, fetchRemindersForSelect, fetchUsersForSelect} from "../shared/api";
import VSelectRemote from "@/ui/VSelectRemote.vue";
import VSelect from "@/ui/VSelect.vue";
import {usePageSetup} from "@/widgets/notifications/notifications-list-widget/usePageSetup";

const props = defineProps({
  filter: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits([
  'update',
])

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

const handleViewedAtBeginInput = (e) => {
  update('viewedAtBegin', e.target.value)
}

const handleViewedAtEndInput = (e) => {
  update('viewedAtEnd', e.target.value)
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
      <div class="col-12 col-md-4 col-lg-3 form-group">
        <label for="">Поиск</label>
        <input class="form-control" type="text" :value="props.filter.search" placeholder="Введите значение для поиска"
               @input="handleSearchInput">
      </div>
      <div class="col-12 col-md-4 col-lg-3 form-group">
        <v-select-remote
          id="notifications"
          :value="props.filter.notifications"
          label="Уведомления"
          @input="handleNotificationsInput"
          :fetch-options-action="fetchNotificationsForSelect"
          multiple
          clearable
        />
      </div>
      <div v-if="canViewOther" class="col-12 col-md-4 col-lg-3 form-group">
        <v-select-remote
          id="users"
          :value="props.filter.users"
          label="Пользователи"
          @input="handleUsersInput"
          :fetch-options-action="fetchUsersForSelect"
          multiple
          clearable
        />
      </div>
      <div class="col-12 col-md-4 col-lg-3 form-group">
        <v-select-remote
          id="initiatorUsers"
          :value="props.filter.initiatorUsers"
          label="Пользователи, вызвавшие получение уведомления"
          @input="handleInitiatorUsersInput"
          :fetch-options-action="fetchUsersForSelect"
          multiple
          clearable
        />
      </div>
      <div class="col-12 col-md-4 col-lg-3 form-group">
        <v-select-remote
          id="reminders"
          :value="props.filter.reminders"
          label="Напоминания"
          @input="handleRemindersInput"
          :fetch-options-action="fetchRemindersForSelect"
          multiple
          clearable
        />
      </div>

      <div class="col-12 col-md-4 col-lg-3 form-group">
        <label for="viewedAtBegin">Дата просмотра</label>
        <div class="d-flex align-items-center justify-content-center" style="gap: 15px">
          <div class="d-flex align-items-center" style="gap: 5px; width: 100%">
            <span class="sub-label">С</span>
            <input v-model="props.filter.viewedAtBegin" type="date" name="viewedAtBegin" class="form-control" @input="handleViewedAtBeginInput" />
          </div>
          <div class="d-flex align-items-center" style="gap: 5px; width: 100%">
            <span class="sub-label">По</span>
            <input v-model="props.filter.viewedAtEnd" type="date" name="viewedAtEnd" class="form-control" @input="handleViewedAtEndInput" />
          </div>
        </div>
      </div>

      <div class="col-12 col-md-4 col-lg-3 form-group">
        <label for="readAtBegin">Дата прочтения</label>
        <div class="d-flex align-items-center justify-content-center" style="gap: 15px">
          <div class="d-flex align-items-center" style="gap: 5px; width: 100%">
            <span class="sub-label">С</span>
            <input v-model="props.filter.readAtBegin" type="date" name="readAtBegin" class="form-control" @input="handleReadAtBeginInput" />
          </div>
          <div class="d-flex align-items-center" style="gap: 5px; width: 100%">
            <span class="sub-label">По</span>
            <input v-model="props.filter.readAtEnd" type="date" name="readAtEnd" class="form-control" @input="handleReadAtEndInput" />
          </div>
        </div>
      </div>

      <div class="col-12 col-md-4 col-lg-3 form-group">
        <label for="viewedAtBegin">Дата выполнения</label>
        <div class="d-flex align-items-center justify-content-center" style="gap: 15px">
          <div class="d-flex align-items-center" style="gap: 5px; width: 100%">
            <span class="sub-label">С</span>
            <input v-model="props.filter.completedAtBegin" type="date" name="completedAtBegin" class="form-control" @input="handleCompletedAtBeginInput" />
          </div>
          <div class="d-flex align-items-center" style="gap: 5px; width: 100%">
            <span class="sub-label">По</span>
            <input v-model="props.filter.completedAtEnd" type="date" name="completedAtEnd" class="form-control" @input="handleCompletedAtEndInput" />
          </div>
        </div>
      </div>

      <div class="col-12 col-md-4 col-lg-3 form-group">
        <label for="viewedAtBegin">Когда истекает</label>
        <div class="d-flex align-items-center justify-content-center" style="gap: 15px">
          <div class="d-flex align-items-center" style="gap: 5px; width: 100%">
            <span class="sub-label">С</span>
            <input v-model="props.filter.expiresAtBegin" type="date" name="expiresAtBegin" class="form-control" @input="handleExpiresAtBeginInput" />
          </div>
          <div class="d-flex align-items-center" style="gap: 5px; width: 100%">
            <span class="sub-label">По</span>
            <input v-model="props.filter.expiresAtEnd" type="date" name="expiresAtEnd" class="form-control" @input="handleExpiresAtEndInput" />
          </div>
        </div>
      </div>

      <div class="col-12 col-md-4 col-lg-3 form-group">
        <label for="viewedAtBegin">Дата создания</label>
        <div class="d-flex align-items-center justify-content-center" style="gap: 15px">
          <div class="d-flex align-items-center" style="gap: 5px; width: 100%">
            <span class="sub-label">С</span>
            <input v-model="props.filter.createdAtBegin" type="date" name="createdAtBegin" class="form-control" @input="handleCreatedAtBeginInput" />
          </div>
          <div class="d-flex align-items-center" style="gap: 5px; width: 100%">
            <span class="sub-label">По</span>
            <input v-model="props.filter.createdAtEnd" type="date" name="createdAtEnd" class="form-control" @input="handleCreatedAtEndInput" />
          </div>
        </div>
      </div>

      <div class="col-12 col-md-4 col-lg-3 form-group">
        <v-select
          label="Статус"
          :value="props.filter.status"
          :options="statusOptions"
          clearable
          @input="handleStatusInput"
        />
      </div>

    </div>
  </div>
</template>

<style scoped lang="scss">
.form-group {
  overflow: visible;

  .sub-label {
    font-size: 12px;
  }

  input[type="date"] {
    width: 100%;
  }
}

.row {
  overflow: visible;
}
</style>
