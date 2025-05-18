<script setup>
import {useActions} from "@/features/user-access-settings/useActions";
import {onMounted} from "vue";
import RolesSelect from "@/features/user-access-settings/RolesSelect.vue";
import PermissionsControl from "@/components/PermissionsControl.vue";

const props = defineProps({
  userId: {
    type: Number,
    required: true,
  },
  disabled: {
    type: Boolean,
    required: false,
    default: false,
  }
})

const emit = defineEmits(['changed'])

const {pending, accessData, dictionaries, loadData, performUpdateUserAccess} = useActions()

const handleSaveClick = async () => {
  try {
    await performUpdateUserAccess(props.userId)
    emit('changed')
  } catch (e) {
    console.error(e)
  }
}

onMounted(async () => {
  await loadData(props.userId)
})
</script>

<template>
  <div>
    <roles-select
      v-model="accessData.roles"
      :options="dictionaries.roles"
    />

    <permissions-control
      v-model="accessData.permissions"
      :mandatory-permissions="accessData.rolesPermissions"
      :items="dictionaries.permissions"
      class="mt-3"
    />

    <b-btn
      class="btn btn-sm btn-success mt-3"
      :disabled="props.disabled || pending"
      @click="handleSaveClick"
    >
      <i class="fa fa-shield"></i>
      <span class="ml-1">Сохранить</span>
    </b-btn>
  </div>
</template>

<style scoped>

</style>
