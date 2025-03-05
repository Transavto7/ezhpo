import {reactive, ref} from "vue";
import {deleteTariffApi, fetchTariffsTableItems} from "./api";
import Swal2 from "sweetalert2";

export const useTariffsTable = () => {
    const fetchTablePending = ref(false)

    const table = reactive({
        items: [],
        total: 0,
    })

    const filter = reactive({
        search: null,
        roles: null,
        towns: null,
        points: null,
    })

    const resetFilter = () => {
        filter.search = null
        filter.towns = null
        filter.points = null
        filter.roles = null
    }

    const params = reactive({
        page: 1,
        perPage: 100,
        sortBy: 'updated_at',
        sortDesc: true,
    })

    const fetchTariffsTable = async () => {
        fetchTablePending.value = true

        try {
            const {data} = await fetchTariffsTableItems({
                ...params,
                filters: {
                    search: filter.search,
                    roles: filter.roles?.map((item) => item.id),
                    towns: filter.towns?.map((item) => item.id),
                    points: filter.points?.map((item) => item.id),
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

    const performDeleteTariff = async (id) => {
        const result = await Swal2.fire({
            title: 'Вы уверены, что хотите удалить?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Да, удалить!',
            cancelButtonText: 'Отмена',
        });

        if (!result.isConfirmed) {
            return;
        }

        try {
            await deleteTariffApi(id)
            await Swal2.fire('Удалено', 'Данные были успешно удалены', 'success');

            return Promise.resolve()
        } catch (e) {
            await Swal2.fire('Ошибка', e.message || 'Что-то пошло не так', 'warning');

            return Promise.reject()
        }
    };


    return {
        fetchTablePending,
        table,
        params,
        filter,
        resetFilter,
        fetchTariffsTable,
        performDeleteTariff
    }
}
