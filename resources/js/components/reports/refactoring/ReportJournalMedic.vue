<template>
    <div class="report-group">
        <div class="report__title">
            Медосмотры и другие услуги для водителей за выбранный период
            {{ reports.data.length === 0 ? 'не найдены' : '' }}
        </div>
        <div v-if="reports.data.length !== 0">
            <div class="report__item mt-2"
                 v-for="(driver, driver_id) in reports.data"
                 :key="driver_id"
            >
                <div class="report__item-title">
                    <div class="report__name">
                        <span class="text-muted">{{ driver_id || 'Неизвестный водитель' }}</span> {{ driver.driver_fio }}
                    </div>
                    <div class="report__pvs mt-1">{{ driver.pv_id || 'Пункты выпуска не найдены' }}</div>
                </div>

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
                             v-for="(service, service_name) in type.services"
                             :key="service_name"
                        >
                            <div class="report__card-item-name">
                                {{ service_name }}
                            </div>

                            <div class="report__card-item-price">
                                {{ service.price }}₽
                                <span v-if="service.discount">{{ service.discount }}%</span>
                            </div>
                        </div>

                        <div class="report__footer">
                            <span v-b-tooltip.hover title="все / несогласованные">
                                Всего осмотров:
                                {{ type.count || type.total || 0 }}
                                {{ type.count_dop ? '/ ' + type.count_dop : '' }}
                            </span>
                            <span v-if="type.price">Общая стоимость: {{ type.price }}₽</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="report__item mt-2">
                <div class="report__item-title">
                    <div class="report__name">
                        Всего
                        <span v-b-tooltip.hover title="всего водителей">Кол-во: {{ reports.total.drivers_count || 0 }}</span>
                        <span>Стоимость: {{ reports.total.price || 0 }}₽</span>
                    </div>
                </div>

                <div class="report__cards medic">
                    <div class="report__card"
                         v-for="(type, type_name) in reports.total.types"
                    >
                        <div class="report__card-title">
                            {{ getName(type_name) }}
                        </div>

                        <div class="report__card-item" v-for="(service, service_name) in type.services" :key="service_name">
                            <div class="report__card-item-name">
                                {{ service_name }}
                                <span>кол-во: {{ service.count || 0 }}</span>
                            </div>

                            <div class="report__card-item-price">
                                {{ service.price }}₽
                            </div>
                        </div>

                        <div class="report__footer">
                            <span v-b-tooltip.hover title="все / несогласованные">
                                Всего осмотров:
                                {{ type.count || type.total || 0 }}
                                {{ type.count_dop ? '/ ' + type.count_dop : '' }}
                            </span>
                            <span  v-if="type.price">Общая стоимость: {{ type.price }}₽</span>
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
    props:['reports'],
    data() {
        return {
            // reports: false,
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
        getTotalCount(services) {
            return services.reduce((sum, service) => { return sum + service.count }, 0)
        },
        getTotalPrice(services) {
           return services.reduce((sum, service) => {
               let data = this.reports.data
               if(service.type_product === 'Разовые осмотры'){
                   return sum + (service.price * service.count)
               }else
                   if(service.type_product === 'Абонентская оплата'){
                       return Object.keys(data).length * service.price
                   } else
               {
                   return sum + service.price
               }
           }, 0)
        }
    }
}
</script>

<style scoped>

</style>
