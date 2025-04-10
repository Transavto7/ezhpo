import {reactive, ref} from "vue";
import Swal2 from "sweetalert2";
import {fetchRemindersTableItems} from "./api";

export const useReminderLogsTable = () => {
    const fetchTablePending = ref(false)

    const table = reactive({
        items: [],
        total: 0,
    })

    const filter = reactive({
        search: null,
        actions: null,
        cities: null,
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
        sortBy: 'created_at',
        sortDesc: true,
    })

    const fetchRemindersTable = async () => {
        fetchTablePending.value = true

        try {
            const {data} = await fetchRemindersTableItems({
                ...params,
                filters: {
                    search: filter.search,
                    actions: filter.actions?.map((item) => item.id),
                    cities: filter.cities?.map((item) => item.id),
                    points: filter.points?.map((item) => item.id),
                    users: filter.users?.map((item) => item.id),
                    roles: filter.roles?.map((item) => item.id),
                    companies: filter.companies?.map((item) => item.id),
                    subject_type: filter.subject_type?.id,
                    subjects: filter.subjects?.map((item) => item.id),
                },
            })

            table.items = data.items;
            table.total = data.total;
            table.mapList = data.mapList;
        } catch (e) {
            console.log(e)
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
        fetchRemindersTable
    }
}
