import {reactive, ref, watch} from "vue";
import {
  fetchAccessData,
  fetchPermissionItems,
  fetchPermissionsByRoles,
  fetchRoleItems, updateUserAccess
} from "@/features/user-access-settings/api";
import Swal2 from "sweetalert2";

export const useActions = () => {
  const loaded = ref(false)
  const pending = ref(false)
  const accessData = reactive({
    roles: [],
    permissions: [],
    rolesPermissions: [],
  })

  const dictionaries = reactive({
    permissions: [],
    roles: [],
  })

  const loadDictionaries = async () => {
    const {data: permissions} = await fetchPermissionItems()
    dictionaries.permissions = permissions

    const {data: roles} = await fetchRoleItems()
    dictionaries.roles = roles
  }

  const loadAccessData = async (userId) => {
    const {data} = await fetchAccessData(userId)
    accessData.permissions = data.permissions.map((item) => +item.id)
    accessData.roles = data.roles
  }

  const loadRolesPermissions = async () => {
    const {data: rolesPermissions} = await fetchPermissionsByRoles(accessData.roles.map((item) => item.id))
    accessData.rolesPermissions = rolesPermissions

    accessData.permissions = accessData.permissions.filter((id) => !rolesPermissions.includes(id))
  }

  const loadData = async (userId) => {
    await loadDictionaries()
    await loadAccessData(userId)
    await loadRolesPermissions()

    loaded.value = true
  }

  const performUpdateUserAccess = async (userId) => {
    pending.value = true

    try {
      await updateUserAccess(userId, {
        permissions: accessData.permissions,
        roles: accessData.roles.map((item) => item.id),
      })
      await Swal2.fire('Обновление параметров доступа', 'Параметры успешно изменены', 'success');
    } catch (e) {
      await Swal2.fire('Ошибка', e.message ?? 'Что-то пошло не так...', 'error');
      throw e
    } finally {
      pending.value = false
    }
  }

  watch(() => accessData.roles, async () => {
    if (!loaded.value) {
      return
    }

    await loadRolesPermissions()
  }, {
    deep: true,
  })

  return {
    pending,
    accessData,
    dictionaries,
    loadData,
    performUpdateUserAccess,
  }
}