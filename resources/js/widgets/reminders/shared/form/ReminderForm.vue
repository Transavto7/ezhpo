<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import VSwitcher from '@/ui/VSwitcher.vue'
import VTinyMce from '@/ui/VTinyMce.vue'
import VFlatPickr from '@/ui/datepickers/VFlatPickr.vue'
import VSelectRemote from '@/ui/select/VSelectRemote.vue'
import { SelectOption } from '@/ui/select/types'
import ErrorsList from '@/widgets/reminders/shared/form/ErrorsList.vue'
import { Reminder } from '@/widgets/reminders/types'
import {
  fetchActionsForSelect,
  fetchCompanyForSelect,
  fetchPointsForSelect,
  fetchReminderStatusesForSelect,
  fetchReminderTypesForSelect,
  fetchRolesForSelect,
  fetchSubjectTypesForSelect,
  fetchSubjectsForSelect,
  fetchTownsForSelect,
  fetchUsersForSelect,
} from '../api'

const props = defineProps<{
  form: Reminder
}>()

const emit = defineEmits(['submit', 'back'])

const reminder = ref(props.form)
const validationErrors = ref<string[]>([])

const validate = () => {
  const errors = []

  if (!reminder.value.title) {
    errors.push('Поле Наименование обязательно для заполнения!')
  }

  if (!reminder.value.type) {
    errors.push('Поле Тип уведомления обязательно для заполнения!')
  }

  if (!reminder.value.status) {
    errors.push('Поле Статус обязательно для заполнения!')
  }

  if (!reminder.value.content) {
    errors.push('Поле Контент обязательно для заполнения!')
  }

  if (!reminder.value.action) {
    errors.push('Поле Действие обязательно для заполнения!')
  }

  return errors
}

const handleSaveClick = () => {
  validationErrors.value = validate()

  if (validationErrors.value.length) {
    return
  }

  emit('submit', reminder.value)
}

const handleBackClick = () => {
  emit('back')
}

watch(props.form, () => {
  reminder.value = props.form
})

const handleChangeSubjectType = (value: SelectOption | SelectOption[] | null) => {
  if (!Array.isArray(value)) {
    reminder.value.conditions.subject_type = value
    reminder.value.conditions.subject = null
  }
}

const innerFetchSubjectsForSelect = (params: { search: string | null }) => {
  return fetchSubjectsForSelect({
    search: params.search,
    subjectType: reminder.value.conditions.subject_type?.id ?? null,
  })
}

const disableSubject = computed(() => {
  return reminder.value.conditions.subject_type === null
})
</script>

<template>
  <div class="p-5">
    <div class="row">
      <div class="col-8">
        <b-form-group
          id="name"
          label="Наименование:"
          label-for="name"
        >
          <b-form-input
            id="name"
            v-model="reminder.title"
            placeholder="Введите имя напоминания"
            required
            type="text"
          ></b-form-input>
        </b-form-group>
      </div>
    </div>

    <div class="row">
      <div class="col-4">
        <b-form-group
          id="name"
          label="Тип уведомления:"
          label-for="name"
        >
          <v-select-remote
            id="subject"
            v-model="reminder.type"
            :fetch-options-action="fetchReminderTypesForSelect"
          />
        </b-form-group>
      </div>
      <div class="col-4">
        <b-form-group
          label="Статус:"
          label-for="content"
        >
          <v-select-remote
            id="subject"
            v-model="reminder.status"
            :fetch-options-action="fetchReminderStatusesForSelect"
          />
        </b-form-group>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div
          class="d-flex align-items-center"
          style="gap: 8px"
        >
          <v-switcher v-model="reminder.hiddenFromInitiator" />
          <label style="margin-bottom: 0">Не уведомлять пользователей, совершивших действие:</label>
        </div>
      </div>
    </div>

    <hr />

    <div class="row">
      <div class="col-12">
        <b-form-group
          label="Срок выполнения:"
          label-for="test"
          style="font-size: 13px"
        >
          <div
            class="d-flex align-items-center"
            style="gap: 10px"
          >
            <div>
              <label
                for="expiresAt"
                style="font-size: 11px"
              >
                Дата и время
              </label>
              <v-flat-pickr
                v-model="reminder.expiresAt"
                alt-format="d.m.Y H:i"
                date-format="Y-m-d h:i"
                enable-time
                placeholder="Выберите дату и время"
              />
            </div>
            <div>
              <label
                for="expiresInMinutes"
                style="font-size: 11px"
                >Кол-во минут</label
              >
              <b-form-input
                id="expiresInMinutes"
                v-model="reminder.expiresInMinutes"
                :disabled="!!reminder.expiresAt"
                :min="1"
                placeholder="Введите значение"
                required
                type="number"
              />
            </div>
          </div>
          <div
            class="mt-1"
            style="font-size: 11px; color: #7a7a7a"
          >
            Укажите дату – крайний срок выполнения, или количество минут, отведенных для выполнения.
          </div>
        </b-form-group>
      </div>
    </div>

    <div class="row">
      <div class="col-8">
        <b-form-group
          id="name"
          class="mb-1"
          label="Уведомлять пользователей:"
          label-for="subject"
        >
          <v-select-remote
            id="subject"
            v-model="reminder.usersToNotify"
            clearable
            :fetch-options-action="fetchUsersForSelect"
            multiple
          />
        </b-form-group>
      </div>
    </div>

    <hr />

    <b-row>
      <b-col>
        <b-form-group
          label="Контент:"
          label-for="content"
        >
          <v-tiny-mce
            id="content"
            v-model="reminder.content"
            required
          ></v-tiny-mce>
        </b-form-group>
      </b-col>
    </b-row>

    <div class="row mb-3">
      <div class="col-md-6">
        <v-select-remote
          id="action"
          v-model="reminder.action"
          :fetch-options-action="fetchActionsForSelect"
          label="Действие"
        />
      </div>

      <div class="col-md-6">
        <v-select-remote
          id="city"
          v-model="reminder.conditions.city"
          clearable
          :fetch-options-action="fetchTownsForSelect"
          label="Город"
        />
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <v-select-remote
          id="point"
          v-model="reminder.conditions.point"
          clearable
          :fetch-options-action="fetchPointsForSelect"
          label="ПВ"
        />
      </div>

      <div class="col-md-6">
        <v-select-remote
          id="user"
          v-model="reminder.conditions.user"
          clearable
          :fetch-options-action="fetchUsersForSelect"
          label="Пользователь"
        />
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <v-select-remote
          id="role"
          v-model="reminder.conditions.role"
          clearable
          :fetch-options-action="fetchRolesForSelect"
          label="Роль"
        />
      </div>

      <div class="col-md-6">
        <v-select-remote
          id="company"
          v-model="reminder.conditions.company"
          clearable
          :fetch-options-action="fetchCompanyForSelect"
          label="Компания"
        />
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <v-select-remote
          id="subject_type"
          clearable
          :fetch-options-action="fetchSubjectTypesForSelect"
          label="Тип субъекта"
          :value="reminder.conditions.subject_type"
          @input="handleChangeSubjectType"
        />
      </div>

      <div class="col-md-6">
        <v-select-remote
          id="subject"
          v-model="reminder.conditions.subject"
          clearable
          :disabled="disableSubject"
          :fetch-options-action="innerFetchSubjectsForSelect"
          label="Cубъект"
        />
      </div>
    </div>

    <hr v-if="validationErrors.length" />

    <errors-list :errors="validationErrors" />

    <div class="mt-4 d-flex justify-content-center align-items-center">
      <button
        class="btn btn-secondary mr-2"
        @click.prevent="handleBackClick"
      >
        Назад
      </button>
      <button
        class="btn btn-primary"
        :disabled="!!validationErrors.length"
        @click.prevent="handleSaveClick"
      >
        Сохранить
      </button>
    </div>
  </div>
</template>

<style scoped></style>
