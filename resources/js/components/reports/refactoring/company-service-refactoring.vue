<template>
<div class="">
    <div class="">

        <div class="card mb-4" style="overflow-x: inherit">
            <h5 class="card-header">Выбор информации</h5>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-lg-8">
                        <label class="mb-1" for="company">Компании</label>
                        <multiselect
                            :disabled="true"
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
                    <div class="form-group col-lg-2">
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
                <b-row v-if="company">
                    <b-col>
                        <span>
                            Отчёт сформирован по:
                        </span>
                        <b-button
                            variant="danger"
                            :href="'/elements/Company?filter=1&id=' + company.id"
                            target="_blank"
                        >
                            {{ company.name }}
                        </b-button>
                    </b-col>
                </b-row>
            </div>
        </div>


        <div v-for="(contract, index) in contracts">
            <div v-if="contract.visible_result" class="my-3">
                <b-button class="mb-2" v-b-toggle="'collapse-' + index"  variant="primary">{{ contract.name }}</b-button>
                    <b-collapse :id="'collapse-' + index" class="mt-2">
                        <b-card>
                            <ReportJournalMedic
                                :reports="contract.medics"
                                ref="reportsMedic"
                            />
                            <ReportJournalTech
                                class="mt-5"
                                :reports="contract.tech"
                                ref="reportsTech"
                            />
                            <ReportJournalMedicOther
                                class="mt-5"
                                :reports="contract.medics_other"
                                ref="reportsMedicOther"
                            />
                            <ReportJournalTechOther
                                class="mt-5"
                                :reports="contract.techs_other"
                                ref="reportsTechOther"
                            />
                            <ReportJournalOther
                                class="mt-5"
                                :data="contract.other"
                                ref="reportsOther"
                            />
                            <Total
                                ref="total"
                                :data="contract.total"
                            >

                            </Total>
                        </b-card>
                    </b-collapse>
            </div>
        </div>




    </div>
</div>
</template>

<script>

import Swal2 from "sweetalert2";
import ReportJournalTechOther from './ReportJournalTechOther'
import ReportJournalOther from './ReportJournalOther'
import ReportJournalMedicOther from './ReportJournalMedicOther'
import ReportJournalMedic from './ReportJournalMedic'
import ReportJournalTech from './ReportJournalTech'
import Total from "./Total";


export default {
    name: "company-service-refactoring",
    props: ['default_company', 'client_company'],
    components: {
        ReportJournalTechOther,
        ReportJournalOther,
        ReportJournalMedic,
        ReportJournalTech,
        Total,
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

            temporary: [],
        }
    },
    methods:{
        async getReport() {
            this.loading = true;
            let fuckerCounterInAssMazzarettoEbletoTotalCountDickInHerAss = this.contracts.length
            let fuckerCounterInAssMazzarettoEbleto = 0;

            this.contracts.forEach( (contract, contract_key) => {

            // for (let contract_key in this.contracts){

                let data = axios.get('/api/reports/contract/journal_v2', {
                    params: {
                        company_id: this.company_id,
                        contracts_ids: [this.contracts[contract_key].id],
                        month:         this.month
                    }
                }).then(({data}) => {
                    console.log('==========================')
                    console.log(data)
                    // console.log(contract_key)


                    this.contracts[contract_key].total = data;
                    this.contracts[contract_key].medics = data.medics;
                    this.contracts[contract_key].tech = data.techs;
                    this.contracts[contract_key].medics_other = data.medics_other;
                    this.contracts[contract_key].techs_other = data.techs_other;
                    this.contracts[contract_key].other = data.other;

                    // if (data.medics.length === undefined || data.medics.length > 0) {
                    // }else{
                    //     this.contracts[contract_key].medics = [];
                    // }

                    this.contracts[contract_key].visible_result = true;

                    // this.contracts[contract_key].tech = data.techs;
                    // this.contracts[contract_key].medicsOther = data.medics_other;
                    // this.contracts[contract_key].techOther = data.techs_other;
                    // this.contracts[contract_key].other = data.other;
                    // this.$refs.total[contract_key].open(data);

                    // this.$refs.reportsMedic[contract_key].hide();
                    // this.$refs.reportsMedic[contract_key].visible(data.medics);

                    // this.$refs.reportsTech[contract_key].hide();
                    // this.$refs.reportsTech[contract_key].visible(data.techs);
                    //
                    // this.$refs.reportsTechOther[contract_key].hide();
                    // this.$refs.reportsTechOther[contract_key].visible(data.techs_other);
                    //
                    // this.$refs.reportsMedicOther[contract_key].hide();
                    // this.$refs.reportsMedicOther[contract_key].visible(data.medics_other);
                    //
                    // this.$refs.reportsOther[contract_key].hide();
                    // this.$refs.reportsOther[contract_key].visible(data.other);


                    if (data.message.length && contract_key === 0) {
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
            // }

            })
        },
        searchCompany(query = '') {
            axios.get('/api/companies/find', {
                params: {
                    search: query
                }
            }).then(({ data }) => {
                data.forEach(company => {
                    company.name = `[ID:${company.hash_id}][ИНН:${company.inn}] ${company.name}`;
                });

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
        getTotalContractSum(contract){
            let res = 0;
            for (let contract_key in contract){
                if(contract_key === 'other' || contract_key === 'message'){
                    continue;
                }
                res += contract[contract_key].services.price

            }
            console.log(res)
            return res;
        },
    },
    mounted() {
        this.searchCompany();
        const now = new Date();
        const months = now.getMonth() > 9 ? now.getMonth() : '0' + now.getMonth();
        this.month = now.getFullYear() + '-'+ months;

        if (this.client_company) {
            this.companies.push(this.client_company.name);
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
