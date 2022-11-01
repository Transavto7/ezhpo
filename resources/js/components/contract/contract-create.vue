<template>
    <b-modal
        v-model="show"
        title="Создание Договора"
        size="lg"
        hide-footer
    >
        <b-row class="my-1">
            <b-col sm="3">
                <label>Название:</label>
            </b-col>
            <b-col sm="9">
                <b-form-input v-model="contractData.name" placeholder="Введите название"></b-form-input>
            </b-col>
        </b-row>
        <b-row class="my-1">
            <b-col sm="3">
                <label>Дата завершения:</label>
            </b-col>
            <b-col sm="9">
                <b-form-datepicker id="date_of_end-datepicker" v-model="contractData.date_of_end"
                                   class="mb-2"></b-form-datepicker>
                <!--                <b-form-input v-model="contractData.date_of_end"-->
                <!--                              placeholder="Введите длительность действия в днях"></b-form-input>-->
            </b-col>
        </b-row>
        <b-row class="my-1">
            <b-col sm="3">
                <label>Сумма договора:</label>
            </b-col>
            <b-col sm="9">
                <b-form-input v-model="contractData.sum" placeholder="Введите сумму договора"></b-form-input>
            </b-col>
        </b-row>
        <b-row class="my-1">
            <b-col sm="3">
                <label>Компания:</label>
            </b-col>
            <b-col sm="9">
                <v-select
                    v-model="contractData.company"
                    :options="companies"
                    key="id"
                    label="name"
                    @search="searchCompanies"
                >
                </v-select>
            </b-col>
        </b-row>
        <b-row class="my-1">
            <b-col sm="3">
                <label>Наша компания:</label>
            </b-col>
            <b-col sm="9">
                <v-select
                    v-model="contractData.our_company"
                    :options="our_companies"
                    key="id"
                    label="name"
                    @search="searchOurCompanies"
                >
                </v-select>
            </b-col>
        </b-row>
        <!--        <b-row class="my-1">-->
        <!--            <b-col sm="3">-->
        <!--                <label>Статус договора:</label>-->
        <!--            </b-col>-->
        <!--            <b-col sm="9">-->
        <!--                <b-form-checkbox-->
        <!--                    v-model="contractData.main_for_company"-->
        <!--                >-->
        <!--                    Главный-->
        <!--                </b-form-checkbox>-->
        <!--            </b-col>-->
        <!--        </b-row>-->
        <b-row class="my-1">
            <b-col sm="3">
                <label>Услуги:</label>
            </b-col>
            <b-col sm="9">
                <v-select
                    v-model="contractData.services"
                    :options="products"
                    key="id"
                    label="name"
                    @search="searchServices"
                    multiple
                >
                </v-select>
            </b-col>
        </b-row>
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

        <b-row class="text-right my-2">
            <b-col>
                <b-form-checkbox
                    v-model="contractData.hard_reset_for_car_and_drivers"
                >
                    Перезаписать всех водителей и авто
                </b-form-checkbox>
            </b-col>
            <b-col>
                <template
                    v-if="contractData.id"
                >
                    <b-button variant="success" @click="updateContract">
                        Сохранить
                    </b-button>
                </template>

                <template
                    v-else
                >
                    <b-button variant="success" @click="createContract">
                        Создать
                    </b-button>
                </template>

                <b-button variant="danger" @click="hideModal">Закрыть</b-button>
            </b-col>
            <!--                <b-col>-->
            <!--                </b-col>-->
        </b-row>


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
            // v-selects
            companies:     [],
            our_companies: [],
            // cars:        [],
            // drivers:     [],
            typeOptions: [],
            products:    [],

            contractData: {
                id:          null,
                name:        null,
                date_of_end: 0,
                // type: null,
                sum: null,
                // driver:      null,
                // car_id:         null,
                company:     null,
                our_company: null,
                // main_for_company: false, // Главный для компании
                services: [],

                hard_reset_for_car_and_drivers: false,
            },
            oldData:      null,
        }
    },
    mounted() {
    },
    methods: {
        hideModal() {
            // this.contractData = this.oldData;
            this.show = false
        },
        async open(data = null) {
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
                // await axios.get(`/v-search/drivers`)
                //     .then(({data}) => {
                //         this.drivers = data;
                //     })
                //     .catch(() => Swal2.fire('Ошибка!', '', 'warning'));
                // await axios.get(`/v-search/cars`)
                //     .then(({data}) => this.cars = data)
                //     .catch(() => Swal2.fire('Ошибка!', '', 'warning'));
                await axios.get(`/v-search/services`)
                    .then(({data}) => this.products = data)
                    .catch(() => Swal2.fire('Ошибка!', '', 'warning'));
            }

            this.show = true
            if (data) {
                // this.oldData = data
                this.contractData = Object.assign({}, data)
                // if (this.contractData.main_for_company) {
                //     this.contractData.main_for_company = true
                // }
                // moment()
                // this.contractData = data
            } else {
                this.contractData = {}
            }
        },

        async createContract(e) {
            if (this.contractData.hard_reset_for_car_and_drivers) {
                let res = await swal.fire({
                    title:              'Подтвердите действие',
                    text:               'Всем водителям и автомобилям компании будет присвоен данный договор',
                    confirmButtonText:  'Да',
                    confirmButtonColor: '#3085d6',
                    showCloseButton:    true,
                }).then((modalRes) => {
                    return modalRes;
                })
                if(!res.isConfirmed){
                    return;
                }
            }

            e.target.disabled = true
            axios.get(`/contract/create`, {
                params: {
                    data_to_save: this.contractData,
                },
            })
                .then(({data}) => {
                    if (data.status) {
                        this.$emit('success');

                        this.$bvToast.toast('Договор добавлен', {
                            title:         `Добавление договора`,
                            variant:       'success',
                            autoHideDelay: 1500,
                            appendToast:   true,
                            noCloseButton: true,
                            solid:         true,
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
        },
        async updateContract(e) {
            if (this.contractData.hard_reset_for_car_and_drivers) {
                let res = await swal.fire({
                    title:              'Подтвердите действие',
                    text:               'Всем водителям и автомобилям компании будет присвоен данный договор',
                    confirmButtonText:  'Да',
                    confirmButtonColor: '#3085d6',
                    showCloseButton:    true,
                }).then((modalRes) => {
                    return modalRes;
                })
                if(!res.isConfirmed){
                    return;
                }
            }

            e.target.disabled = true
            axios.post(`/contract/update`, {
                data_to_save: this.contractData,
                _method:      'PUT',
            })
                .then(({data}) => {
                    if (data.status) {
                        this.$emit('success');

                        this.$bvToast.toast('Договор обновлён', {
                            title:         `Редактирование договора`,
                            variant:       'success',
                            autoHideDelay: 1500,
                            appendToast:   true,
                            noCloseButton: true,
                            solid:         true,
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
        },

        searchCompanies(value, loading) {
            loading(true);

            this.companies = [];
            axios
                .get(`/v-search/companies`, {
                    params: {
                        query: value,
                    },
                })
                .then(({data}) => {
                    this.companies = data;
                    loading(false);
                })
                .catch((err) => {
                    //Ошибка
                    Swal2.fire('Ошибка!', '', 'warning');
                    loading(false);
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

        // searchCars(value, loading) {
        //     loading(true);
        //
        //     this.cars = [];
        //     axios
        //         .get(`/v-search/cars`, {
        //             params: {
        //                 query: value,
        //             },
        //         })
        //         .then(({data}) => {
        //             this.cars = data;
        //             loading(false);
        //         })
        //         .catch((err) => {
        //             //Ошибка
        //             Swal2.fire('Ошибка!', '', 'warning');
        //             loading(false);
        //         });
        // },
        //
        // searchDrivers(value, loading) {
        //     loading(true);
        //
        //     this.drivers = [];
        //     axios
        //         .get(`/v-search/drivers`, {
        //             params: {
        //                 query: value,
        //             },
        //         })
        //         .then(({data}) => {
        //             this.drivers = data;
        //             loading(false);
        //         })
        //         .catch((err) => {
        //             //Ошибка
        //             Swal2.fire('Ошибка!', '', 'warning');
        //             loading(false);
        //         });
        // },

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

    watch: {
        // contractData(val){
        //     if(this.contractData.id){
        //         let newValue = null
        //         console.log(this.contractData.services)
        //         this.contractData.services = this.contractData.services.filter((item) => {
        //             console.log(item)
        //             if(!item.pivot){
        //                 console.log('=NAHUI')
        //                 newValue = {
        //                     id: item.id,
        //                     name: item.name,
        //                     pivot: {
        //                         service_id: item.id,
        //                         contract_id: this.contractData.id,
        //                         service_cost: item.price_unit
        //                     },
        //                 }
        //                 return false;
        //             }
        //             return true;
        //         });
        //         console.log(this.contractData.services)
        //         console.log(newValue)
        //         if(newValue){
        //             this.contractData.services = this.contractData.services.push(newValue)
        //         }
        //     }
        // }
    },
}
</script>

<style scoped>

</style>
