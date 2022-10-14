<template>
    <div class="">
        <!-- filters -->
        <contract-filters
            v-on:change_filters="changeFilters"
            ref="contractFilters"
            :trash="trash"
            v-on:create_new="showCreateModal"
            v-on:view_trash_toggle="toggleTrash"
        >
        </contract-filters>

        <!-- table -->
        <contract-table
            :table="table"
            :busy="busy"
            :trash="trash"
            v-on:change_sort="changeSort"
            ref="contractTable"
            v-on:update_data="showCreateModal"
            v-on:success="loadData"
        >
        </contract-table>

        <!-- paginator/ -->
        <b-row class="w-100 d-flex justify-content-center" v-if="total > 1">
<!--            <b-col class="my-1 d-flex justify-content-center">-->
            <b-col class="my-1">
                <b-form-group
                    label="Выберите страницу:"
                    label-for="paginate_filters"
                    label-cols-sm="6"
                    label-cols-md="4"
                    label-cols-lg="3"
                    label-align-sm="right"
                    content-cols="4"
                    label-size="sm"
                    class="mb-0"
                >
                    <b-pagination
                        id="paginate_filters"
                        :total-rows="total"
                        :per-page="filters.perPage"
                        v-model="filters.currentPage"
                        class="my-0 mx-2"
                        @change="selectPage"
                    />
                </b-form-group>
            </b-col>
        </b-row>

        <!-- perPage -->
        <b-row>
            <b-col class="my-1">
                <b-form-group
                    label="Количество элементов на странице:"
                    label-for="per-page-select"
                    label-cols-sm="6"
                    label-cols-md="4"
                    label-cols-lg="3"
                    label-align-sm="right"
                    content-cols="4"
                    label-size="sm"
                    class="mb-0"
                >
                    <b-form-select
                        id="per-page-select"
                        v-model="filters.perPage"
                        :options="pageOptions"
                        label-cols="4"
                        size="sm"
                        @change="selectPerPage"
                    ></b-form-select>
                </b-form-group>
            </b-col>
        </b-row>

        <!-- totalRow -->
        <b-row class="mb-3">
            <b-col class="my-1">
                <b-form-group
                    label-class="font-weight-bold pt-0"
                    class="mb-0"
                    disabled
                >
                    <b-form-group
                        label="Всего записей:"
                        label-for="nested-street"
                        label-cols="4"
                        label-cols-sm="3"
                        content-cols="4"
                        label-align-sm="right"
                    >
                        <b-form-input id="nested-street" v-model="total"></b-form-input>
                    </b-form-group>
                </b-form-group>

            </b-col>
        </b-row>


        <!-- create/update contracts -->
        <contract-create
            ref="contractCreate"
            v-on:success="loadData"
        >
        </contract-create>
    </div>
</template>

<script>
import Swal2 from "sweetalert2";
import ContractCreate from "./contract-create";
import ContractFilters from "./contract-filters";

export default {
    name:       "contract-index",
    components: {
        Swal2,
        ContractCreate,
        ContractFilters,
    },
    data() {
        return {
            trash: 0,

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
                        key:   "services",
                        label: "Услуги",
                    },
                    {
                        key:      "company.name",
                        sortable: true,
                        label:    "Компания",
                    },
                    {
                        key:   "company.inn",
                        label: "ИНН",
                    },
                    {
                        key:      "our_company.name",
                        sortable: true,
                        label:    "Наша компания",
                    },
                    {
                        key:   "our_company.inn",
                        label: "ИНН нашей компании",
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
                        key:   "buttons",
                        label: "#",
                        class: "text-center",
                    },
                ],
            },

            filters:     {
                sortBy:      'id',
                sortDesc:    true,
                currentPage: 1,
                perPage:     15,
            },
            total:       0,
            pageOptions: [15, 100, 500],
        }
    },
    mounted() {
        this.loadData()
    },
    methods: {
        toggleTrash() {
            this.trash = this.trash ? 0 : 1;
            this.loadData();
        },
        selectPage(page = 1) {
            this.filters.currentPage = page;
            this.loadData();
        },
        selectPerPage(perPage = 15) {
            this.filters.perPage = perPage;
            this.loadData();
        },
        changeSort(e) {
            this.filters.sortBy = e.sortBy;
            this.filters.sortDesc = e.sortDesc;
            this.loadData();
        },
        changeFilters(filters) {
            this.filters.currentPage = 1;

            for (let filter_key in filters) {
                this.filters[filter_key] = filters[filter_key];
            }

            this.loadData();
        },

        showCreateModal(data = null) {
            this.$refs.contractCreate.open(data)
        },

        loadData() {
            this.busy = true
            let data = {
                params: this.filters,
            };
            data.params.trash = this.trash;

            console.log(data)
            axios.get("/contract/index", data)
                .then(({data}) => {
                    console.log(data)
                    if (data.status) {
                        this.table.items = data.result.contracts
                        this.total = data.result.total;
                        this.filters.currentPage = data.result.currentPage;
                    } else {
                        Swal2.fire({title: "Неизвестная ошибка сервера", icon: "error"});
                    }
                })

                .catch(error => {
                    Swal2.fire({title: "Неизвестная ошибка", icon: "error"});
                }).finally(() => this.busy = false);
        },


    },
}
</script>

<style scoped>

</style>
