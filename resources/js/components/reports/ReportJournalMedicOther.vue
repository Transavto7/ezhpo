<template>
    <div>
        <div v-if="data" class="card">
            <h5 class="card-header">Медосмотры и другие услуги для водителей за другие периоды</h5>
            <div class="card-body">
                <table id="reports-table-4" class="table table-responsive">
                    <thead>
                    <tr>
                        <th v-for="(item, name, index) in data" :key="index" style="border-left: 1px solid #e9e9e9;background: #e9e9e9;" class="text-center" colspan="7">
                            {{ months[item.month - 1] }} {{ item.year }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <td v-for="(item, name, index) in data" colspan="7" class="p-0">
                        <table class="w-100 table">
                            <thead>
                            <th class="text-center">Водитель</th>
                            <th class="text-center">Предрейсовый/Предсменный</th>
                            <th class="text-center">Послерейсовый/Послесменный</th>
                            <th class="text-center">Несогласованные осмотры</th>

                            <th class="text-center">БДД</th>
                            <th class="text-center">Отчёты с карт</th>
                            <th class="text-center">Печать ПЛ</th>
                            </thead>

                            <tbody>
                            <tr v-for="(report, name, index) in item.reports">
                                <td class="text-center">
                                    {{ report.driver_fio || 'Неизвестный водитель' }} / {{ name }}
                                </td>
                                <td class="text-center">
                                    {{ getTotal(report, 'Предрейсовый', 'Предсменный') }}

                                    <div class="font-weight-bold" v-if="getSum(report, 'Предрейсовый', 'Предсменный')">
                                        {{ getSum(report, 'Предрейсовый', 'Предсменный') }}₽
                                        <span class="text-red" v-if="getDiscount(report, 'Предрейсовый', 'Предсменный')">
                                            ({{ getDiscount(report, 'Предрейсовый', 'Предсменный') }}%)
                                        </span>
                                        <i v-if="isSync(report, 'Предрейсовый', 'Предсменный')" class="fa fa-refresh text-success"></i>
                                        <i v-else class="fa fa-refresh text-red"></i>
                                    </div>

                                </td>
                                <td class="text-center">
                                    {{ getTotal(report, 'Послерейсовый', 'Послесменный') }}

                                    <div class="font-weight-bold" v-if="getSum(item, 'Послерейсовый', 'Послесменный')">
                                        {{ getSum(report, 'Послерейсовый', 'Послесменный') }}₽
                                        <span class="text-red" v-if="getDiscount(report, 'Послерейсовый', 'Послесменный')">
                                            ({{ getDiscount(report, 'Послерейсовый', 'Послесменный') }}%)
                                        </span>
                                        <i v-if="isSync(report, 'Послерейсовый', 'Послесменный')" class="fa fa-refresh text-success"></i>
                                        <i v-else class="fa fa-refresh text-red"></i>
                                    </div>
                                </td>
                                <td class="text-center">
                                    {{ getTotal(report, 'is_dop') }}
                                </td>

                                <td class="text-center">
                                    {{ getTotal(report, 'bdd') }}

                                    <div class="font-weight-bold" v-if="getSum(report, 'bdd')">
                                        {{ getSum(report, 'bdd') }}₽
                                        <span class="text-red" v-if="getDiscount(report, 'bdd')">
                                            ({{ getDiscount(report, 'bdd') }}%)
                                        </span>
                                        <i v-if="isSync(report, 'bdd')" class="fa fa-refresh text-success"></i>
                                        <i v-else class="fa fa-refresh text-red"></i>
                                    </div>
                                </td>
                                <td class="text-center">
                                    {{ getTotal(report, 'report_cart') }}

                                    <div class="font-weight-bold" v-if="getSum(report, 'report_cart')">
                                        {{ getSum(report, 'report_cart') }}₽
                                        <span class="text-red" v-if="getDiscount(report, 'report_cart')">
                                            ({{ getDiscount(report, 'report_cart') }}%)
                                        </span>
                                        <i v-if="isSync(report, 'report_cart')" class="fa fa-refresh text-success"></i>
                                        <i v-else class="fa fa-refresh text-red"></i>
                                    </div>
                                </td>
                                <td class="text-center">
                                    {{ getTotal(report, 'pechat_pl') }}

                                    <div class="font-weight-bold" v-if="getSum(item, 'pechat_pl')">
                                    {{ getSum(report, 'pechat_pl') }}₽
                                    <span class="text-red" v-if="getDiscount(report, 'pechat_pl')">
                                        ({{ getDiscount(report, 'pechat_pl') }}%)
                                    </span>
                                    <i v-if="isSync(report, 'pechat_pl')" class="fa fa-refresh text-success"></i>
                                    <i v-else class="fa fa-refresh text-red"></i>
                                </div>
                                </td>
                            </tr>
                            <tr v-if="item.reports">
                                <td class="text-center">
                                    <b>Всего</b>
                                </td>
                                <td class="text-center">
                                    {{ getTotalAll(item.reports, 'Предрейсовый', 'Предсменный') }}

                                    <div class="font-weight-bold" v-if="getSumAll(item.reports, 'Предрейсовый', 'Предсменный') != null">
                                        {{ getSumAll(item.reports, 'Предрейсовый', 'Предсменный') }}₽
                                    </div>
                                </td>

                                <td class="text-center">
                                    {{ getTotalAll(item.reports, 'Послерейсовый', 'Послесменный') }}

                                    <div class="font-weight-bold" v-if="getSumAll(reports, 'Послерейсовый', 'Послесменный') != null">
                                        {{ getSumAll(item.reports, 'Послерейсовый', 'Послесменный') }}₽
                                    </div>
                                </td>

                                <td class="text-center">
                                    {{ getTotalAll(item.reports, 'is_dop') }}
                                </td>

                                <td class="text-center">
                                    {{ getTotalAll(item.reports, 'bdd')}}
                                    <div class="font-weight-bold" v-if="getSumAll(item.reports, 'bdd') != null">
                                        {{ getSumAll(item.reports, 'bdd') }}₽
                                    </div>
                                </td>
                                <td class="text-center">
                                    {{ getTotalAll(item.reports, 'report_cart') }}
                                    <div class="font-weight-bold" v-if="getSumAll(item.reports, 'report_cart') != null">
                                        {{ getSumAll(item.reports, 'report_cart') }}₽
                                    </div>
                                </td>

                                <td class="text-center">
                                    {{ getTotalAll(item.reports, 'pechat_pl') }}
                                    <div class="font-weight-bold" v-if="getSumAll(item.reports, 'pechat_pl') != null">
                                        {{ getSumAll(item.reports, 'pechat_pl') }}₽
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-else-if="show" class="alert alert-secondary" role="alert">
            Медосмотры за другие периоды не найдены
        </div>
    </div>
</template>

<script>
import { months } from '../const/local';
import {getTotalAll, getTotal, getSum, getSumAll, getDiscount, isSync} from "../const/reportsAmount";

export default {
    name: "ReportJournalMedicOther",
    data() {
        return {
            data: false,
            show: false,
            months,
        }
    },
    methods: {
        visible(data, show = true) {
            if (data.length === undefined || data.length > 0) {
                this.data = data;
            }
            this.show = true;
        },
        hide() {
            this.data = false;
            this.show = false;
        },
        getTotalAll,
        getTotal,
        getSumAll,
        getSum,
        getDiscount,
        isSync
    }
}
</script>

<style scoped>

</style>
