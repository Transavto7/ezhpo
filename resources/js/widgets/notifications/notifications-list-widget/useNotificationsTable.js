import {reactive, ref} from "vue";
import {fetchNotificationTableItems} from "@/widgets/notifications/notifications-list-widget/api";

export const useNotificationTable = () => {
  const fetchTablePending = ref(false)

  const table = reactive({
    items: [],
    total: 0,
  })

  const filter = reactive({
    notifications: null,
    search: null,
    users: null,
    initiatorUsers: null,
    reminders: null,
    expiresAtBegin: null,
    expiresAtEnd: null,
    readAtBegin: null,
    readAtEnd: null,
    completedAtBegin: null,
    completedAtEnd: null,
    createdAtBegin: null,
    createdAtEnd: null,
    status: null,
  })

  const resetFilter = () => {
    filter.notifications = null
    filter.search = null
    filter.users = null
    filter.initiatorUsers = null
    filter.reminders = null
    filter.expiresAtBegin = null
    filter.expiresAtEnd = null
    filter.readAtBegin = null
    filter.readdAtEnd = null
    filter.completedAtBegin = null
    filter.completedAtEnd = null
    filter.createdAtBegin = null
    filter.createdAtEnd = null
    filter.status = null
  }

  const params = reactive({
    page: 1,
    perPage: 100,
    sortBy: 'created_at',
    sortDesc: true,
  })

  const fetchNotificationsTable = async () => {
    fetchTablePending.value = true

    try {
      const {data} = await fetchNotificationTableItems({
        ...params,
        filters: {
          notifications: filter.notifications?.map((item) => item.id) ?? [],
          search: filter.search,
          users: filter.users?.map((item) => item.id) ?? [],
          initiatorUsers: filter.initiatorUsers?.map((item) => item.id) ?? [],
          reminders: filter.reminders?.map((item) => +item.id) ?? [],
          expiresAtBegin: filter.expiresAtBegin,
          expiresAtEnd: filter.expiresAtEnd,
          readAtBegin: filter.readAtBegin,
          readAtEnd: filter.readAtEnd,
          completedAtBegin: filter.completedAtBegin,
          completedAtEnd: filter.completedAtEnd,
          createdAtBegin: filter.createdAtBegin,
          createdAtEnd: filter.createdAtEnd,
          status: filter.status?.id ?? null,
        },
      })

      table.items = data.items;
      table.total = data.total;
    } catch (e) {
      console.error(e)
    } finally {
      fetchTablePending.value = false
    }
  }

  return {
    fetchTablePending,
    table,
    params,
    filter,
    resetFilter,
    fetchNotificationsTable
  }
}
