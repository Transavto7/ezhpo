<template>
    <div class="my-2">
        <b-row>
            <b-col class="d-flex align-items-center col-lg-3">
                <b-button class="m-1" variant="success" @click="$emit('create_new')">Создать</b-button>
                <b-button class="m-1" variant="warning"
                          @click="$emit('view_trash_toggle')"
                          v-if="permissions.trash && permissions.read"
                >
                    {{ trash ? 'Назад' : 'Корзина' }}
                    <i v-if="!trash" class="fa fa-trash"></i>
                </b-button>
            </b-col>
        </b-row>

        <div class="card" style="overflow-x: inherit"
             v-if="permissions.read"
        >
            <div class="card-body">
                <div class="row">
                    <div class="d-flex align-items-center col-lg-3">
                        <b-form-group label="ID договора" v-slot="{ ariaDescribedby }" class="w-100">
                            <b-form-input
                                v-model="filters.id"
                                placeholder="Введите ID договора"
                            ></b-form-input>
                        </b-form-group>
                    </div>
                    <div class="d-flex align-items-center col-lg-3">
                        <b-form-group label="Название" v-slot="{ ariaDescribedby }" class="w-100">
                            <b-form-input
                                v-model="filters.name"
                                placeholder="Введите название"
                            ></b-form-input>
                        </b-form-group>
                    </div>
                    <div class="d-flex align-items-center col-lg-3">
                        <b-form-group label="Услуги" v-slot="{ ariaDescribedby }" class="w-100">
                            <v-select
                                v-model="filters.service_id"
                                :options="services"
                                key="id"
                                label="name"
                                @search="searchServices"
                                placeholder="Выберите услуги"
                                :reduce="item => item.id"
                            >
                            </v-select>
                        </b-form-group>
                    </div>

                    <div class="d-flex align-items-center col-lg-3">
                        <b-form-group label="Компания" v-slot="{ ariaDescribedby }" class="w-100">
                            <v-select
                                v-model="filters.company_id"
                                :options="companies"
                                @search="searchCompanies"
                                placeholder="Выберите компанию"
                                :reduce="companies => companies.id"
                                label="name"
                            >
                            </v-select>
                        </b-form-group>
                    </div>
                    <div class="d-flex align-items-center col-lg-3">
                        <b-form-group label="Выберите нашу компанию" v-slot="{ ariaDescribedby }" class="w-100">
                            <v-select
                                v-model="filters.our_company_id"
                                :options="our_companies"
                                key="id"
                                label="name"
                                @search="searchOurCompanies"
                                placeholder="Выберите нашу компанию"
                                :reduce="our_companies => our_companies.id"
                            >
                            </v-select>
                        </b-form-group>
                    </div>
<!--                    <div class="d-flex align-items-center col-lg-3">-->
<!--                        <b-form-group label="Главный" v-slot="{ ariaDescribedby }" class="w-100">-->
<!--                            <b-form-radio v-model="filters.main_for_company" :aria-describedby="ariaDescribedby"-->
<!--                                          name="some-radios"-->
<!--                                          :value="null">Все-->
<!--                            </b-form-radio>-->
<!--                            <b-form-radio v-model="filters.main_for_company" :aria-describedby="ariaDescribedby"-->
<!--                                          name="some-radios"-->
<!--                                          :value="1">Главный-->
<!--                            </b-form-radio>-->
<!--                            <b-form-radio v-model="filters.main_for_company" :aria-describedby="ariaDescribedby"-->
<!--                                          name="some-radios"-->
<!--                                          :value="0">Не главный-->
<!--                            </b-form-radio>-->
<!--                        </b-form-group>-->
<!--                    </div>-->
                    <div class="d-flex align-items-center col-lg-3">
                        <b-form-group label="Дата окончания договора от" v-slot="{ ariaDescribedby }" class="w-100">
                            <b-form-datepicker id="date_of_end_start" v-model="filters.date_of_end_start"
                                               placeholder="Укажите дату"
                                               reset-button
                                               close-button
                                               label-reset-button="Сбросить"
                                               label-close-button="Закрыть"
                                               class="mb-2"></b-form-datepicker>
                        </b-form-group>
                    </div>
                    <div class="d-flex align-items-center col-lg-3">
                        <b-form-group label="Дата окончания договора до" v-slot="{ ariaDescribedby }" class="w-100">
                            <b-form-datepicker id="date_of_end_end" v-model="filters.date_of_end_end"
                                               placeholder="Укажите дату"
                                               reset-button
                                               close-button
                                               label-reset-button="Сбросить"
                                               label-close-button="Закрыть"
                                               class="mb-2"></b-form-datepicker>
                        </b-form-group>
                    </div>
                    <div class="d-flex align-items-center col-lg-3">
                        <b-form-group label="Проверить главный договор" v-slot="{ ariaDescribedby }" class="w-100">
                            <b-form-datepicker id="date_check_main" v-model="filters.date_check_main"
                                               placeholder="Укажите дату"
                                               reset-button
                                               close-button
                                               label-reset-button="Сбросить"
                                               label-close-button="Закрыть"
                                               class="mb-2"></b-form-datepicker>
                        </b-form-group>
                    </div>
                    <div class="d-flex align-items-center col-lg-3">
                        <b-button
                            variant="info"
                            @click="search"
                            class="m-1"
                        >
                            Поиск
                        </b-button>
                        <b-button
                            variant="danger"
                            @click="reset_filters"
                            class="m-1"
                        >
                            Сброс фильтров
                        </b-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import vSelect from "vue-select";
import 'vue-select/dist/vue-select.css';
import Swal2 from "sweetalert2";

export default {
    name: "contract-filters",
    props: ['trash', 'permissions'],
    components: {
        vSelect,
        Swal2,
    },

    data() {
        return {
            filters: {
                date_of_end_end: null,
                date_of_end_start: null,
                // main_for_company: null,
                our_company_id: null,
                company_id: null,
                id: null,
                name: null,
                service_id: null,
            },

            services:      [],
            companies:     [],
            our_companies: [],
        }
    },
    mounted() {
        axios.get(`/v-search/companies`).then(({data}) => this.companies = data)
            .catch(() => Swal2.fire('Ошибка!', '', 'warning'));
        axios.get(`/v-search/our_companies`).then(({data}) => this.our_companies = data)
            .catch(() => Swal2.fire('Ошибка!', '', 'warning'));
        axios.get(`/v-search/services`)
            .then(({data}) => this.services = data)
            .catch(() => Swal2.fire('Ошибка!', '', 'warning'));
    },
    methods: {
        search() {
            this.$emit('change_filters', this.filters);
        },
        reset_filters() {
            this.filters = {
                date_of_end_end: null,
                date_of_end_start: null,
                date_check_main: null,
                // main_for_company: null,
                our_company_id: null,
                company_id: null,
                id: null,
                name: null,
                service_id: null,
            };
            this.$emit('change_filters', this.filters);
        },
        setFilters(newFilters){
            for(let param in newFilters){
                this.filters[param] = newFilters[param]
            }
        },
        searchCompanies(value, loading) {
            loading(true);

            // this.companies = [];
            // this.companies.fil
            axios
                .get(`/v-search/companies`, {
                    params: {
                        query: value,
                    },
                })
                .then(({data}) => {
                    data.map((item) => {
                        if (
                            this.filters.company_id !== item.id
                            && !this.companies.filter((company_option) => {
                                return company_option.id == item.id
                            }).length
                        ) {
                            this.companies.push(item);
                        }
                    })
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

            axios.get(`/v-search/our_companies`, {
                params: {
                    query: value,
                },
            })
                .then(({data}) => {
                    data.map((item) => {
                        if (
                            this.filters.our_company_id !== item.id
                            && !this.our_companies.filter((our_company_option) => {
                                return our_company_option.id == item.id
                            }).length
                        ) {
                            this.our_companies.push(item);
                        }
                    })
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

            axios.get(`/v-search/services`, {
                params: {
                    query: value,
                },
            })
                .then(({data}) => {
                    data.map((item) => {
                        if (
                            this.filters.service_id !== item.id
                            && !this.services.filter((service_option) => {
                                return service_option.id == item.id
                            }).length
                        ) {
                            this.services.push(item);
                        }
                    })
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
