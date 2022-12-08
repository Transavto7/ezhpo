<template>
    <div class="">
        <!-- filters -->
        <contract-filters
            v-on:change_filters="changeFilters"
            ref="contractFilters"
            :trash="filters.trash"
            :permissions="permissions"
            v-on:create_new="showCreateModal"
            v-on:view_trash_toggle="toggleTrash"
        >
        </contract-filters>


        <div class="card">

            <div class="card-body pt-0">
                <!-- table -->
                <contract-table
                    v-show="permissions.read"
                    :table="table"
                    :busy="busy"
                    :trash="filters.trash"
                    :permissions="permissions"
                    v-on:change_sort="changeSort"
                    ref="contractTable"
                    v-on:update_data="showCreateModal"
                    v-on:clone_data="showCloneModal"
                    v-on:success="loadData"
                >
                </contract-table>

                <!-- paginator/ -->
                <b-row
                    class="w-100 d-flex justify-content-start" v-if="total > 1 && permissions.read">
                    <!--            <b-col class="my-1 d-flex justify-content-center">-->
                    <b-col class="my-1 d-flex justify-content-start">
                        <b-form-group
                            label-size="sm"
                            class="mb-0"
                        >
                            <b-pagination
                                id="paginate_filters"
                                :total-rows="total"
                                :per-page="mazaretto_yeban"
                                v-model="filters.currentPage"

                                class="my-0"
                                @change="selectPage"
                            />
                        </b-form-group>
                    </b-col>
                </b-row>
                <!-- perPage -->
                <b-row
                    v-if="permissions.read"
                >

                    <b-col md="2" class="my-1">
                        <select v-model="mazaretto_yeban">
                            <option value="20">20</option>
                            <option value="500">500</option>
                            <option value="1500">1500</option>
                            <option value="2000">2000</option>
                            <option value="2500">2500</option>
                        </select>
<!--                        <select-->
<!--                            name=""-->

<!--                            id="per-page-select"-->
<!--                            v-model="filters.perPage"-->
<!--                            :options="pageOptions"-->
<!--                            size="sm"-->
<!--                            @change="selectPerPage"-->
<!--                        >-->

<!--                        </select>-->
<!--                        <b-form-group>-->
<!--                            -->
<!--                            <b-form-select-->
<!--                                id="per-page-select"-->
<!--                                v-model="filters.perPage"-->
<!--                                :options="pageOptions"-->
<!--                                size="sm"-->
<!--                                @change="selectPerPage"-->
<!--                            ></b-form-select>-->
<!--                        </b-form-group>-->
                    </b-col>
                </b-row>
                <p class="text-success">Найдено записей: <b>{{ total }}</b></p>
            </div>
        </div>


        <!-- totalRow -->
        <!--        <b-row class="mb-3"-->
        <!--               v-show="permissions.read"-->
        <!--        >-->
        <!--            <b-col class="my-1 ">-->
        <!--                <p class="text-success">Найдено записей: <b>{{ total }}</b></p>-->
        <!--&lt;!&ndash;                <b-form-group&ndash;&gt;-->
        <!--&lt;!&ndash;                    label-class="font-weight-bold pt-0"&ndash;&gt;-->
        <!--&lt;!&ndash;                    class="mb-0"&ndash;&gt;-->
        <!--&lt;!&ndash;                    disabled&ndash;&gt;-->
        <!--&lt;!&ndash;                >&ndash;&gt;-->
        <!--&lt;!&ndash;                    <b-form-group&ndash;&gt;-->
        <!--&lt;!&ndash;                        label="Всего записей:"&ndash;&gt;-->
        <!--&lt;!&ndash;                        label-for="nested-street"&ndash;&gt;-->
        <!--&lt;!&ndash;                        label-cols="4"&ndash;&gt;-->
        <!--&lt;!&ndash;                        label-cols-sm="3"&ndash;&gt;-->
        <!--&lt;!&ndash;                        content-cols="4"&ndash;&gt;-->
        <!--&lt;!&ndash;                        label-align-sm="right"&ndash;&gt;-->
        <!--&lt;!&ndash;                    >&ndash;&gt;-->
        <!--&lt;!&ndash;                        <b-form-input id="nested-street" v-model="total"></b-form-input>&ndash;&gt;-->
        <!--&lt;!&ndash;                    </b-form-group>&ndash;&gt;-->
        <!--&lt;!&ndash;                </b-form-group>&ndash;&gt;-->

        <!--            </b-col>-->
        <!--        </b-row>-->


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
import {getParams, addParams} from "../const/params";

export default {
    name:       "contract-index",
    components: {
        Swal2,
        ContractCreate,
        ContractFilters,
    },
    props:      ['permissions'],
    data() {
        return {
            // trash: 0,

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
                        key:      "main_for_company",
                        label:    "Главный",
                    },
                    {
                        key:   "services",
                        label: "Услуги",
                    },
                    {
                        key:      "company",
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
                    // {
                    //     key:      "main_for_company",
                    //     sortable: true,
                    //     label:    "Главный",
                    // },
                    {
                        key:      "date_of_start",
                        sortable: true,
                        label:    "Дата начала договора",
                    },
                    {
                        key:      "date_of_end",
                        sortable: true,
                        label:    "Дата окончания договора",
                    },
                    {
                        key:      "created_at",
                        sortable: true,
                        label:    "Дата создания",
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
                // perPage:     500,
                trash:       0,
            },
            total:       0,
            mazaretto_yeban: 20,
            // pageOptions: [15, 100, 500], // da mne pohui
        }
    },
    mounted() {
        let getData = getParams()
        for (let param in getData) {
            if (!isNaN(getData[param])) {
                this.filters[param] = Number(getData[param]);
            } else {
                this.filters[param] = getData[param];
            }
        }
        this.$refs.contractFilters.setFilters(this.filters)


        this.loadData()
    },
    methods: {
        toggleTrash() {
            this.filters.trash = this.filters.trash ? 0 : 1;
            this.loadData();
        },
        selectPage(page = 1) {
            this.filters.currentPage = page;
            this.loadData();
        },
        // selectPerPage(perPage = 15) {
        //     this.filters.perPage = perPage;
        //     this.loadData();
        // },
        changeSort(e) {
            this.filters.sortBy = e.sortBy;
            this.filters.sortDesc = e.sortDesc;
            this.loadData();
        },
        changeFilters(filters) {
            // this.filters.currentPage = 1;

            for (let filter_key in filters) {
                this.filters[filter_key] = filters[filter_key];
            }

            this.loadData();
        },

        showCreateModal(data = null) {
            this.$refs.contractCreate.open(data)
        },

        showCloneModal(data = null) {
            this.$refs.contractCreate.open(data, true)
        },

        loadData() {
            this.busy = true

            // console.log(this.filters)
            addParams(this.filters);

            let data = {
                params: Object.assign({}, this.filters),
            };
            data.params.mazaretto_yeban = this.mazaretto_yeban;

            axios.get("/contract/index", data)
                .then(({data}) => {

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
