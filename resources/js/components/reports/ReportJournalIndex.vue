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
                      <v-select
                          v-model="contracts"
                          :options="contracts_options"
                          key="id"
                          label="name"
                          multiple
                          class="is-invalid"
                      >
                      </v-select>
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

      <template v-for="">

      </template>
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
    name: "ReportJournalIndex",
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
        report() {
            this.reset();
            this.loading = true;
            axios.get('/api/reports/journal', {
                params: {
                    company_id: this.company_id,
                    contracts_ids: this.contracts.map((item) => item.id),
                    month: this.month
                }
            }).then(({ data }) => {
                this.$refs.reportsMedic.visible(data.medics);
                this.$refs.reportsTech.visible(data.techs);
                this.$refs.reportsTechOther.visible(data.techs_other);
                this.$refs.reportsMedicOther.visible(data.medics_other);
                this.$refs.reportsOther.visible(data.other);
            }).finally(() => {
                this.loading = false;
            });
        },
        exportData() {
            this.loadingExport = true;
            axios.get('/api/reports/journal/export', {
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
        company_id(val){
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
