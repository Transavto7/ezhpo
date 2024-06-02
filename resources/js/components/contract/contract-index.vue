<template>
    <div>
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
            <div class="card-body" v-if="permissions.read">
                <contract-table
                    :table="table"
                    :busy="busy"
                    :trash="filters.trash"
                    :permissions="permissions"
                    ref="contractTable"
                    v-on:logs_read="logsRead"
                    v-on:change_sort="changeSort"
                    v-on:update_data="showCreateModal"
                    v-on:clone_data="showCloneModal"
                    v-on:success="loadData"
                >
                </contract-table>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <b-row class="w-100 d-flex justify-content-start" v-if="total > 1">
                    <b-col class="my-1 d-flex justify-content-start">
                        <b-form-group
                            label-size="sm"
                            class="mb-0"
                        >
                            <b-pagination
                                id="paginate_filters"
                                :total-rows="total"
                                :per-page="perPage"
                                v-model="filters.currentPage"

                                class="my-0"
                                @change="selectPage"
                            />
                        </b-form-group>
                    </b-col>
                </b-row>

                <b-row>
                    <b-col md="2" class="my-1">
                        <select @change="loadData" v-model="perPage">
                            <option value="20">20</option>
                            <option value="500">500</option>
                            <option value="1500">1500</option>
                            <option value="2000">2000</option>
                            <option value="2500">2500</option>
                        </select>
                    </b-col>
                </b-row>

                <p class="text-success">Найдено записей: <b>{{ total }}</b></p>
            </div>
        </div>

        <contract-create
            ref="contractCreate"
            v-on:success="loadData"
        >
        </contract-create>

        <b-modal
            v-model="logsModalShow"
            :title="'Журнал действий'"
            :static="true"
            size="lg"
            hide-footer>
            <logs-modal ref="logsModal">
            </logs-modal>
        </b-modal>
    </div>
</template>

<script>
import Swal2 from "sweetalert2";
import ContractCreate from "./contract-create";
import ContractFilters from "./contract-filters";
import ContractTable from "./contract-table";
import LogsModal from "../logs/logs-modal";
import {getParams, addParams} from "../const/params";

export default {
    name: "contract-index",
    components: {
        Swal2,
        ContractCreate,
        ContractFilters,
        ContractTable,
        LogsModal
    },
    props: ['permissions', 'fields'],
    data() {
        return {
            logsModalShow: false,
            busy: false,
            user: null,
            table: {
                items: [],
                fields: [],
            },
            filters: {
                sortBy: 'id',
                sortDesc: true,
                currentPage: 1,
                trash: 0,
            },
            total: 0,
            perPage: 20,
        }
    },
    mounted() {
        this.fields.forEach(field => {
            this.table.fields.push({
                'key': field.field,
                'label': field.name,
                'sortable': true,
                'thAttr': {
                    'data-toggle': 'tooltip',
                    'data-html': true,
                    'data-trigger': 'hover',
                    'data-placement': 'top',
                    title: field.content,
                }
            });
        });
        this.table.fields.push({key: "buttons", label: "#", class: "text-center"});

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
        logsRead(modelId) {
            this.logsModalShow = true
            this.$refs.logsModal.loadData(modelId)
        },
        toggleTrash() {
            this.filters.trash = this.filters.trash ? 0 : 1;

            this.loadData();
        },
        selectPage(page = 1) {
            this.filters.currentPage = page;

            this.loadData();
        },
        changeSort(e) {
            this.filters.sortBy = e.sortBy;
            this.filters.sortDesc = e.sortDesc;

            this.loadData();
        },
        changeFilters(filters) {
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
            addParams(this.filters);

            let data = {
                params: Object.assign({}, this.filters),
            };
            data.params.perPage = this.perPage;

            axios.get("/contract/index", data)
                .then(({data}) => {
                    if (!data.status) {
                        Swal2.fire({title: "Неизвестная ошибка сервера", icon: "error"});
                        return;
                    }

                    this.table.items = data.result.contracts
                    this.total = data.result.total;
                    this.filters.currentPage = data.result.currentPage;
                })
                .catch(() => Swal2.fire({title: "Неизвестная ошибка", icon: "error"}))
                .finally(() => this.busy = false);
        },
    },
}
</script>

<style scoped>

</style>
