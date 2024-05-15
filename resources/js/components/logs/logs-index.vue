<script>
import LogsFilter from "./logs-filter.vue";
import LogsTable from "./logs-table.vue";
import swal from "sweetalert2";

export default {
    name: "logs-index",
    components: {LogsTable, LogsFilter},
    data() {
        return {
            pageSetup: window.PAGE_SETUP,
            filter: {
                users: [],
                models: [],
                id: null,
                actions: [],
                date_start: null,
                date_end: null,
            },
            items: [
                // {
                //     user: 'thunderstruck',
                //     date: '2018-08-10 11:09:21',
                //     action: 'action',
                //     model: 'Anketa',
                //     id: 'e738b07b-1b22-429d-9856-c94fbebb75a9',
                //     changes: [
                //         {
                //             name: 'note',
                //             oldValue: '1 водитель, 1 машина (+ одна подменная на всякий случай)ПРМО ПРТО ПЛ факт., ОП Монтажная 8Счет по безналу на вайберЕсть несколько ИП которые по 1000р осмотры, тк договоренность была у них с ВН Шило, теперь новые пришли на таких же условиях.&nbsp;Согласовано с ВН 10% на ПРМО и 10% на ПРТО',
                //             newValue: 'преемник ООО "Интер Лайф 2013'
                //         },
                //         {
                //             name: 'inn',
                //             oldValue: '6732123869',
                //             newValue: '1238902132'
                //         },
                //     ],
                // },
            ],
            page: 1,
            total: 1,
            limit: 100,
        }
    },
    methods: {
        async reload() {
            try {
                const {data} = await axios.post(this.pageSetup.tableDataUrl, {
                    limit: this.limit,
                    page: this.page,
                    filter: {
                        id: this.filter.id ? this.filter.id : null,
                        date_start: this.filter.date_start ? this.filter.date_start : null,
                        date_end: this.filter.date_end ? this.filter.date_end : null,
                        users: this.filter.users.map(item => item.id),
                        models: this.filter.models.map(item => item.id),
                        actions: this.filter.actions.map(item => item.id),
                    },
                })

                this.total = data.total
                this.items = data.items
            } catch (e) {
                swal.fire({
                    title: 'Ошибка',
                    text: 'Ошибка при загрузке данных',
                    icon: 'error'
                });
            }
        },

        async handleApply() {
            await this.reload()
        },

        async handleReset() {
            this.filter = {
                users: [],
                models: [],
                id: null,
                actions: [],
                date_start: null,
                date_end: null,
            }
            await this.reload()
        }
    },
    async mounted() {
        await this.reload();
    },
    watch: {
        page() {
            this.filter = {
                users: [],
                models: [],
                id: null,
                actions: [],
                date_start: null,
                date_end: null,
            }
            this.reload()
        },

        limit() {
            this.reload()
        },
    }
}
</script>

<template>
    <div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <logs-filter
                        :filter.sync="filter"
                        :users-options="pageSetup.usersOption"
                        :models-options="pageSetup.modelsOption"
                        :actions-options="pageSetup.actionsOption"
                        style="margin-bottom: 25px"
                        @apply="handleApply"
                        @reset="handleReset"
                    />

                    <hr>

                    <logs-table
                        :items="items"
                        :page.sync="page"
                        :limit.sync="limit"
                        :total="total"
                        style="margin-top: 25px"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
