<script setup>
import {
  fetchActionsForSelect, fetchNotificationsForSelect,
  fetchUsersForSelect
} from "../shared/api";
import {computed} from "vue";
import VSelectRemote from "@/ui/VSelectRemote.vue";

const props = defineProps({
  search: {
    type: String,
    required: false,
    default: null,
  },
  actions: {
    type: Array,
    required: false,
    default: null,
  },
  notifications: {
    type: Array,
    required: false,
    default: null,
  },
  users: {
    type: Array,
    required: false,
    default: null,
  },
})

const emit = defineEmits([
  'update:search',
  'update:actions',
  'update:users',
  'update:notifications',
])


const handleInputSearch = (e) => {
  emit('update:search', e.target.value)
}

const handleChangeActions = (value) => {
  emit('update:actions', value)
}

const handleChangeUsers = (value) => {
  emit('update:users', value)
}

const handleChangeNotifications = (value) => {
  emit('update:notifications', value)
}
</script>

<template>
  <div>
    <div class="row">
      <div class="col-12 col-md-4 col-lg-3 form-group">
        <label for="">Поиск</label>
        <input class="form-control" type="text" :value="props.search" placeholder="Введите значение для поиска"
               @input="handleInputSearch">
      </div>
      <div class="col-12 col-md-4 col-lg-3 form-group">
        <v-select-remote
          id="notifications"
          :value="notifications"
          label="Уведомления"
          @input="handleChangeNotifications"
          :fetch-options-action="fetchNotificationsForSelect"
          multiple
          clearable
        />
      </div>
      <div class="col-12 col-md-4 col-lg-3 form-group">
        <v-select-remote
          id="actions"
          :value="actions"
          label="Действия"
          @input="handleChangeActions"
          :fetch-options-action="fetchActionsForSelect"
          multiple
          clearable
        />
      </div>
      <div class="col-12 col-md-4 col-lg-3 form-group">
        <v-select-remote
          id="users"
          :value="users"
          label="Пользователи"
          @input="handleChangeUsers"
          :fetch-options-action="fetchUsersForSelect"
          multiple
          clearable
        />
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
</style>
