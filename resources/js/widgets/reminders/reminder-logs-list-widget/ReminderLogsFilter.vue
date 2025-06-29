<script setup>
import { computed } from 'vue'
import VSelectRemote from '@/ui/select/VSelectRemote.vue'
import {
  fetchActionsForSelect,
  fetchCompanyForSelect,
  fetchPointsForSelect,
  fetchRolesForSelect,
  fetchSubjectTypesForSelect,
  fetchTownsForSelect,
  fetchUsersForSelect,
} from '../shared/api'

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
  cities: {
    type: Array,
    required: false,
    default: null,
  },
  points: {
    type: Array,
    required: false,
    default: null,
  },
  users: {
    type: Array,
    required: false,
    default: null,
  },
  roles: {
    type: Array,
    required: false,
    default: null,
  },
  companies: {
    type: Array,
    required: false,
    default: null,
  },
  subject_type: {
    type: Object,
    required: false,
    default: null,
  },
})

const emit = defineEmits([
  'update:search',
  'update:actions',
  'update:cities',
  'update:points',
  'update:users',
  'update:roles',
  'update:companies',
  'update:subject_type',
])

const handleInputSearch = (e) => {
  emit('update:search', e.target.value)
}

const handleChangeActions = (value) => {
  emit('update:actions', value)
}

const handleChangeCities = (value) => {
  emit('update:cities', value)
}

const handleChangePoints = (value) => {
  emit('update:points', value)
}

const handleChangeUsers = (value) => {
  emit('update:users', value)
}

const handleChangeRoles = (value) => {
  emit('update:roles', value)
}

const handleChangeCompanies = (value) => {
  emit('update:companies', value)
}

const handleChangeSubjectTypes = (value) => {
  emit('update:subject_type', value)
}

const disableSubjectFilter = computed(() => {
  return props.subject_type === null
})
</script>

<template>
  <div>
    <div class="row">
      <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
        <label for="">Поиск</label>
        <input
          class="form-control"
          placeholder="Введите значение для поиска"
          type="text"
          :value="props.search"
          @input="handleInputSearch"
        />
      </div>
      <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
        <v-select-remote
          id="actions"
          clearable
          :fetch-options-action="fetchActionsForSelect"
          label="Действия (триггеры)"
          multiple
          :value="actions"
          @input="handleChangeActions"
        />
      </div>
      <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
        <v-select-remote
          id="cities"
          clearable
          :fetch-options-action="fetchTownsForSelect"
          label="Город"
          multiple
          :value="cities"
          @input="handleChangeCities"
        />
      </div>
      <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
        <v-select-remote
          id="points"
          clearable
          :fetch-options-action="fetchPointsForSelect"
          label="ПВ"
          multiple
          :value="points"
          @input="handleChangePoints"
        />
      </div>
      <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
        <v-select-remote
          id="users"
          clearable
          :fetch-options-action="fetchUsersForSelect"
          label="Пользователи"
          multiple
          :value="users"
          @input="handleChangeUsers"
        />
      </div>
      <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
        <v-select-remote
          id="roles"
          clearable
          :fetch-options-action="fetchRolesForSelect"
          label="Роли"
          multiple
          :value="roles"
          @input="handleChangeRoles"
        />
      </div>
      <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
        <v-select-remote
          id="companies"
          clearable
          :fetch-options-action="fetchCompanyForSelect"
          label="Компании"
          multiple
          :value="companies"
          @input="handleChangeCompanies"
        />
      </div>
      <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
        <v-select-remote
          id="subject_types"
          clearable
          :fetch-options-action="fetchSubjectTypesForSelect"
          label="Тип субъекта"
          :value="subject_type"
          @input="handleChangeSubjectTypes"
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
