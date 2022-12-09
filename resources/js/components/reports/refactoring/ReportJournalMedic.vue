<template>
    <div class="report-group">
        <div class="report__title">
            Медосмотры и другие услуги для водителей за выбранный период
            {{ reports.data.length === 0 ? 'не найдены' : '' }}
        </div>
        <div v-if="reports.data.length !== 0">
            <div class="report__item mt-3"
                 v-for="(driver, driver_id) in reports.data"
                 :key="driver_id"
            >
                <div class="report__name">
                    <span class="text-muted">{{ driver_id }}</span> {{ driver.driver_fio }}
                </div>
                <div class="report__pvs mt-1">{{ driver.pv_id || 'Пункты выпуска не найдены' }}</div>
                <div class="report__cards medic">
                    <div class="report__card"
                         v-if="type.count > 0 || type.total > 0"
                         v-for="(type, type_name) in driver.types"
                         :key="type_name"
                    >
                        <div class="report__card-title">
                            {{ getName(type_name) }}
                        </div>

                        <div class="report__card-item"
                             v-if="type_name !== 'is_dop'"
                             v-for="(service, index) in type.services"
                             :key="index"
                        >
                            <div class="report__card-item-name">
                                {{ service.name }}
                            </div>

                            <div class="report__card-item-price">
                                {{ service.price }}₽
                                <span v-if="service.discount">{{ service.discount }}%</span>
                            </div>
                        </div>

                        <div class="report__footer">
                            <span>Всего осмотров: {{ type.count || type.total || 0 }}</span>
                            <span v-if="type.sum">Общая стоимость: {{ type.sum }}₽</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="report__item mt-3">
                <div class="report__name ">
                    Всего
                    <span>Кол-во: {{ reports.services.count || 0 }}</span>
                    <span>Стоимость: {{ reports.services.price || 0 }}₽</span>
                </div>
                <div class="report__cards">
                    <div class="report__card medic"
                         v-for="(services, type_name) in reports.services.services_for_artem"
                         v-if="getTotalCount(services) > 0"
                    >
                        <div class="report__card-title">
                            {{ getName(type_name) }}
                        </div>

                        <div class="report__card-item" v-if="type_name !== 'is_dop'" v-for="(service, index) in services" :key="index">
                            <div class="report__card-item-name">
                                {{ service.name }}
                                <span>кол-во: {{ service.count || 0 }}</span>
                            </div>

                            <div class="report__card-item-price">
                                {{ service.price }}₽
                                <span v-if="service.discount">{{ service.discount }}%</span>
                            </div>
                        </div>

                        <div class="report__footer">
                            <span>Всего осмотров: {{ getTotalCount(services) }}</span>
                            <span  v-if="type_name !== 'is_dop'">Общая стоимость: {{ getTotalPrice(services) }}₽</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ReportJournalMedic",
    data() {
        return {
            reports: false,
            show: false,
        }
    },
    methods: {
        getName(key) {
            if (key === 'is_dop') {
                return 'Несогласованные осмотры'
            } else if (key === 'bdd') {
                return 'БДД'
            } else if (key === 'report_cart') {
                return 'Отчёты с карт'
            } else if (key === 'pechat_pl') {
                return 'Печать ПЛ'
            }

            return key;
        },
        visible(reports, show = true) {
            if (reports.length === undefined || reports.length > 0) {
                this.reports = reports;
            }
            this.show = show;
        },
        hide() {
            this.reports = false;
            this.show = false;
        },
        getTotalCount(services) {
            return services.reduce((sum, service) => { return sum + service.count }, 0)
        },
        getTotalPrice(services) {
           return services.reduce((sum, service) => {
               console.log(service)
               if(service.type_product === 'Разовые осмотры'){
                   return sum + (service.price * service.count)
               }else{
                   return sum + service.price
               }
           }, 0)
        }
    }
}
</script>

<style scoped>

</style>
