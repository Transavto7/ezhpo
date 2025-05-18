<script setup>
import {computed, ref, watch} from "vue";
import VSelectRemote from "@/ui/VSelectRemote.vue";
import VTinyMce from "@/ui/VTinyMce.vue";
import {
  fetchActionsForSelect, fetchCompanyForSelect,
  fetchPointsForSelect, fetchReminderStatusesForSelect, fetchReminderTypesForSelect,
  fetchRolesForSelect, fetchSubjectsForSelect, fetchSubjectTypesForSelect,
  fetchTownsForSelect,
  fetchUsersForSelect
} from "./api";
import VSwitcher from "@/ui/VSwitcher.vue";

const props = defineProps({
  form: {
    type: Object,
    required: true,
  }
})

const emit = defineEmits(["submit", 'back']);

const reminder = ref(props.form);

const validateErrors = computed(() => {
  let errors = [];

  if (!reminder.value.title) {
    errors.push('Поле Наименование обязательно для заполнения!')
  }

  if (!reminder.value.content) {
    errors.push('Поле Контент обязательно для заполнения!')
  }

  if (!reminder.value.action) {
    errors.push('Поле Действие обязательно для заполнения!')
  }

  if (!reminder.value.type) {
    errors.push('Поле Тип уведомления обязательно для заполнения!')
  }

  if (!reminder.value.status) {
    errors.push('Поле Статус обязательно для заполнения!')
  }

  return errors;
})

const hasErrors = computed(() => {
  return validateErrors.value.length > 0
})

const handleSaveClick = () => {
  emit('submit', reminder.value)
}

const handleBackClick = () => {
  emit("back")
}

watch(props.form, () => {
  reminder.value = props.form
})

const handleChangeSubjectType = (type) => {
  reminder.value.conditions.subject_type = type
  reminder.value.conditions.subject = null
}

const innerFetchSubjectsForSelect = ({search}) => {
  return fetchSubjectsForSelect(search, reminder.value.conditions.subject_type.id)
}

const disableSubject = computed(() => {
  return reminder.value.conditions.subject_type === null;
})
</script>

<template>
  <b-container fluid class="py-3">
    <b-alert variant="danger" :show="hasErrors">
      Ошибка валидации!
      <ul>
        <li v-for="error in validateErrors">{{ error }}</li>
      </ul>
    </b-alert>
    <b-row>
      <b-col>
        <b-form-group
          id="name"
          label="Наименование:"
          label-for="name"
        >
          <b-form-input
            id="name"
            v-model="reminder.title"
            type="text"
            placeholder="Введите имя напоминания"
            required
          ></b-form-input>
        </b-form-group>
      </b-col>
      <b-col>
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
      </b-col>
      <b-col>
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
      </b-col>
    </b-row>

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
          label="Действие"
          :fetch-options-action="fetchActionsForSelect"
        />
      </div>

      <div class="col-md-6">
        <v-select-remote
          id="city"
          v-model="reminder.conditions.city"
          label="Город"
          clearable
          :fetch-options-action="fetchTownsForSelect"
        />
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <v-select-remote
          id="point"
          v-model="reminder.conditions.point"
          label="ПВ"
          clearable
          :fetch-options-action="fetchPointsForSelect"
        />
      </div>

      <div class="col-md-6">
        <v-select-remote
          id="user"
          v-model="reminder.conditions.user"
          label="Пользователь"
          clearable
          :fetch-options-action="fetchUsersForSelect"
        />
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <v-select-remote
          id="role"
          v-model="reminder.conditions.role"
          label="Роль"
          clearable
          :fetch-options-action="fetchRolesForSelect"
        />
      </div>

      <div class="col-md-6">
        <v-select-remote
          id="company"
          v-model="reminder.conditions.company"
          label="Компания"
          clearable
          :fetch-options-action="fetchCompanyForSelect"
        />
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <v-select-remote
          id="subject_type"
          :value="reminder.conditions.subject_type"
          :fetch-options-action="fetchSubjectTypesForSelect"
          @input="handleChangeSubjectType"
          label="Тип субъекта"
          clearable
        />
      </div>

      <div class="col-md-6">
        <v-select-remote
          id="subject"
          v-model="reminder.conditions.subject"
          label="Cубъект"
          clearable
          :fetch-options-action="innerFetchSubjectsForSelect"
          :disabled="disableSubject"
        />
      </div>
    </div>

    <hr>

    <b-row>
      <b-col>
        <b-form-group
          id="name"
          label="Срок выполнения:"
          label-for="name"
        >
          <div class="d-flex align-items-center" style="gap: 10px;">
            <div>
              <label for="expiresAt" style="font-size: 11px">Дата</label>
              <input v-model="reminder.expiresAt" type="date" id="expiresAt" class="form-control" @input="() => {}" />
            </div>
            <div>
              <label for="expiresInMinutes" style="font-size: 11px">Кол-во минут</label>
              <b-form-input
                id="expiresInMinutes"
                v-model="reminder.expiresInMinutes"
                type="number"
                placeholder="Введите значение"
                :disabled="!!reminder.expiresAt"
                :min="1"
                required
              />
            </div>
          </div>
          <div class="mt-1" style="font-size: 11px; color: #7a7a7a;">
            Укажите дату – крайний срок выполнения, или количество минут, отведенных для выполнения.
          </div>
        </b-form-group>
      </b-col>
    </b-row>

    <b-row>
      <b-col>
        <b-form-group
          id="name"
          label="Уведомлять пользователей:"
          label-for="name"
        >
          <v-select-remote
            id="subject"
            v-model="reminder.usersToNotify"
            clearable
            multiple
            :fetch-options-action="fetchUsersForSelect"
          />
        </b-form-group>
      </b-col>
    </b-row>

    <b-row>
      <b-col>
        <b-form-group
          id="name"
          label="Не уведомлять пользователей, совершивших действие:"
          label-for="name"
        >
          <v-switcher v-model="form.hiddenFromInitiator" />
        </b-form-group>
      </b-col>
    </b-row>

    <hr>

    <div class="mt-2 d-flex justify-content-center align-items-center">
      <button class="btn btn-secondary mr-2" @click.prevent="handleBackClick">Назад</button>
      <button class="btn btn-primary" @click.prevent="handleSaveClick" :disabled="hasErrors">Сохранить</button>
    </div>
  </b-container>
</template>

<style scoped>

</style>
