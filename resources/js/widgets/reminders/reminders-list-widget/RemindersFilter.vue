<script setup>
import VSelectRemote from "@/ui/select/VSelectRemote.vue";
import {
  fetchActionsForSelect, fetchCompanyForSelect,
  fetchPointsForSelect, fetchRemindersForSelect,
  fetchRolesForSelect, fetchSubjectsForSelect, fetchSubjectTypesForSelect,
  fetchTownsForSelect,
  fetchUsersForSelect
} from "../shared/api";
import {computed} from "vue";

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
  reminders: {
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
  subjects: {
    type: Array,
    required: false,
    default: null,
  },
})

const emit = defineEmits([
  'update:search',
  'update:actions',
  'update:reminders',
  'update:cities',
  'update:points',
  'update:users',
  'update:roles',
  'update:companies',
  'update:subject_type',
  'update:subjects',
])


const handleInputSearch = (e) => {
  emit('update:search', e.target.value)
}

const handleChangeActions = (value) => {
  emit('update:actions', value)
}

const handleChangeReminders = (value) => {
  emit('update:reminders', value)
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

const handleChangeSubjects = (value) => {
  emit('update:subjects', value)
}

const innerFetchSubjectsForSelect = ({search}) => {
  return fetchSubjectsForSelect(search, props.subject_type.id)
}

const disableSubjectFilter = computed(() => {
  return props.subject_type === null;
})
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
          id="actions"
          :value="actions"
          label="Действия(триггеры)"
          @input="handleChangeActions"
          :fetch-options-action="fetchActionsForSelect"
          multiple
          clearable
        />
      </div>
      <div class="col-12 col-md-4 col-lg-3 form-group">
        <v-select-remote
          id="reminders"
          :value="reminders"
          label="Напоминание"
          @input="handleChangeReminders"
          :fetch-options-action="fetchRemindersForSelect"
          multiple
          clearable
        />
      </div>
      <div class="col-12 col-md-4 col-lg-3 form-group">
        <v-select-remote
          id="cities"
          :value="cities"
          label="Город"
          @input="handleChangeCities"
          :fetch-options-action="fetchTownsForSelect"
          multiple
          clearable
        />
      </div>
      <div class="col-12 col-md-4 col-lg-3 form-group">
        <v-select-remote
          id="points"
          :value="points"
          label="ПВ"
          @input="handleChangePoints"
          :fetch-options-action="fetchPointsForSelect"
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
      <div class="col-12 col-md-4 col-lg-3 form-group">
        <v-select-remote
          id="roles"
          :value="roles"
          label="Роли"
          @input="handleChangeRoles"
          :fetch-options-action="fetchRolesForSelect"
          multiple
          clearable
        />
      </div>
      <div class="col-12 col-md-4 col-lg-3 form-group">
        <v-select-remote
          id="companies"
          :value="companies"
          label="Компании"
          @input="handleChangeCompanies"
          :fetch-options-action="fetchCompanyForSelect"
          multiple
          clearable
        />
      </div>
      <div class="col-12 col-md-4 col-lg-3 form-group">
        <v-select-remote
          id="subject_types"
          :value="subject_type"
          label="Тип субъекта"
          @input="handleChangeSubjectTypes"
          :fetch-options-action="fetchSubjectTypesForSelect"
          clearable
        />
      </div>
      <div class="col-12 col-md-4 col-lg-3 form-group">
        <v-select-remote
          id="subjects"
          :value="subjects"
          label="Субъект"
          @input="handleChangeSubjects"
          :fetch-options-action="innerFetchSubjectsForSelect"
          :disabled="disableSubjectFilter"
          clearable
          multiple
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
