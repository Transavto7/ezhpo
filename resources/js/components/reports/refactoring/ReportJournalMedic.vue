<template>
    <div class="report-group">
        <div class="report__title">
            Медосмотры и другие услуги для водителей за выбранный период
            {{ reports.data.length === 0 ? 'не найдены' : '' }}
        </div>

        <div class="report__item mt-3" v-for="(driver, driver_id) in reports.data" :key="reports.data.length !== 0">
            <div class="report__name">
                <span class="text-muted">{{ driver_id }}</span> {{ driver.driver_fio }}
            </div>
            <div class="report__pvs mt-1">{{ driver.pv_id || 'Пункты выпуска не найдены' }}</div>
            <div class="report__cards">
                <div class="report__card" v-for="(type, type_name) in driver.types" :key="type_name">
                    <div class="report__card-title">
                        {{ getName(type_name) }}
                    </div>

                    <div class="report__card-item" v-for="(service, index) in type.services" :key="index">
                        <div class="report__card-item-name">
                            {{ service.name }}
                        </div>

                        <div class="report__card-item-price">
                            {{ service.price }}
                            <span v-if="service.discount">{{ service.discount }}%</span>
                        </div>
                    </div>

                    <div class="report__footer">
                        <span>Всего осмотров: {{ type.count || type.total || 0 }}</span>
                        <span v-if="type.sum">Общая стоимость: {{ type.sum }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="report__item mt-3">
            <div class="report__name">
                Всего
                <span>Кол-во: {{ reports.services.count || 0 }}</span>
                <span>Стоимость: {{ reports.services.price || 0 }}</span>
            </div>
            <div class="report__cards">
                <div class="report__card" v-for="(services, type_name) in reports.services.services_for_artem">
                    <div class="report__card-title">
                        {{ getName(type_name) }}
                    </div>

                    <div class="report__card-item" v-for="(service, index) in services" :key="index">
                        <div class="report__card-item-name">
                            {{ service.name }}
                            <span>кол-во {{ service.count || 0 }}</span>
                        </div>

                        <div class="report__card-item-price">
                            {{ service.price }}
                            <span v-if="service.discount">{{ service.discount }}%</span>
                        </div>
                    </div>

                    <div class="report__footer">
                        <span>Всего осмотров: {{ services.reduce((sum, service) => { return sum + service.count }, 0) }}</span>
                        <span>Общая стоимость: {{ services.reduce((sum, service) => { return sum + service.count }, 0) }}</span>
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
        }
    }
}
</script>

<style scoped>

</style>
