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

        <div v-for="(contract, index) in contracts">
            <b-card-group v-show="contract.visible_result" class="my-3">
                <b-card :header="contract.name">
                    <template #header>
                        <b-button  v-b-toggle="'collapse-' + index"  variant="primary">{{ contract.name }}</b-button>
                    </template>
                    <b-collapse :id="'collapse-' + index" class="mt-2">
                        <b-card>
                            <b-card-text>
                                <ReportJournalMedic
                                    ref="reportsMedic"
                                />

                                <ReportJournalTech
                                    ref="reportsTech"
                                />

                                <ReportJournalMedicOther
                                    ref="reportsMedicOther"
                                />

                                <ReportJournalTechOther
                                    ref="reportsTechOther"
                                />

                                <ReportJournalOther
                                    ref="reportsOther"
                                />
                            </b-card-text>
                        </b-card>
                        <b-row>
                            <b-col class="text-right">
                                <p>
                                    Итого по договору: {{ contract.sum }}
                                </p>
                            </b-col>
                        </b-row>
                    </b-collapse>
                </b-card>
                <!--            <div class="card">-->
                <!--                <h5 class="card-header">{{ contracts[i].name }}</h5>-->
                <!--            </div>-->
                <!--            <div class="card-body">-->
                <!--            </div>-->
            </b-card-group>
        </div>

    </div>
</div>
</template>

<script>

import Swal2 from "sweetalert2";
import ReportJournalTechOther from '../journal/ReportJournalOther'
import ReportJournalOther from '../journal/ReportJournalOther'
import ReportJournalMedicOther from '../journal/ReportJournalMedicOther'
import ReportJournalMedic from './ReportJournalMedic'
import ReportJournalTech from './ReportJournalTech'
export default {
    name: "company-service-refactoring",

    components: {
        ReportJournalTechOther,
        ReportJournalOther,
        ReportJournalMedic,
        ReportJournalTech,
        ReportJournalMedicOther
    },

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
            let fuckerCounterInAssMazzarettoEbletoTotalCountDickInHerAss = this.contracts.length
            let fuckerCounterInAssMazzarettoEbleto = 0;
            for (let contract_key in this.contracts) {

                // this.reset();
                axios.get('/api/reports/contract/journal_v2', {
                    params: {
                        company_id: this.company_id,
                        // contracts_ids: this.contracts.map((item) => item.id),
                        contracts_ids: [this.contracts[contract_key].id],
                        month:         this.month
                    }
                }).then(({data}) => {
                    this.contracts[contract_key].visible_result = true;
                    this.contracts[contract_key].sum = this.getTotalContractSum(data);

                    this.$refs.reportsMedic[contract_key].hide();
                    this.$refs.reportsMedic[contract_key].visible(data.medics);

                    this.$refs.reportsTech[contract_key].hide();
                    this.$refs.reportsTech[contract_key].visible(data.techs);

                    this.$refs.reportsTechOther[contract_key].hide();
                    this.$refs.reportsTechOther[contract_key].visible(data.techs_other);

                    this.$refs.reportsMedicOther[contract_key].hide();
                    this.$refs.reportsMedicOther[contract_key].visible(data.medics_other);

                    this.$refs.reportsOther[contract_key].hide();
                    this.$refs.reportsOther[contract_key].visible(data.other);

                    if (data.message.length) {
                        Swal2.fire({
                            icon:  'error',
                            title: 'Упсс...',
                            text:  data.message,
                        })
                    }
                }).finally(() => {
                    fuckerCounterInAssMazzarettoEbleto++;
                    if (fuckerCounterInAssMazzarettoEbleto === fuckerCounterInAssMazzarettoEbletoTotalCountDickInHerAss) {
                        this.loading = false;
                    }
                });
            }
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
