<template>
    <div class="report-group">
        <div class="report__title">
            Медосмотры и другие услуги для водителей за другие периоды
            {{ reports.data.length === 0 ? 'не найдены' : '' }}
        </div>

        <div class="report__item mt-3" v-for="(data, year) in reports.data" :key="year" v-if="reports.data.length !== 0">
            <div class="report__name">
                <span class="text-muted">{{ data.year }}</span> {{ months[data.month] }}
            </div>
            <div class="card p-2" style="border-radius: 10px">
                <div v-for="(driver, driver_id) in data.reports" :key="driver_id">
                    <div class="report__item-title">
                        <div class="report__name">
                            <span class="text-muted">{{ driver_id || 'Неизвестный водитель' }}</span> {{ driver.driver_fio }}
                        </div>
                        <div class="report__pvs mt-1">{{ driver.pv_id || 'Пункты выпуска не найдены' }}</div>
                    </div>
                    <div class="report__cards">
                        <div class="report__card"
                             v-for="(type, type_name) in driver.types"
                             :key="type_name"
                             v-if="type_name !== 'medic'"
                        >
                            <div class="report__card-title">
                                {{ getName(type_name) }}
                            </div>

                            <div class="report__footer">
                                <span>Всего осмотров: {{ type.count || type.total || 0 }}₽</span>
                            </div>
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
    name: "ReportJournalMedicOther",
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
