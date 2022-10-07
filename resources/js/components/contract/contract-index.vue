<template>
    <div class="">
        <contract-filters
            v-on:change_filters="changeFilters"
            ref="contractFilters"
            v-on:create_new="showCreateModal"
        >
        </contract-filters>

        <contract-table
            :table="table"
            :busy="busy"
            v-on:change_sort="changeSort"
            ref="contractTable"
            v-on:update_data="showCreateModal"
            v-on:success="loadData"
        >
        </contract-table>

        <b-row class="w-100 d-flex justify-content-center" v-if="total > 1">
            <b-col class="my-1 d-flex justify-content-center">
                <b-pagination
                    :total-rows="total"
                    :per-page="filters.perPage"
                    v-model="filters.currentPage"
                    class="my-0"
                    @change="selectPage"
                />
            </b-col>
        </b-row>

        <contract-create
            ref="contractCreate"
            v-on:success="loadData"
        >
        </contract-create>

        <!--    <contract-update-->
        <!--        :change_one="changeOne"-->
        <!--        ref="contract-update"-->
        <!--    >-->
        <!--    </contract-update>-->
    </div>
</template>

<script>
import Swal2 from "sweetalert2";
import ContractCreate from "./contract-create";
import ContractFilters from "./contract-filters";

export default {
    name: "contract-index",
    components:  {
        Swal2,
        ContractCreate,
        ContractFilters
    },
    data() {
        return {
            busy: false,
            user: null,

            table: {
                items:  [],
                fields: [
                    {
                        key:      "id",
                        sortable: true,
                        label:    "ID",
                    },
                    {
                        key:      "name",
                        sortable: true,
                        label:    "Название",
                    },
                    {
                        key:      "services",
                        label:    "Услуги",
                    },
                    {
                        key:      "company.name",
                        sortable: true,
                        label:    "Компания",
                    },
                    {
                        key:      "company.inn",
                        sortable: true,
                        label:    "ИНН",
                    },
                    {
                        key:      "our_company.inn",
                        sortable: true,
                        label:    "ИНН",
                    },
                    {
                        key:      "main_for_company",
                        sortable: true,
                        label:    "Главный",
                    },
                    {
                        key:      "date_of_end",
                        sortable: true,
                        label:    "Время окончания договора",
                    },
                    {
                        key:      "buttons",
                        label:    "#",
                        class:    "text-center",
                    },
                ],
            },

            filters: {
                sortBy:      'id',
                sortDesc:    true,
                currentPage: 1,
                perPage: 15,
            },
            total: 0,
        }
    },
    mounted() {

        this.loadData()
        // this.$bvToast.toast('Артикулы были добавлены', {
        //     title:         `Добавление артикулов`,
        //     variant:       'success',
        //     autoHideDelay: 1500,
        //     appendToast:   true,
        //     noCloseButton: true,
        //     solid:         true,
        // });
    },
    methods: {
        selectPage(page) {
            this.filters.currentPage = page;
            this.loadData(page);
        },
        showCreateModal(data = null) {
            // console.log(123)
            this.$refs.contractCreate.open(data)
        },

        loadData() {
            this.busy = true
            let data = {
                params: this.filters,
            };
            console.log(data)
            axios.get("/contract/index", data)
                .then(({data}) => {
                    console.log(data)
                    if (data.status) {
                        this.table.items = data.result.contracts
                        this.total = data.result.total;
                    } else {
                        Swal2.fire({title: "Неизвестная ошибка сервера", icon: "error"});
                    }
                })

                .catch(error => {
                    Swal2.fire({title: "Неизвестная ошибка", icon: "error"});
                }).finally(()=>this.busy = false);
        },

        changeOne() {

        },

        changeFilters() {
            // poka pusto
        },

        changeSort(e) {
            console.log(e)
            this.filters.sortBy = e.sortBy;
            this.filters.sortDesc = e.sortDesc;
            this.loadData();
        },
    },
}
</script>

<style scoped>

</style>
