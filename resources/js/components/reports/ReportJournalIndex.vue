<template>
  <div>
      <div class="card mb-4" style="overflow-x: inherit">
          <h5 class="card-header">Выбор информации</h5>
          <div class="card-body">
              <div class="row">
                  <div class="form-group col-lg-3">
                      <label class="mb-1" for="company">Компании</label>
                      <multiselect
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
                      <label class="mb-1" for="date_from">Дата с:</label>
                      <input type="date" required ref="date_from" v-model="date_from"
                             id="date_from" class="form-control form-date" name="date_from">
                  </div>
                  <div class="form-group col-lg-2">
                      <label class="mb-1" for="date_to">Дата по:</label>
                      <input type="date" required v-model="date_to" id="date_to" class="form-control form-date" name="date_to">
                  </div>
              </div>
              <div class="row">
                  <div class="form-group col-lg-12">
                      <button type="submit" @click="report" class="btn btn-info" :disabled="loading">
                          <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                          Сформировать отчет
                      </button>
                      <a href="?" class="btn btn-danger">Сбросить</a>
                  </div>
              </div>
          </div>
      </div>

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

      <ReportJournalPL
        ref="reportsPL"
      />
  </div>
</template>

<script>
import ReportJournalMedic from "./ReportJournalMedic";
import ReportJournalTech from "./ReportJournalTech";
import ReportJournalTechOther from "./ReportJournalTechOther";
import ReportJournalMedicOther from "./ReportJournalMedicOther";
import ReportJournalPL from "./ReportJournalPL";

export default {
    name: "ReportJournalIndex",
    props: ['default_company'],
    components: {
        ReportJournalMedic,
        ReportJournalTech,
        ReportJournalTechOther,
        ReportJournalMedicOther,
        ReportJournalPL
    },
    data() {
        return {
            loading: false,
            company: null,
            companies: [],
            date_to: null,
            date_from: null,
            company_id: 0,
        }
    },
    mounted() {
        this.searchCompany();

        if (this.default_company) {
            this.companies.push(this.default_company);
            this.company = this.default_company;
            this.company_id = this.default_company.id;
        }

        const now = new Date();
        const days = now.getDate() > 9 ? now.getDate() : '0' + now.getDate();
        const months = now.getMonth() > 9 ? now.getMonth() : '0' + now.getMonth();
        this.date_from = now.getFullYear() + '-'+ months + '-' + '01';

        this.date_to = now.getFullYear() + '-' + months + '-' +
            new Date(now.getFullYear(), now.getMonth(), 0).getDate();

    },
    methods: {
        reset() {
            this.$refs.reportsMedic.hide();
            this.$refs.reportsTech.hide();
            this.$refs.reportsTechOther.hide();
            this.$refs.reportsMedicOther.hide();
            this.$refs.reportsPL.hide();
        },
        report() {
            this.reset();
            this.loading = true;
            axios.get('/api/reports/journal', {
                params: {
                    company_id: this.company_id,
                    date_to: this.date_to,
                    date_from: this.date_from
                }
            }).then(({ data }) => {
                this.$refs.reportsMedic.visible(data.medics);
                this.$refs.reportsTech.visible(data.techs);
                this.$refs.reportsTechOther.visible(data.techs_other);
                this.$refs.reportsMedicOther.visible(data.medics_other);
                this.$refs.reportsPL.visible(data.other_pl);
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
    }
}
</script>

<style scoped>

</style>
