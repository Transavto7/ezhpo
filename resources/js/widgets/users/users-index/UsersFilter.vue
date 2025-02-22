<script setup>
import {useIndexPageSetup} from "@/widgets/users/users-index/useIndexPageSetup";
import VSelectRemote from "@/v-components/VSelectRemote.vue";
import {fetchUsersSelect} from "@/widgets/users/users-index/api";

const {statusFilterOptions, entityTypeFilterOptions} = useIndexPageSetup()

const props = defineProps({
  search: {
    type: String,
    required: false,
    default: null,
  },
  users: {
    type: Array,
    required: false,
  },
  status: {
    type: String,
    required: false,
    default: null,
  },
  entityType: {
    type: String,
    required: false,
    default: null,
  },
})

const emit = defineEmits(['update:status', 'update:users', 'update:search', 'update:entity-type'])

const handleInputSearch = (e) => {
  emit('update:search', e.target.value)
}

const handleChangeUsers = (value) => {
  emit('update:users', value)
}

const handleChangeStatus = (e) => {
  emit('update:status', e.target.value)
}

const handleChangeEntityType = (e) => {
  emit('update:entity-type', e.target.value)
}

const usersSelectAction = async (params) => {
  return await fetchUsersSelect({
    ...params,
    entityType: props.entityType ?? null,
  })
}
</script>

<template>
  <div>
    <div class="row">
      <div class="col-12 col-md-4 col-lg-3 form-group">
        <label for="">Поиск</label>
        <input class="form-control" type="text" :value="props.search" placeholder="Введите значение для поиска" @input="handleInputSearch">
      </div>
      <div class="col-12 col-md-4 col-lg-3 form-group">
        <label for="">Заблокирован</label>
        <select class="form-control" style="color: gray;" @change="handleChangeStatus">
          <option value="" selected>Выберите значение</option>
          <option
            v-for="item of statusFilterOptions"
            :key="item.value"
            :value="item.value"
            :selected="props.status === item.value"
          >
            {{ item.label }}
          </option>
        </select>
      </div>
      <div class="col-12 col-md-4 col-lg-3 form-group">
        <label for="">Тип</label>
        <select class="form-control" style="color: gray;" @change="handleChangeEntityType">
          <option value="" selected>Выберите значение</option>
          <option
            v-for="item of entityTypeFilterOptions"
            :key="item.value"
            :value="item.value"
            :selected="props.entityType === item.value"
          >
            {{ item.label }}
          </option>
        </select>
      </div>
      <div class="col-12 col-md-4 col-lg-3 form-group">
        <label for="">Связанная сущность</label>
        <v-select-remote
          :value="props.users"
          multiple
          @input="handleChangeUsers"
          :fetch-options-action="usersSelectAction"
        />
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>