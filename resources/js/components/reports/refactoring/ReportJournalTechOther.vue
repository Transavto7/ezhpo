<template>
    <div class="report-group">
        <div class="report__title">
            Техосмотры за другие периоды
            {{ reports.data.length === 0 ? 'не найдены' : '' }}
        </div>

        <div v-if="reports.data.length !== 0">
            <div class="report__item mt-3" v-for="(data, year) in reports.data" :key="year">
                <div class="report__item-title">
                    <div class="report__name">
                        <span class="text-muted">{{ data.year }}</span> {{ months[data.month] }}
                    </div>
                </div>
                <div class="card p-2" style="border-radius: 10px">
                    <div class="d-flex" v-for="(car, car_id) in data.reports" :key="car_id">
                        <div class="report__item-title">
                            <div class="report__name">
                                <div>
                                    <span class="text-muted">{{ car_id || 'Неизвестный автомобиль' }}</span>
                                    <span class="text-muted" v-if="car.type_auto">{{ car.type_auto }}</span>
                                </div>
                                {{ car.car_gos_number }}
                            </div>
                            <div class="report__pvs mt-1">{{ car.pv_id || 'Пункты выпуска не найдены' }}</div>
                        </div>
                        <div class="report__cards">
                            <div class="report__card"
                                 v-for="(type, type_name) in car.types"
                                 :key="type_name"
                            >
                                <div class="report__card-title">
                                    {{ getName(type_name) }}
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
                </div>
            </div>

            <div class="report__item mt-2" >
                <div class="report__item-title">
                    <div class="report__name">
                        Всего
                        <span v-b-tooltip.hover title="всего автомобилей">Кол-во: {{ reports.total.cars_count || 0 }}</span>
                        <span>Стоимость: {{ reports.total.price || 0 }}₽</span>
                    </div>
                </div>

                <div class="report__cards">
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
import { months } from "../../const/local";

export default {
    name: "ReportJournalTechOther",
    props:['reports'],
    data() {
        return {
            // reports: false,
            show: false,
            months,
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
        // visible(reports, show = true) {
        //     if (reports.length === undefined || reports.length > 0) {
        //         this.reports = reports;
        //     }
        //     this.show = show;
        // },
        // hide() {
        //     this.reports = false;
        //     this.show = false;
        // }
    }
}
</script>

<style scoped>

</style>
