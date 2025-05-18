import {reactive, ref} from "vue";
import {fetchNotificationLogsTableItems} from "@/widgets/notifications/notification-logs-list-widget/api";

export const useNotificationLogsTable = () => {
    const fetchTablePending = ref(false)

    const table = reactive({
        items: [],
        total: 0,
    })

    const filter = reactive({
        search: null,
        notifications: null,
        actions: null,
        users: null,
    })

    const resetFilter = () => {
        filter.search = null
        filter.notifications = []
        filter.actions = []
        filter.users = []
    }

    const params = reactive({
        page: 1,
        perPage: 100,
        sortBy: 'created_at',
        sortDesc: true,
    })

    const fetchNotificationsLogsTable = async () => {
        fetchTablePending.value = true

        try {
            const {data} = await fetchNotificationLogsTableItems({
                ...params,
                filters: {
                    search: filter.search,
                    notifications: filter.notifications?.map((item) => item.id) ?? [],
                    actions: filter.actions?.map((item) => item.id) ?? [],
                    users: filter.users?.map((item) => item.id) ?? [],
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
        fetchNotificationsLogsTable
    }
}
