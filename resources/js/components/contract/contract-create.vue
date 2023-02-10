<template>
    <b-modal
        v-model="show"
        :title="contractData.id ? 'Редактирование Договора' : 'Создание договора'"
        size="lg"
        hide-footer
    >
      <b-form @submit.stop.prevent="submitForm" ref="form">

        <b-form-group
            label="Название"
            label-for="contract_name"
            :state="validationStates.nameState"
            invalid-feedback="Название договора - обязательное поле!"
            class="required my-1">

          <b-form-input required :state="validationStates.nameState" id="contract_name" v-model="contractData.name"
                        placeholder="Введите название"></b-form-input>
        </b-form-group>

        <b-row class="my-1">
          <b-col sm="3">
            <label>Дата начала:</label>
          </b-col>
          <b-col sm="9">
            <b-form-datepicker id="date_of_start-datepicker" v-model="contractData.date_of_start"
                               class="mb-2"></b-form-datepicker>
          </b-col>
        </b-row>
        <b-row class="my-1">
          <b-col sm="3">
            <label>Дата завершения:</label>
          </b-col>
          <b-col sm="9">
            <b-form-datepicker id="date_of_end-datepicker" v-model="contractData.date_of_end"
                               class="mb-2"></b-form-datepicker>
          </b-col>
        </b-row>
        <b-form-group
            class="required my-1"
            label="Компания"
            label-for="company_select"
            :state="validationStates.companyState"
            invalid-feedback="Выберите компанию">

          <multiselect
              required
              :state="validationStates.companyState"
              id="company_select"
              v-model="company_id"
              @select="(company) => contractData.company_id = company.hash_id"
              @search-change="searchCompanies"
              :options="companies"
              :searchable="true"
              :close-on-select="true"
              :show-labels="false"
              placeholder="Выберите компанию"
              label="name"
              track-by="id"
          >
            <span slot="noResult">Результатов не найдено</span>
            <span slot="noOptions">Список пуст</span>
          </multiselect>
        </b-form-group>

        <b-form-group
            class="required my-1"
            label="Наша компания"
            label-for="our_company_select"
            :state="validationStates.ourCompanyState"
            invalid-feedback="Выберите компанию">
            <v-select
                :state="validationStates.ourCompanyState"
                id="our_company_select"
                v-model="contractData.our_company"
                :options="our_companies"
                key="id"
                label="name"
                @search="searchOurCompanies"
            >
            </v-select>
        </b-form-group>

        <b-row class="my-1">
          <b-col sm="3">
            <label>Статус договора:</label>
          </b-col>
          <b-col sm="9">
            <b-form-checkbox
                v-model="contractData.main_for_company"
            >
              Главный
            </b-form-checkbox>
            <b-form-checkbox
                v-model="contractData.finished"
            >
              Завершен
            </b-form-checkbox>
          </b-col>
        </b-row>
        <b-form-group
            label-for="services_select"
            label="Услуги"
            :state="validationStates.servicesState"
            class="required my-1"
            invalid-feedback="Выберите услуги"
        >
            <v-select
                id="services_select"
                :state="validationStates.servicesState"
                v-model="contractData.services"
                :options="products"
                key="id"
                label="name"
                @search="searchServices"
                multiple
            >
            </v-select>
        </b-form-group>
        <b-row class="my-1" v-if="contractData.services">
          <b-col>
            <b-table
                :items="contractData.services"
                :fields="[
                        {
                            label: 'Услуга',
                            key: 'name',
                        },
                        {
                            label: 'Цена',
                            key: 'service_cost',
                        },
                    ]"
            >
              <template #cell(service_cost)="row">
                <template
                    v-if="row.item.pivot"
                >
                  <b-form-input v-model="row.item.pivot.service_cost"></b-form-input>
                </template>
                <template
                    v-else
                >
                  <b-form-input v-model="row.item.price_unit"></b-form-input>
                </template>
              </template>
            </b-table>
          </b-col>
        </b-row>

        <div>
          <b-button
              class="collapsed"
              :aria-expanded="driversCarsVisible ? 'true' : 'false'"
              aria-controls="collapse-driversCarsVisible"
              @click="driversCarsVisible = !driversCarsVisible"
          >
            {{ carsVisible ? 'Скрыть настройки водителей и машин' : 'Раскрыть настройки водителей и машин' }}
          </b-button>
          <b-collapse id="collapse-driversCarsVisible" v-model="driversCarsVisible" class="mt-2">
            <b-card>
              <b-button
                  class="collapsed"
                  :aria-expanded="driversVisible ? 'true' : 'false'"
                  aria-controls="collapse-driversVisible"
                  @click="driversVisible = !driversVisible"
              >
                {{ driversVisible ? 'Скрыть водителей' : 'Раскрыть водителей' }}
              </b-button>
              <b-collapse id="collapse-driversVisible" v-model="driversVisible" class="mt-2">
                <b-card>
                  <b-row class="my-1" v-if="drivers_of_company.length">
                    <b-col>
                      <b-form-checkbox
                          v-model="allDriversSelected"
                      >
                        Выбрать все
                      </b-form-checkbox>
                    </b-col>
                  </b-row>

                  <b-form-group v-slot="{ ariaDescribedby }">
                    <b-form-checkbox-group
                        :aria-describedby="ariaDescribedby"
                        name="flavour-2"
                        ref="drivers_list_of_company"
                        v-model="contractData.drivers"
                    >
                      <b-row>
                        <div class="box">
                          <div v-for="(dataDriver, index) in drivers_of_company">
                            <b-col>
                              <b-form-checkbox
                                  :value="dataDriver.id"
                                  :key="index"
                              >
                                {{ dataDriver.fio }}
                              </b-form-checkbox>
                            </b-col>
                          </div>
                        </div>
                      </b-row>
                    </b-form-checkbox-group>
                  </b-form-group>
                </b-card>
              </b-collapse>
              <b-button
                  class="collapsed"
                  :aria-expanded="carsVisible ? 'true' : 'false'"
                  aria-controls="collapse-carsVisible"
                  @click="carsVisible = !carsVisible"
              >
                {{ carsVisible ? 'Скрыть машины' : 'Раскрыть машины' }}
              </b-button>
              <b-collapse id="collapse-carsVisible" v-model="carsVisible" class="mt-2">
                <b-card>
                  <b-row class="my-1" v-if="cars_of_company.length">
                    <b-col>
                      <b-form-checkbox
                          v-model="allCarsSelected"
                      >
                        Выбрать все
                      </b-form-checkbox>
                    </b-col>
                  </b-row>

                  <b-form-group v-slot="{ ariaDescribedby }">
                    <b-form-checkbox-group
                        :aria-describedby="ariaDescribedby"
                        name="flavour-2"
                        v-model="contractData.cars"
                    >
                      <b-row>
                        <div class="box">
                          <div v-for="(dataCar, index) in cars_of_company">
                            <b-col>
                              <b-form-checkbox
                                  :value="dataCar.id"
                                  :key="index"
                              >
                                {{ dataCar.gos_number }}
                              </b-form-checkbox>
                            </b-col>
                          </div>
                        </div>
                      </b-row>
                    </b-form-checkbox-group>
                  </b-form-group>
                </b-card>
              </b-collapse>
            </b-card>
          </b-collapse>
        </div>

        <b-row class=" my-2">
          <b-col class="text-right">
            <template>
              <b-button variant="success" type="submit">
                Отправить
              </b-button>
            </template>
            <b-button variant="danger" @click="hideModal">Закрыть</b-button>
          </b-col>
        </b-row>

      </b-form>
    </b-modal>
</template>


<script>

import vSelect from "vue-select";
import 'vue-select/dist/vue-select.css';
import Swal2 from "sweetalert2";
import swal from "sweetalert2";

export default {
    name:       "contract-create",
    components: {
        vSelect,
    },
    props:      ['busy'],
    data() {
        return {
          show: false,
          companies: [],
          our_companies: [],
          typeOptions: [],
          products: [],
          validationStates: {
            nameState: null,
            companyState: null,
            ourCompanyState: null,
            servicesState: null,
          },
          contractData: {
            id: null,
            name: null,
            date_of_start: 0,
            date_of_end: 0,
            company: null,
            our_company: null,
            main_for_company: false, // Главный для компании
            finished: false,
            services: [],
            drivers: [],
            cars: [],

            hard_reset_for_car_and_drivers: false,
          },
          oldData: null,

          driversCarsVisible: false,
          driversVisible: false,
          carsVisible: false,

            cars_of_company: [],
            drivers_of_company: [],

            company_id: [],

            allDriversSelected: false,
            allCarsSelected: false,

        }
    },
    watch: {
        company_id(val){
            if(val?.id){
                this.contractData.company = val
            }else{
                this.contractData.company = null
            }

            this.loadDrivers(true)
            this.loadCars(true)
        },
        allDriversSelected(){
            this.contractData.drivers = [];

            if (this.allDriversSelected) {
                for (let driver in this.drivers_of_company) {
                    console.log(this.drivers_of_company[driver])
                    this.contractData.drivers.push(this.drivers_of_company[driver].id);
                }
            }
        },
        allCarsSelected(){
            this.contractData.cars = [];

            if (this.allCarsSelected) {
                for (let car in this.cars_of_company) {
                    this.contractData.cars.push(this.cars_of_company[car].id.toString());
                }
            }
        }
    },
    mounted(loadCars) {
    },
    methods: {
      validationCheck() {
        const valid = this.$refs.form.checkValidity()
        this.validationStates.nameState = valid;
        this.validationStates.companyState = valid;
        this.validationStates.ourCompanyState = valid;
        this.validationStates.servicesState = false;

        if (!this.contractData.company) {
          this.validationStates.companyState = false
          return false;
        }

        if (!this.contractData.our_company) {
          this.validationStates.ourCompanyState = false
          return false;
        }

        if (this.contractData.services.length < 1) {
          this.validationStates.servicesState = false
          return false;
        }

        return valid;
      },

      loadDrivers(is_new = false) {
        if (is_new) {
          // this.contractData.drivers = [];
        }

        this.drivers_of_company = []
        if (this.contractData.company) {
          axios.post(`/contract/getDriversByCompany/` + this.contractData.company.id).then(({data}) => this.drivers_of_company = data)
              .catch(() => Swal2.fire('Ошибка!', '', 'warning'));
        }
      },

      loadCars(is_new = false) {
        if (is_new) {
                // this.contractData.cars = [];
            }
            console.log(this.contractData.company)
            this.cars_of_company = []
            if(this.contractData.company){
                axios.post(`/contract/getCarsByCompany/` + this.contractData.company.id).then(({data}) => this.cars_of_company = data)
                    .catch(() => Swal2.fire('Ошибка!', '', 'warning'));
            }
        },

        hideModal() {
            // this.contractData = this.oldData;
            this.show = false
        },
        async open(data = null, isClone = false) {
            if (!this.companies.length) {
                axios.get(`/v-search/companies`).then(({data}) => this.companies = data)
                    .catch(() => Swal2.fire('Ошибка!', '', 'warning'));
                axios.get(`/v-search/our_companies`).then(({data}) => this.our_companies = data)
                    .catch(() => Swal2.fire('Ошибка!', '', 'warning'));
                axios.get(`/contract/getTypes`)
                    .then(({data}) => {
                        this.typeOptions = data;
                    })
                    .catch(() => Swal2.fire('Ошибка!', '', 'warning'));

                await axios.get(`/v-search/services`)
                    .then(({data}) => this.products = data)
                    .catch(() => Swal2.fire('Ошибка!', '', 'warning'));
            }

            this.allDriversSelected = false;
            this.allCarsSelected = false;

            this.company_id = {}

            this.show = true
            if (data) {
              this.contractData = Object.assign({}, data)

              if (Number(this.contractData.main_for_company)) {
                this.contractData.main_for_company = true
              }

              if (Number(this.contractData.finished)) {
                this.contractData.finished = true
              }

              if (this.contractData.cars.length) {
                this.contractData.cars = this.contractData.cars.map((item) => {
                  return item.id
                })
              }

              if (this.contractData.drivers.length) {
                this.contractData.drivers = this.contractData.drivers.map((item) => {
                  return item.id
                })
              }

              this.company_id = this.contractData.company
              if (isClone) {
                this.contractData.id = null;
              }
            } else {
              this.contractData = {
                drivers: [],
                cars: [],
              }
            }

          this.loadDrivers()
          this.loadCars()
        },

      async submitForm(e) {

        if (!this.validationCheck()) {
          e.preventDefault();
          e.stopPropagation();
          return;
        }

        if (this.contractData.hard_reset_for_car_and_drivers) {
          let res = await swal.fire({
            title: 'Подтвердите действие',
            text: 'Всем водителям и автомобилям компании будет присвоен данный договор',
            confirmButtonText: 'Да',
            confirmButtonColor: '#3085d6',
            showCloseButton: true,
          }).then((modalRes) => {
            return modalRes;
          })
          if (!res.isConfirmed) {
            return;
          }
        }

        e.target.disabled = true
        if (this.contractData.id) {
          axios.post(`/contract/update`, {
            data_to_save: this.contractData,
          })
              .then(({data}) => {
                if (data.status) {
                  this.$emit('success');

                  this.$bvToast.toast('Договор обновлён', {
                    title: `Редактирование договора`,
                    variant: 'success',
                    autoHideDelay: 1500,
                    appendToast: true,
                    noCloseButton: true,
                    solid: true,
                  });
                } else {
                  Swal2.fire('Ошибки!', '', 'warning')
                }
              })
              .catch((err) => {
                Swal2.fire('Ошибкии!', '', 'warning');
                console.log(err)
              })
              .finally(() => {
                e.target.disabled = false
                this.show = false
              });
        } else {
          axios.get(`/contract/create`, {
            params: {
              data_to_save: this.contractData,
            },
          })
              .then(({data}) => {
                if (data.status) {
                  this.$emit('success');

                  this.$bvToast.toast('Договор добавлен', {
                    title: `Добавление договора`,
                    variant: 'success',
                    autoHideDelay: 1500,
                    appendToast: true,
                    noCloseButton: true,
                    solid: true,
                  });
                } else {
                  Swal2.fire('Ошибка!', '', 'warning')
                }
              })
              .catch((err) => {
                Swal2.fire('Ошибка!', '', 'warning');
              })
              .finally(() => {
                e.target.disabled = false
                this.show = false


              });
        }

      },

      searchCompanies(value = '') {
        // loading(true);

        this.companies = [];
        axios
            .get(`/v-search/companies`, {
              params: {
                query: value,
              },
            })
            .then(({data}) => {
              this.companies = data;
              // loading(false);
            })
            .catch((err) => {
              //Ошибка
              Swal2.fire('Ошибка!', '', 'warning');
              // loading(false);
            });
      },
      searchOurCompanies(value, loading) {
        loading(true);

        this.companies = [];
        axios
            .get(`/v-search/our_companies`, {
              params: {
                query: value,
              },
            })
            .then(({data}) => {
              this.our_companies = data;
              loading(false);
            })
            .catch((err) => {
              //Ошибка
              Swal2.fire('Ошибка!', '', 'warning');
              loading(false);
            });
      },

      searchServices(value, loading) {
        loading(true);

        this.products = [];
        axios
            .get(`/v-search/services`, {
              params: {
                query: value,
              },
            })
            .then(({data}) => {
              this.products = data;
              loading(false);
            })
            .catch((err) => {
              //Ошибка
              Swal2.fire('Ошибка!', '', 'warning');
              loading(false);
            });
        },
    },

}
</script>

<style scoped>

</style>
