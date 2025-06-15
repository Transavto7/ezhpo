import Swal2 from 'sweetalert2'
import { reactive, ref } from 'vue'
import { deleteReminderApi, fetchRemindersTableItems } from './api'

export const useRemindersTable = () => {
  const fetchTablePending = ref(false)

  const table = reactive({
    items: [],
    total: 0,
  })

  const filter = reactive({
    search: null,
    actions: null,
    cities: null,
    reminders: null,
    points: null,
    users: null,
    roles: null,
    companies: null,
    subject_type: null,
    subjects: null,
  })

  const resetFilter = () => {
    filter.search = null
    filter.actions = null
    filter.reminders = null
    filter.cities = null
    filter.points = null
    filter.users = null
    filter.roles = null
    filter.companies = null
    filter.subject_type = null
    filter.subjects = null
  }

  const params = reactive({
    page: 1,
    perPage: 100,
    sortBy: 'updated_at',
    sortDesc: true,
  })

  const fetchRemindersTable = async () => {
    fetchTablePending.value = true

    try {
      const { data } = await fetchRemindersTableItems({
        ...params,
        filters: {
          search: filter.search,
          actions: filter.actions?.map((item) => item.id),
          reminders: filter.reminders?.map((item) => item.id),
          cities: filter.cities?.map((item) => item.id),
          points: filter.points?.map((item) => item.id),
          users: filter.users?.map((item) => item.id),
          roles: filter.roles?.map((item) => item.id),
          companies: filter.companies?.map((item) => item.id),
          subject_type: filter.subject_type?.id,
          subjects: filter.subjects?.map((item) => item.id),
        },
      })

      table.items = data.items
      table.total = data.total
    } catch (e) {
      console.error(e)
    } finally {
      fetchTablePending.value = false
    }
  }

  const performDeleteReminder = async (id) => {
    const result = await Swal2.fire({
      title: 'Вы уверены, что хотите удалить?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Да, удалить!',
      cancelButtonText: 'Отмена',
    })

    if (!result.isConfirmed) {
      return
    }

    try {
      await deleteReminderApi(id)
      await Swal2.fire('Удалено', 'Данные были успешно удалены', 'success')

      return Promise.resolve()
    } catch (e) {
      await Swal2.fire('Ошибка', e.message || 'Что-то пошло не так', 'warning')

      return Promise.reject()
    }
  }

  return {
    fetchTablePending,
    table,
    params,
    filter,
    resetFilter,
    fetchRemindersTable,
    performDeleteReminder,
  }
}
