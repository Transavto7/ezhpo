<template>
    <div>
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
                        <label class="mb-1" for="contract">Договор</label>
                        <multiselect
                            :multiple="true"
                            v-model="contracts"
                            :options="contracts_options"
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
<!--                            <span slot="noResult">По договорам компании осмотров не проводилось</span>-->
                            <span slot="noOptions">По договорам компании осмотров не проводилось</span>
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
                        <button v-if="permissions.create" type="submit" @click="report" class="btn btn-info" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Сформировать отчет
                        </button>
                        <button v-if="permissions.export" type="submit" @click="exportData" class="btn btn-info" :disabled="loadingExport">
                            <span v-if="loadingExport" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Экспортировать
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
<!--                        <h3 class="mb-0">{{ contract.name }}</h3>-->
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
</template>

<script>
import ReportJournalMedic from "./ReportJournalMedic";
import ReportJournalTech from "./ReportJournalTech";
import ReportJournalTechOther from "./ReportJournalTechOther";
import ReportJournalMedicOther from "./ReportJournalMedicOther";
import ReportJournalOther from "./ReportJournalOther";
import Swal2 from "sweetalert2";
import vSelect from "vue-select";

export default {
    name: "ReportJournalContract",
    props: ['default_company', 'client_company', 'permissions'],
    components: {
        ReportJournalMedic,
        ReportJournalTech,
        ReportJournalTechOther,
        ReportJournalMedicOther,
        vSelect,
        ReportJournalOther
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

            counter: 5,
        }
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
    methods: {
        getTotalContractSum(contract){
            console.log(contract)
            let res = 0;
            // hyli mne pohui, structura dannih by ElliHui
            for (let type_report in contract){
                if(type_report == 'techs' || type_report == 'medics'){
                    for (let human_id in contract[type_report]){
                        for (let type in contract[type_report][human_id].types){
                            if(contract[type_report][human_id].types[type].sum){
                                res += contract[type_report][human_id].types[type].sum
                            }
                        }
                    }
                    // console.log(type_report)
                    // console.log(res)
                    continue;
                }
                if(type_report == 'medics_other' || type_report == 'techs_other'){
                    for (let year in contract[type_report]){
                        for (let human_id in contract[type_report][year].reports){
                            for (let type in contract[type_report][human_id]){
                                if(contract[type_report][human_id].types[type].sum){
                                    res += contract[type_report][human_id].types[type].sum
                                }
                            }
                        }
                    }
                    // console.log(type_report)
                    // console.log(res)
                    continue;
                }

                if(type_report == 'other'){
                    for (let type in contract[type_report]){
                        if(type == 'company'){
                            for (let totall in contract[type_report][type]){
                                res += contract[type_report][type][totall]
                            }
                            // console.log(type)
                            // console.log(type_report)
                            // console.log(contract[type_report][type][totall])
                            continue;
                        }
                        // if(type == 'drivers'){
                            for (let totall in contract[type_report][type]){
                                res += contract[type_report][type][totall].sum
                                // console.log(type)
                                // console.log(type_report)
                                // console.log(contract[type_report][type][totall].sum)
                            }
                        // }
                    }
                }
            }
            return res;
        },
        searchServices() {
            this.loading = true
            this.contracts = [];
            this.contracts_options = [];

            axios.get('/api/reports/getContractsForCompany', {
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

        reset() {
            this.$refs.reportsMedic.hide();
            this.$refs.reportsTech.hide();
            this.$refs.reportsTechOther.hide();
            this.$refs.reportsMedicOther.hide();
            this.$refs.reportsOther.hide();
        },
        async report() {
            this.loading = true;
            let fuckerCounterInAssMazzarettoEbletoTotalCountDickInHerAss = this.contracts.length
            let fuckerCounterInAssMazzarettoEbleto = 0;
            for (let contract_key in this.contracts){

            // this.reset();
                axios.get('/api/reports/contract/journal', {
                    params: {
                        company_id: this.company_id,
                        // contracts_ids: this.contracts.map((item) => item.id),
                        contracts_ids: [this.contracts[contract_key].id],
                        month: this.month
                    }
                }).then(({ data }) => {
                        fuckerCounterInAssMazzarettoEbleto++;
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
                        if(fuckerCounterInAssMazzarettoEbleto === fuckerCounterInAssMazzarettoEbletoTotalCountDickInHerAss){
                            this.loading = false;

                        }
                }).finally(() => {
                });
            }

            // this.loading = false;
        },
        exportData() {
            this.loadingExport = true;
            axios.get('/api/reports/contract/journal/export', {
                params: {
                    company_id: this.company_id,
                    month: this.month
                },
                responseType: 'blob'
            }).then(({ data }) => {
                const url = window.URL.createObjectURL(new Blob([data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'export.xlsx'); //or any other extension
                document.body.appendChild(link);
                link.click();
            }).finally(() => {
                this.loadingExport = false;
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
