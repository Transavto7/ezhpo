import {reactive, ref, watch} from "vue";
import {fetchUsersTableItems} from "@/widgets/users/users-index/api";
import {debounce} from "lodash";

export const useUsersTable = () => {
  const fetchTablePending = ref(false)

  const table = reactive({
    items: [],
    total: 0,
  })

  const params = reactive({
    page: 1,
    perPage: 50,
    sortBy: 'updated_at',
    sortDesc: true,
  })

  const filter = reactive({
    search: null,
    users: [],
    status: null,
    entityType: null,
  })

  const resetFilter = () => {
    filter.search = null
    filter.users = []
    filter.status = null
    filter.entityType = null
  }

  const fetchUsersTable = async () => {
    fetchTablePending.value = true

    try {
      const {data} = await fetchUsersTableItems({
        ...params,
        filter: {
          ...filter,
          userIds: filter.users.map((item) => +item.id)
        },
      })

      table.items = data.items;
      table.total = data.total;
    } catch (e) {
      console.log(e)
    } finally {
      fetchTablePending.value = false
    }
  }

  watch([
      () => params.page,
      () => params.perPage,
      () => params.sortBy,
      () => params.sortDesc,
    ],
    debounce(async () => {
      await fetchUsersTable()
    }, 200)
  )

  watch(
    () => filter.entityType,
    () => {
      filter.users = []
    }
  )

  watch([
      () => filter.search,
      () => filter.users,
      () => filter.status,
      () => filter.entityType,
    ],
    debounce(async () => {
      params.page = 1
      await fetchUsersTable()
    }, 200),
    {
      deep: true
    })

  return {
    fetchTablePending,
    table,
    params,
    filter,
    resetFilter,
    fetchUsersTable
  }
}