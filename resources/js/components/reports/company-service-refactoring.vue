<template>
<div class="">
    <div class="">

        <div class="card mb-4" style="overflow-x: inherit">
            <h5 class="card-header">Выбор информации</h5>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-lg-3">
                        <label class="mb-1" for="company">Компании</label>
                        <multiselect
                            :disabled="client"
                            v-model="company"
                            @search-change="searchCompany"
                            @select="(company) => company_id = company.hash_id"
                            :options="companies"
                            :searchable="true"
                            :close-on-select="true"
                            :show-labels="false"
                            placeholder="Выберите компанию"
                            label="name"
                            class="is-invalid"
                        >
                            <span slot="noResult">Результатов не найдено</span>
                            <span slot="noOptions">Список пуст</span>
                        </multiselect>
                    </div>
                    <div class="form-group col-lg-3">
                        <label class="mb-1" for="company">Договор</label>
                        <multiselect
                            v-model="contracts"
                            :options="contracts_options"
                            :multiple="true"
                            :close-on-select="false"
                            :clear-on-select="false"
                            :preserve-search="true"
                            placeholder="Выберите договор"
                            label="name"
                            track-by="name"
                            :preselect-first="true"
                            selectLabel="Enter чтобы выбрать"
                            deselectLabel="Enter чтобы отменить"
                            selectedLabel="Выбрано"
                        >
                            <span slot="noResult">Результатов не найдено</span>
                        </multiselect>
                    </div>
                    <div class="form-group col-lg-2">
                        <label class="mb-1" for="date_from">Период:</label>
                        <input type="month" required ref="month" v-model="month"
                               id="month" class="form-control form-date" name="month">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-12">
                        <button v-if="permissions.create" type="submit" @click="getReport" class="btn btn-info" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Сформировать отчет
                        </button>
                        <a href="?" class="btn btn-danger">Сбросить</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" v-for="(contract, index) in result">
            <div class="card-header">
                <b-button  v-b-toggle="'collapse-' + index" variant="primary">{{ contract.name }}</b-button>
            </div>

            <b-collapse :id="'collapse-' + index" class="mt-2">
                <b-card v-for="inspection_group in contract.inspections">
                    <div class="card">
                        <div class="card-header">
                            {{ inspection_group.name }}
                        </div>
                        <div class="card-body" v-for="car_or_driver in inspection_group.data">
                            <b-row>
                                <b-col cols="3">
                                    <h4>{{ car_or_driver.gos_number || car_or_driver.fio }}</h4>
                                </b-col>
                                <b-col cols="9">
                                    <b-row>
                                        <b-col cols="4" v-for="type in car_or_driver.types">
                                            <div class="card">
                                                <div class="card-header">
                                                    {{ type.name }}
                                                </div>
                                                <div class="card-body">
                                                    <p>Количество: {{ type.count }}</p>
                                                    <b-table
                                                        hover
                                                        bordered
                                                        :items="type.services"
                                                        :fields="[{
                                                            key: 'name',
                                                            label: 'Название'
                                                        },{
                                                            key: 'price',
                                                            label: 'Цена'
                                                        }]"
                                                    >
                                                        <template #cell(price)="row">
                                                            {{ row.value }} <span class="text-red">({{ row.item.discount }})%</span>
                                                        </template>
                                                    </b-table>
                                                </div>
                                            </div>
                                        </b-col>
                                    </b-row>
                                </b-col>
                            </b-row>
                        </div>
                    </div>

                </b-card>
            </b-collapse>

        </div>

    </div>
</div>
</template>

<script>

import Swal2 from "sweetalert2";

export default {
    name: "company-service-refactoring",

    data() {
        return {
            loading: false,
            loadingExport: false,
            company: null,
            client: false,
            companies: [],
            month: null,
            company_id: 0,

            contracts: [],
            contracts_options: [],

            permissions: {
                export: true,
                create: true,

            },

            result: [],
        }
    },
    methods:{
        async getReport() {
            this.loading = true;

            await axios.get('/report/contract/journal_v2', {
                params: {
                    company_id: this.company_id,
                    contracts_id: this.contracts.map((contract) => {
                        return contract.id
                    }),
                    month: this.month
                }
            }).then(({ data }) => {
                if(data.status){
                    this.result = data.result
                }else{
                    Swal2.fire({
                        icon: 'error',
                        text: 'Ошибка!'
                    })
                }
            }).finally(() => {
                this.loading = false;
            });
        },
        searchCompany(query = '') {
            axios.get('/api/companies/find', {
                params: {
                    search: query
                }
            }).then(({ data }) => {
                this.companies = data;
            });
        },
        searchServices() {
            this.loading = true
            this.contracts = [];
            this.contracts_options = [];

            axios.get('/report/getContractsForCompany_v2', {
                params: {
                    id: this.company_id
                }
            }).then(({ data }) => {
                this.contracts = data
                this.contracts_options = data
            }).finally(() => {
                this.loading = false
            });
        },
    },
    mounted() {
        this.searchCompany();
        const now = new Date();
        const months = now.getMonth() > 9 ? now.getMonth() : '0' + now.getMonth();
        this.month = now.getFullYear() + '-'+ months;

        if (this.client_company) {
            this.companies.push(this.client_company);
            this.company = this.client_company;
            this.company_id = this.client_company.hash_id;
            this.client = true;
        } else if (this.default_company) {
            this.companies.push(this.default_company);
            this.company = this.default_company;
            this.company_id = this.default_company.hash_id;

            this.report();
        }
    },
    watch:{
        company(val){
            if(this.company_id == 0){
                this.loading = true
                return;
            }

            this.searchServices()
        }
    }
}
</script>

<style scoped>

</style>
